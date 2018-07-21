<?php
require_once __DIR__ . '/database.php';

$retval=true;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$name = $_POST['name'];
$email = $_POST['email'];
$userid = $_POST['userid'];
$is_schedule = $_POST['is_schedule'];
$schedule_time = $_POST['schedule_time'];
$fb_account = $_POST['fb_account'];

$db->getUserById($userid);
if ($db->userExists($userid)) {
	$db->deleteUser();
	if ($db->insertUser ($userid, $name, $email, $is_schedule, $schedule_time, $fb_account)) {
		$retval = true;
	} else {
		$retval = false;
	}
	$retval = true;
} else {
	$retval = false;
}
header('Content-type: application/json');
$arr = array('retval' => true);
echo json_encode($arr);
$db->close();
