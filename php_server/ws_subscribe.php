<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/fbscraper.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$name = $_POST['name'];
$email = $_POST['email'];
$userid = $_POST['userid'];
$fbaccount = $_POST['fbaccount'];

$fbs = new fbscraper();
//$allFriends = $fbs->getFriends($fbaccount);
#$allFriends = $_POST['friends'];
$totalForUser = array();
foreach ($allFriends as $key) {
	$totalForUser[$key]++;
}

//
// Insert User
//
$db->getUserById($userid);
if ($db->userExists($userid)) {
	$retval = true;
} else {
	if ($db->insertUser ($userid, $name, $email, false, "0400", "")) {
		$db->deleteAllFriendsOfUser();
		$db->addFriendsToUser($totalForUser);
		$retval = true;
	} else {
		$retval = false;
		//die ( "Error: " . $sql . "<br>" . $conn->error);
	}
}
header('Content-type: application/json');
$arr = array('retval' => true);
echo json_encode($arr);
$db->close();
