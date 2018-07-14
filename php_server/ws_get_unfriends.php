<?php
require_once __DIR__ . '/database.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$userid = $_POST['userid'];
$friendsOfUser = $_POST['friends'];
$aggragatedForUser = array();
foreach ($friendsOfUser as $key) {
	$aggragatedForUser[$key]++;
}

$db->getUserById($userid);
$unfriends_list = array();
if ($db->userExists($userid)) {
	$friends_of_user_in_db = $db->getFriendsOfUser();
	foreach ($friends_of_user_in_db as $key => $value) {
		if (array_key_exists($key, $aggragatedForUser)) {
			$diff = $friends_of_user_in_db[$key] - $aggragatedForUser[$key];
			if ($diff > 0) {
				$unfriends_list[$key] = $diff;
			}
		} else {
			$unfriends_list[$key] = $friends_of_user_in_db[$key];
		}
	}
} else {
	die ( "Error: user doesn't exist<br>");
}


$db->close();	

$unfriends_in_simple_array = array();
$index=0;
foreach ($unfriends_list as $key => $value) {
	for ($index2=0; $index2<$value; $index2++)
	{
		$unfriends_in_simple_array[$index] = $key;
		$index++;
	}
}

header('Content-type: application/json');
$arr = array('unfriends_list' => $unfriends_in_simple_array);
echo json_encode($arr);
