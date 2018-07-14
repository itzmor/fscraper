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

$db->deleteAllFriendsOfUser();
$db->addFriendsToUser($aggragatedForUser);

$db->close();	
echo "<html><body><font size=60>";
echo "<br><p>Friends list has been updated</p>";
echo "<br";
echo "<br";
echo "<br";
echo '<p><a href="https://www.itzikm.co.il/index.html">Use again!!</a></p>';

echo "</font></body></html>";
