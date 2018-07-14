<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/facebookr.php';
if (!session_id()) {
    session_start();
}

	$fb = new facebookr();
	$user = $fb->getUserData();
	$allFriends = $fb->getFriends();
	$totalForUser = array();
	foreach ($allFriends as $key) {
		$totalForUser[$key['name']]++;
	}

	//
	// Insert User
	//

	$db = new database();
	$db->getUserById($user['id']);
	if ($db->userExists($user['id'])) {
	} else {
		if ($db->insertUser ($user['id'], $user['name'], $user['email'])) {
			$db->deleteAllFriendsOfUser();
			$db->addFriendsToUser($totalForUser);
		} else {
			die ( "Error: " . $sql . "<br>" . $conn->error);
		}
	}


	$db->close();	
	echo "<html><body><font size=60>";
	echo "<br><p>You are now subscribed</p>";
	echo "<br";
	echo "<br";
	echo "<br";
	echo '<p><a href="https://www.itzikm.co.il/index.html">Use again!!</a></p>';
	//echo '<p><a href="https://www.facebook.com/">Back to the feed</a></p>';
	echo "</font></body></html>";
	// Now you can redirect to another page and use the access token from $_SESSION['facebook_access_token']
