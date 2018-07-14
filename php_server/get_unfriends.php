<?php
require_once __DIR__ . '/facebookr.php';
require_once __DIR__ . '/database.php';
if (!session_id()) {
    session_start();
}

$fb = new facebookr();
$user = $fb->getUserData();
$friendsOfUser = $fb->getFriends();
$aggragatedForUser = array();
foreach ($friendsOfUser as $key) {
	$aggragatedForUser[$key['name']]++;
}


$db = new database();
$db->getUserById($user['id']);
$unfriends_list = array();
if ($db->userExists($user['id'])) {
	$friends_of_user_in_db = $db->getFriendsOfUser();
	foreach ($friends_of_user_in_db as $key => $value) {
		if (array_key_exists($key, $aggragatedForUser)) {
			$diff = $friends_of_user_in_db[$key] - $aggragatedForUser[$key];
			if ($diff < 0) {
				$unfriends_list[$key] = $diff * -1;
			}
		} else {
			$unfriends_list[$key] = $friends_of_user_in_db[$key];
		}
	}
} else {
	die ( "Error: user doesn't exist<br>");
}

//$db->deleteAllFriendsOfUser();
//$db->addFriendsToUser($aggragatedForUser);

$db->close();	
echo "<html><body><font size=60>";
echo "<br><p>List of unfriends</p>";
//echo "<table border=1>";
foreach ($unfriends_list as $key => $value) {
	for ($index=0; $index < $value; $index++) {
		//echo "<tr><td>" . $key . "</td></tr>";
		echo "<br>" . $key;
	}
}
//echo "</table>";
echo "<br>";
echo "<br>";
echo "<br>";
echo '<p><a href="https://www.itzikm.co.il/index.html">Use again!!</a></p>';

echo "</font></body></html>";
