<?php
session_start();
require_once __DIR__ . '/database.php';
//echo "1";
//echo "2";
//echo "3";

echo "<html><body><font size=60>";

$db = new database();
//echo "4";
$db->getUserById($user['id']);
//echo "5";
if ($db->userExists($user['id'])) {
	echo '<p><a href="https://www.itzikm.co.il/get_unfriends.php">Get Unfriends List!!</a></p>';
	echo '<p><a href="https://www.itzikm.co.il/update.php">Update To Latest!!</a></p>';
	echo '<br>';
	echo '<p><a href="https://www.itzikm.co.il/unsubscribe.php">Unsubscribe</a></p>';
} else {
	echo '<p><a href="https://www.itzikm.co.il/subscribe.php">Subscribe!!</a></p>';
}
//echo "6";
// User is logged in!
echo "</font></body></html>";
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');
?>
