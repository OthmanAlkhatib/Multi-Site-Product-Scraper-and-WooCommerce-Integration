from bs4 import BeautifulSoup
import requests
from selenium.webdriver import Chrome, ChromeOptions
from selenium.webdriver.common.by import By
from webdriver_manager.chrome import ChromeDriverManager
# from requests_html import HTMLSession
# import time

class Scrap:
    def __init__(self):
        self.HEADERS = ({
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36',
            'Accept-Language': 'en-US, en;q=0.5'
        })
        self.options = ChromeOptions()
        self.options.add_argument("--start-maximized")
        self.options.add_argument("--log-level=3")
        self.options.add_argument('--window-size=1920,1080')
        self.options.add_argument("--headless")
        self.options.add_argument("user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36")

        self.chrome_prefs = dict()
        self.chrome_prefs["profile.default_content_settings"] = {"images": 2}
        self.chrome_prefs["profile.managed_default_content_settings"] = {"images": 2}
        self.options.experimental_options["prefs"] = self.chrome_prefs

    def amazon_scrap(self, url):
        try:
            # response = requests.get(url, 'html.parser', headers=self.HEADERS)
            # soup = BeautifulSoup(response.content, 'html.parser')
            html = self.get_page_source(url)
            soup = BeautifulSoup(html, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.find(id="productTitle").text.strip()
        except:
            title = "null"

        try:
            main_image = soup.find(id="landingImage")["src"]
        except:
            main_image = "null"

        try:
            discription = [soup.find(id="productDescription").p.span.text.strip()]
        except:
            discription = "null"

        try:
            attributes = []
            for li in soup.find(id="feature-bullets").ul.find_all("li"):
                attributes.append(li.span.text.strip())
        except:
            attributes = "null"

        try:
            alt_photos = []
            cnt = 1
            while True:
                img = soup.select_one(f"li[class~='itemNo{str(cnt)}'] img")
                if img == None:
                    break
                alt_photos.append(img["src"])
                cnt += 1
        except Exception as error:
            alt_photos = "null"
            print(error)

        try:
            price = soup.find(class_="a-offscreen").text.strip()
        except:
            price = "null"

        try:
            availability = soup.find(id="availability").span.text.strip()
        except:
            availability = "null"

        return [title, main_image, discription, attributes, alt_photos, price, availability]

    def medcart_scrap(self, url):
        try:
            response = requests.get(url, 'html.parser', headers=self.HEADERS)
            soup = BeautifulSoup(response.content, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.find(class_="product-text-view").h1.text.strip()
        except:
            title = 'null'

        try:
            main_image = soup.find(id="thumbnail-large-img")["src"]
        except:
            main_image = 'null'

        try:
            discription = [soup.find(id="id-desc").p.text.strip()]
        except:
            discription = 'null'

        try:
            attributes = soup.find(class_="additional-info").p.text.strip()
        except:
            attributes = 'null'

        try:
            alt_photos = []
            imgs = soup.find(id="myTab2").find_all("li")
            for li in range(len(imgs)):
                try:
                    alt_photos.append(imgs[li].img["src"])
                except Exception as error:
                    continue
        except:
            alt_photos = 'null'

        try:
            price = soup.find(id="unit_selling_price").text.strip()
        except:
            price = 'null'

        # availability = soup.find(id="availability").span.text.strip()
        availability = 'null'

        return [title, main_image, discription, attributes, alt_photos, price, availability]

    def alibaba_scrap(self, url):
        # response = requests.get(url, 'html.parser', headers=self.HEADERS)
        # response = self.session.get(url)
        # response.html.render(timeout=60)

        try:
            html = self.get_page_source(url)
            soup = BeautifulSoup(html, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.find(class_="product-title").h1.text.strip()
        except:
            title = 'null'

        try:
            main_image = soup.find(class_="main-img")["src"]
        except:
            main_image = 'null'

        try:
            discription = []
            overview = soup.find(class_="do-entry-list").find_all("dl")
            for dl in overview:
                discription.append(dl.dt.span.text.strip() + dl.dd.div.text.strip())
        except:
            discription = 'null'

        # attributes = soup.find(class_="additional-info").p.text.strip()
        attributes = 'null'

        try:
            alt_photos = []
            imgs = soup.select(".detail-next-slick-slide.main-item")
            for main_item in range(len(imgs)):
                try:
                    alt_photos.append(imgs[main_item].img["src"])
                except Exception as error:
                    continue

            for img_index in range(len(alt_photos)):
                alt_photos[img_index] = alt_photos[img_index].replace("100x100xz", "960x960")
        except Exception as error:
            alt_photos = 'null'
            print(error)

        try:
            price = soup.find(class_="price").text.strip()
        except:
            price = 'null'

        # availability = soup.find(id="availability").span.text.strip()
        availability = 'null'

        return [title, main_image, discription, attributes, alt_photos, price, availability]
        # return [title, main_image, discription, alt_photos, price]

    def aliexpress_scrap(self, url):
        try:
            html = self.get_page_source(url)
            soup = BeautifulSoup(html, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.find(class_="product-title-text").text.strip()
        except:
            title = 'null'

        try:
            all_images = soup.find(class_="images-view-list").find_all('img')
            images_len = len(all_images)

            main_image = all_images[0]['src']
            alt_photos = []
            for img_index in range(1, images_len):
                alt_photos.append(all_images[img_index]['src'])

            main_image = main_image.replace("_50x50.jpg_", "_Q90.jpg_")
            for img_src in range(len(alt_photos)):
                alt_photos[img_src] = alt_photos[img_src].replace("_50x50.jpg_", "_Q90.jpg_")
        except:
            main_image = 'null'
            alt_photos = 'null'

        try:
            discription = []
            overview = soup.find(class_="do-entry-list").find_all("dl")
            for dl in overview:
                discription.append(dl.dt.span.text.strip() + dl.dd.div.text.strip())
        except:
            discription = 'null'

        attributes = 'null'

        try:
            price = soup.find(class_="uniform-banner-box-price")
            if price is None :
                price = soup.find(class_="product-price-value")
            price = price.text.strip()

        except:
            price = 'null'

        availability = 'null'

        return [title, main_image, discription, attributes, alt_photos, price, availability]
        # return [title, main_image, discription, alt_photos, price]

    def noon_scrap(self, url):
        # response = self.session.get(url)
        # response.html.render(timeout=60)

        # response = requests.get(url, 'html.parser', headers=self.HEADERS)
        try:
            html = self.get_page_source(url)
            soup = BeautifulSoup(html, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.select_one("h1[data-qa*='pdp-name']").text.strip()
        except:
            title = 'null'

        try:
            imgs = soup.select("div[class~='bEHGRQ'] img")
        except:
            imgs = 'null'

        try:
            main_image = imgs[0]["src"]
        except:
            main_image = 'null'

        try:
            overview = soup.select_one("div[class~=igCiTB] ul")
            discription = []
            for li in overview:
                discription.append(li.text.strip())
        except:
            discription = 'null'

        try:
            attributes = []
            attrs_div = soup.select("div[class~=bqEeIs]")
            for div in attrs_div:
                attrs_tr = div.find_all("tr")
                for tr in attrs_tr:
                    attrs_td = tr.find_all("td")
                    attributes.append(attrs_td[0].text + " : " + attrs_td[1].text)
            if len(attributes) == 0:
                attributes = 'null'
        except:
            attributes = 'null'

        try:
            alt_photos = []
            for img in range(len(imgs)):
                try:
                    alt_photos.append(imgs[img]["src"])
                except Exception as error:
                    continue
            if len(alt_photos) == 0:
                alt_photos = 'null'
        except:
            alt_photos = 'null'

        try:
            price = " ".join(soup.find(class_="priceNow").text.split()[0:2])
        except:
            price = 'null'

        # availability = soup.find(id="availability").span.text.strip()
        availability = 'null'

        return [title, main_image, discription, attributes, alt_photos, price, availability]
        # return [title, main_image, discription, attributes, alt_photos, price]

    def desertcart_scrap(self, url):
        # response = self.session.get(url)
        # response.html.render(timeout=60)

        # response = requests.get(url, 'html.parser', headers=self.HEADERS)

        try:
            html = self.get_page_source(url)
            soup = BeautifulSoup(html, 'html.parser')
        except Exception as error:
            return None

        try:
            title = soup.find(class_="ProductPage__productTitle").text.strip()
        except:
            title = 'null'

        try:
            imgs = soup.select(".ProductThumbnails__option-container img")
            imgs_len = len(imgs)
            if imgs_len > 4:
                imgs_len = (imgs_len - 4) // 2
                main_image = imgs[4]["src"]
            else:
                main_image = imgs[0]["src"]
                main_image = main_image.replace(".SS50", "")
        except:
            main_image = 'null'
            imgs_len = ''

        try:
            overview = soup.select_one(".ProductDescription__text ul")
            discription = []
            for li in overview:
                discription.append(li.text.strip())
            if len(discription) == 0:
                discription = 'null'
        except:
            discription = 'null'

        # attributes = []
        # attrs_div = soup.select("div[class~=bqEeIs]")
        # for div in attrs_div:
        #     attrs_tr = div.find_all("tr")
        #     for tr in attrs_tr:
        #         attrs_td = tr.find_all("td")
        #         attributes.append(attrs_td[0].text + " : " + attrs_td[1].text)
        attributes = 'null'

        try:
            alt_photos = []
            for img_index in range(imgs_len):
                try:
                    alt_photos.append(imgs[img_index]["src"])
                except Exception as error:
                    continue

            for img_index in range(len(alt_photos)):
                alt_photos[img_index] = alt_photos[img_index].replace(".SS50", "")
        except:
            alt_photos = 'null'

        availability = 'null'

        try:
            price = soup.find(class_="ProductThumbnails__price").text
        except:
            price = 'null'

        return [title, main_image, discription, attributes, alt_photos, price, availability]
        # return [title, main_image, discription, alt_photos, availability, price]


    def get_page_source(self, url):
        self.driver.get(url)
        if 'amazon' in url:
            altImages = self.driver.find_elements(By.CSS_SELECTOR, 'div[id=altImages] ul li img')
            for small_image in altImages:
                try:
                    self.driver.execute_script("arguments[0].click();", small_image)
                except Exception as error:
                    break
        html = self.driver.page_source

        return html

    def start_driver(self):
        self.driver = Chrome(ChromeDriverManager().install(), options=self.options)

    def quit_driver(self):
        try:
            self.driver.quit()
        except Exception as error:
            print('quit_driver: ', error)



scrapper = Scrap()

def startDriver():
    scrapper.start_driver()
def quitDriver():
    scrapper.quit_driver()

# startDriver()

def getDetails(url, for_check = False):
    websites = ['desertcart', 'amazon', 'medcart', 'alibaba', 'noon', 'aliexpress']
    for website in websites:
        if website in url:
            if website == 'desertcart':
                result = scrapper.desertcart_scrap(url)
            elif website == 'amazon':
                result = scrapper.amazon_scrap(url)
            elif website == 'medcart':
                result = scrapper.medcart_scrap(url)
            elif website == 'alibaba':
                result = scrapper.alibaba_scrap(url)
            elif website == 'noon':
                result = scrapper.noon_scrap(url)
            elif website == 'aliexpress':
                result = scrapper.aliexpress_scrap(url)

            if for_check:
                return result[-2]
            return result
    return None
