$host = 'http://localhost:4444/wd/hub'; // this is the default

$driver = RemoteWebDriver::create($host, DesiredCapabilities::chrome());
