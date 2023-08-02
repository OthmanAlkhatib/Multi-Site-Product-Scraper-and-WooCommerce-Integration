from rest_framework import status
from rest_framework.views import APIView
from django.http import JsonResponse
from .scrap import getDetails, startDriver, quitDriver
from time import sleep
from threading import Thread
from requests import get
import logging

logging.basicConfig(filename='scrap.log', level=logging.INFO, format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')

def check_is_site(url):
    websites = ['desertcart', 'amazon', 'medcart', 'alibaba', 'noon', 'aliexpress']
    for website in websites:
        if website in url:
            return True
    return False

req = 0
thread = None
is_thread_running = False
def check_last_request():
    logging.info("--- Running Check Last Request")
    global thread
    global is_thread_running
    global req
    while is_thread_running:
        req += 10
        if req >= 180:
            logging.info("--- No Requests For 3 Minutes")
            is_thread_running = False
        sleep(10)
    logging.info("--- Quit Driver")
    quitDriver()

def start_check_request():
    global thread
    thread = Thread(target=check_last_request, args=())
    thread.daemon = True
    thread.start()


sleep_duration = 3600
def check_products():
    logging.info("== Running Check Products")
    global sleep_duration
    global req
    global is_thread_running
    while True:
        req = 0
        if not is_thread_running:
            startDriver()
            is_thread_running = True
            start_check_request()
        try:
            response = get("YOUR_WORDPRESS_DOMAIN/wp-content/plugins/insert-product/api/get_products_api.php")
            if response.status_code != 200:
                logging.error("==== Can Not Connect To Elab, Response Status : " + str(response.status_code))
                sleep_duration = 3600
            else:
                products_data = response.json()
                for product in products_data:
                    try:
                        req = 0
                        old_price = product['actual_price']
                        new_price = getDetails(product['product_url'], True)
                    except Exception as error:
                        logging.error("==== Error Getting Product Data For : " + product['product_url'])
                        logging.error(error)
                        continue
                    if new_price != old_price:
                        logging.info(old_price)
                        logging.info(new_price)
                        notify_req = get("YOUR_WORDPRESS_DOMAIN/wp-content/plugins/insert-product/api/notification_api.php")
                        if notify_req.status_code == 200:
                            logging.info("== Admin Notificated")
                            sleep_duration = 10800
                            break
                        else:
                            logging.error("==== Error, Can not notify admin")
        except Exception as error:
            logging.error("====== Exception Error ======")
            logging.error(error)
            sleep_duration = 3600
        logging.info("Sleeping " + str(sleep_duration) + " Seconds")
        sleep(sleep_duration)

def start_check_products():
    check_thread = Thread(target=check_products, args=())
    check_thread.daemon = True
    check_thread.start()
# start_check_products()


class GetProduct(APIView):
    lookup_url_kwarg = 'url'

    def get(self, request, format=None):
        global req
        global is_thread_running
        req = 0
        if not is_thread_running:
            startDriver()
            is_thread_running = True
            start_check_request()

        product_url = request.GET.get(self.lookup_url_kwarg)
        is_correct_site = check_is_site(product_url)

        if product_url is not None and is_correct_site:
            try:
                result = getDetails(product_url)
                if result is None:
                    return JsonResponse({'message': 'No URL found'}, status=status.HTTP_404_NOT_FOUND)

                title, main_image, discription, attributes, photos, price, availability = result
                json_data = {
                    'title': title,
                    'main_image': main_image,
                    'discription': discription,
                    'attributes': attributes,
                    'photos': photos,
                    'price': price,
                    'availability': availability,
                }

                return JsonResponse(json_data, status=status.HTTP_200_OK)

            except Exception as error:
                response = JsonResponse({'message': 'Can not scrap data', 'error_details': str(error)}, status=status.HTTP_400_BAD_REQUEST)
                quitDriver()
                startDriver()
                return response

        return JsonResponse({'message': 'No URL found'}, status=status.HTTP_404_NOT_FOUND)