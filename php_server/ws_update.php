<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fbscraper.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$userid = $_POST['userid'];

//$allFriends = $_POST['friends'];
$db->getUserById($userid);
$fbaccount = $db->getFbAccount();
$fbs = new fbscraper();
$allFriends = $fbs->getFriends($fbaccount);

$totalForUser = array();
foreach ($allFriends as $key) {
	if (array_key_exists($key, $totalForUser)) {
		$totalForUser[$key]++;
	} else {
		$totalForUser[$key]=1;
	}
}


$db->deleteAllFriendsOfUser();
$db->addFriendsToUser($totalForUser);

header('Content-type: application/json');
$arr = array('retval' => true);
echo json_encode($arr);
$db->close();
