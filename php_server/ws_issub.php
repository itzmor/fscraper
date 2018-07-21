<?php
require_once __DIR__ . '/database.php';

$retval=false;

$db = new database();

$_POST = json_decode(file_get_contents('php://input'), true);
$userid = $_POST['userid'];

$db->getUserById($userid);
if ($db->userExists($userid)) {
	$retval=true;
}
header('Content-type: application/json');
$arr = array('retval' => $retval, 'name' => $db->getName(), 'email' => $db->getEmail(), 'is_schedule' => $db->isSchedule(), 'schedule_time' => $db->getScheduleTime(), 'fb_account' => $db->getFbAccount());
echo json_encode($arr);
$db->close();
