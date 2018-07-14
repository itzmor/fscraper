<?php
use FacebookApi\FacebookApiException;

class facebookr
{
	protected $fb;
	protected $accessToken;

	public function __construct()
	{
		require_once __DIR__ . '/Facebook/autoload.php'; // change path as needed
		$this->fb = new Facebook\Facebook([
			  'app_id' => '415374225575006',
			  'app_secret' => '09e4f64c13d8ebdcf1599b2903e3115d',
			  'default_graph_version' => 'v2.12',
		]);
//		echo "b";
		$helper = $this->fb->getCanvasHelper();

//		echo "c";
//		echo "d";
		try {
			if (isset($_SESSION['facebook_access_token'])) {
				$accessToken = $_SESSION['facebook_access_token'];
				$this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
//		echo "e";
			} else {
			  		// OAuth 2.0 client handler
					$oAuth2Client = $this->fb->getOAuth2Client();
					// Exchanges a short-lived access token for a long-lived one
					$longLivedAccessToken = $oAuth2Client->getLongLivedAccessToken($_SESSION['facebook_access_token']);
					$_SESSION['facebook_access_token'] = (string) $longLivedAccessToken;
					$this->fb->setDefaultAccessToken($_SESSION['facebook_access_token']);
//					echo "AT=" . $_SESSION['facebook_access_token'] . "<br>";
					exit;
			}
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		 	// When Graph returns an error
		 	echo 'Graph returned an error: ' . $e->getMessage();
//		echo "g";
		  	exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		 	// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
//		echo "h";
		  	exit;
		}
//		echo "m";
		//} else {
		//echo "n";
		//	$helper = $this->fb->getRedirectLoginHelper();
		//	$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/415374225575006/', $permissions);
		//	//echo "<script>window.top.location.href='".$loginUrl."'</script>";
		//	echo "PROBLEM";
		//}
	}

	function getUserData()
	{
		//$permissions = ['user_friends,email']; // optionnal
		// validating the access token
		try {
			$request = $this->fb->get('/me?fields=id,name,email');
			$user = $request->getGraphUser();
			echo "u=" . $user['name'];
			return $user;
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			//if ($e->getCode() == 190) {
			//	unset($_SESSION['facebook_access_token']);
			//	$helper = $this->fb->getRedirectLoginHelper();
			//	$loginUrl = $helper->getLoginUrl('https://apps.facebook.com/415374225575006/', $permissions);
			//	echo "<script>window.top.location.href='".$loginUrl."'</script>";
			//}
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookApiException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
	}

	function getFriends() 
	{
		// get list of friends' names
		try {
			$requestFriends = $this->fb->get('/me/taggable_friends?fields=name&limit=100');
			$friends = $requestFriends->getGraphEdge();
			print_r ($friends);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
			// When Graph returns an error
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
			// When validation fails or other local issues
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		// if have more friends than 100 as we defined the limit above on line no. 68
		if ($this->fb->next($friends)) {
			echo "1";
			$allFriends = array();
			$friendsArray = $friends->asArray();
			$allFriends = array_merge($friendsArray, $allFriends);
			while ($friends = $this->fb->next($friends)) {
			echo "2";
				$friendsArray = $friends->asArray();
				$allFriends = array_merge($friendsArray, $allFriends);
			}
		} else {
			echo "3";
			$allFriends = $friends->asArray();
			$totalFriends = count($allFriends);
		}
			echo "4";
		print_r ($allFriends);
		return $allFriends;
	}
}
