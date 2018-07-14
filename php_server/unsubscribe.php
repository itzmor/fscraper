<?php
require_once __DIR__ . '/facebookr.php';
require_once __DIR__ . '/database.php';

if (!session_id()) {
    session_start();
}

$fb = new facebookr();
$user = $fb->getUserData();

$db = new database();
$db->getUserById($user['id']);
if ($db->userExists($user['id'])) {
	if ($db->deleteUser ()) {
		$db->deleteAllFriendsOfUser();
        }
}
	
$db->close();	
echo "<html><body><font size=60>";
echo "<br><p>You are now unsubscribed</p>";
echo "<br";
echo "<br";
echo "<br";
echo '<p><a href="https://www.itzikm.co.il/index.html">Use again!!</a></p>';

echo "</font></body></html>";

