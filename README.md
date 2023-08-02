# Multi-Site Product Scraper and WooCommerce Integration

## Description
The Multi-Site Product Scraper and WooCommerce Integration is a powerful and comprehensive project that combines Django server side and WordPress server side to streamline product scraping and WooCommerce management. It leverages Python libraries such as Selenium and BeautifulSoup to create a robust web scraper that can extract product data from various renowned websites, including Amazon, Desertcart, Medcart, Alibaba, Noon, and AliExpress.

## Django Server Side Features
1. Web Scraper API: The Django server side hosts a versatile API that receives product URLs from different websites and returns JSON data containing scraped product information.
2. Dynamic Web Scraping: The scraper uses Selenium and BeautifulSoup to dynamically fetch product data, ensuring accuracy and adaptability to website changes.
3. Thread Management: The server runs a thread to monitor API requests, shutting down the web driver after an hour of inactivity to optimize server resources.

## WordPress Server Side Features
1. WordPress Plugin: The WordPress server side introduces a user-friendly plugin accessible from the WordPress dashboard.
2. Inserting Products: In the plugin's "Insert Product" tab, users can input a product URL, fetch its data from the Django API, and insert it as a WooCommerce product into the database.
3. Checking Updates: The "Check Updates" tab allows users to compare stored product prices with real-time data by requesting the Django API, highlighting products with price changes, and offering the option to update prices accordingly.
4. Price Difference Notifications: A Django thread runs every 3 hours to send notifications to WordPress if price differences are detected between stored data and real websites.
