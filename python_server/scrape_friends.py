#!/usr/bin/env python3
from selenium import webdriver
options = webdriver.ChromeOptions()
prefs = {"profile.default_content_setting_values.notifications" : 2}
options.add_experimental_option("prefs",prefs)
chrome = webdriver.Chrome("/home/itzik/Downloads/chromedriver", chrome_options=options)
 
import time
 
email = "ramishavit01@walla.com"
pass2 = "qwe123" 
def facebook():
    chrome.get('https://facebook.com/login')
    user = chrome.find_element_by_css_selector('#email')
    user.send_keys(email)
    password = chrome.find_element_by_css_selector('#pass')
    password.send_keys(pass2)
    login = chrome.find_element_by_css_selector('#loginbutton')
    login.click()
 
    time.sleep(10)
    chrome.get('https://facebook.com/itzik.moradov/friends')
    time.sleep(10)
    friend_name = chrome.find_element_by_id('fb-timeline-cover-name')
    print(friend_name.text)
 
    more_about = str('More About ' + str(friend_name.text))
    print(more_about)
    scroll_to_bottom()

def scroll_to_bottom():
    SCROLL_PAUSE_TIME = 2.5
    # Get scroll height
    last_height = chrome.execute_script("return document.body.scrollHeight")
 
    while True:
        # Scroll down to bottom
        chrome.execute_script("window.scrollTo(0, document.body.scrollHeight);")
 
        # Wait to load page
        time.sleep(SCROLL_PAUSE_TIME)
 
        # Calculate new scroll height and compare with last scroll height
        new_height = chrome.execute_script("return document.body.scrollHeight")
        if new_height == last_height:
            break
        last_height = new_height
 
facebook()

html = chrome.page_source

import bs4
import requests
import lxml
htmls = bs4.BeautifulSoup(html, 'lxml')
alla = htmls.findAll('a')
alla = htmls.select("a[href*=friends_tab]")

for i in alla:
    if i.text != "":
        print (i.text)
print (len(alla))
