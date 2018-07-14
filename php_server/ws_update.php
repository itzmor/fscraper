<?php
require_once __DIR__ . '/database.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$userid = $_POST['userid'];

$allFriends = $_POST['friends'];
$totalForUser = array();
foreach ($allFriends as $key) {
        $totalForUser[$key]++;
}

$db->getUserById($userid);

$db->deleteAllFriendsOfUser();
$db->addFriendsToUser($totalForUser);

header('Content-type: application/json');
$arr = array('retval' => true);
echo json_encode($arr);
$db->close();
