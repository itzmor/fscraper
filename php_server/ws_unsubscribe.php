<?php
require_once __DIR__ . '/database.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$userid = $_POST['userid'];

$db->getUserById($userid);
if ($db->userExists($userid)) {
	if ($db->deleteUser ()) {
		$db->deleteAllFriendsOfUser();
        }
}
	
header('Content-type: application/json');
$arr = array('retval' => true);
echo json_encode($arr);
$db->close();
