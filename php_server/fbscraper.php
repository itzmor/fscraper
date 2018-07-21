<?php
require_once('vendor/autoload.php');
use Facebook\WebDriver;

class fbscraper
{

    const SERVERNAME = 'localhost:3306';
    //const SERVERNAME = 'itzikm.co.il:3306';
    //const SERVERNAME = 'localhost:3307';
    const username = 'itzik_dbuser';
    const password = 'qwe123';
    const dbname = 'db_for_facebook';

    protected $user_in_db = false;

    public function getFriends(String $fbAccount)
    {
	$host = 'http://itzik-H110M-S2H:4444/wd/hub'; // this is the default

	$options = new ChromeOptions();
	$prefs = array('profile.default_content_setting_values.notifications' => 2);
	$options->setExperimentalOption('prefs', $prefs);

	$capabilities = DesiredCapabilities::chrome(); 
	$capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

	$driver = RemoteWebDriver::create($host, $capabilities, 5000);

	$email2 = "ramishavit01@walla.com";
	$pass2 = "qwe123";

	$driver->get('https://facebook.com/login');
	$mail = $driver->findElement(WebDriverBy::cssSelector('#email'));
	$mail->sendKeys($email2);
	$pass = $driver->findElement(WebDriverBy::cssSelector('#pass'));
	$pass->sendKeys($pass2);

	$driver->findElement(WebDriverBy::cssSelector('#loginbutton'))->click();

	#$driver->get('https://facebook.com/itzik.moradov/friends');
	$driver->get('https://facebook.com/' . $fbAccount . '/friends');

	usleep(2500);
	$last_height = $driver->executeScript('return document.body.scrollHeight');

	while (1) {
	        # Scroll down to bottom
	        $driver->executeScript("window.scrollTo(0, " . $last_height . ");");

		for ($ind = 0; $ind < 10; $ind++) {
			usleep(1000000);
			$new_height = $driver->executeScript('return document.body.scrollHeight');
	        	if ($new_height != $last_height) {
				break;
			}
		}
	        # Calculate new scroll height and compare with last scroll height
	        if ($new_height == $last_height) {
	            break;
		}
	        $last_height = $new_height;
	}

	usleep(2500000);


	$dom = new DOMDocument();
	$dom->loadHTML($driver->getPageSource());

	$scraped_friends = array();
	ini_set('max_execution_time', 300);
	foreach($dom->getElementsByTagName('a') as $link) {
		ini_set('max_execution_time', 300);
		if (strpos($link->getAttribute('href'), "hc_location=friends_tab")) {
			if ($link->textContent !== "") {
				array_push($scraped_friends, $link->textContent);
			}
		}
	}
	$driver->close();
	return ($scraped_friends);
    }
}
