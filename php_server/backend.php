<?php
require_once __DIR__ . '/facebookr.php';
require_once __DIR__ . '/database.php';

class actions
{
	var $fb;
	var $db;
	var $retval;
	public function __construct()
	{
		if (!session_id()) {
			session_start();
		}

		$fb = new facebookr();
		$db = new database();
	}

	public function isUserSubscribed()
	{
		$user = $fb->getUserData();
		$db->getUserById($user['id']);
		if ($db->userExists($user['id'])) {
			$retval = true;
		} else {
			$retval = false;
		}
		header('Content-type: application/json');
		echo json_encode('retval'=>$retval));
	}

	public function subscribe()
	{
		$user = $fb->getUserData();
		$allFriends = $fb->getFriends();
		$totalForUser = array();
		foreach ($allFriends as $key) {
			$totalForUser[$key['name']]++;
		}
		//
		// Insert User
		//
		$db->getUserById($user['id']);
		if ($db->userExists($user['id'])) {
		} else {
			if ($db->insertUser ($user['id'], $user['name'], $user['email'])) {
				$db->deleteAllFriendsOfUser();
				$db->addFriendsToUser($totalForUser);
				$retval = true;
			} else {
				$retval = false;
				//die ( "Error: " . $sql . "<br>" . $conn->error);
			}
		}
		header('Content-type: application/json');
		echo json_encode('retval'=>$retval));
	}

	public function unsubscribe()
	{
		$db->getUserById($user['id']);
		if ($db->userExists($user['id'])) {
			if ($db->deleteUser ()) {
				$db->deleteAllFriendsOfUser();
				$retval = true;
			} else {
				$retval = false;
		}
		header('Content-type: application/json');
		echo json_encode('retval'=>$retval));
	}



	public function get_unfriends()
	{
		$user = $fb->getUserData();
		$friendsOfUser = $fb->getFriends();
		$aggragatedForUser = array();
		foreach ($friendsOfUser as $key) {
			$aggragatedForUser[$key['name']]++;
		}

		$db->getUserById($user['id']);
		$unfriends_list = array();
		if ($db->userExists($user['id'])) {
			$friends_of_user_in_db = $db->getFriendsOfUser();
			foreach ($friends_of_user_in_db as $key => $value) {
				if (array_key_exists($key, $aggragatedForUser)) {
					$diff = $friends_of_user_in_db[$key] - $aggragatedForUser[$key];
					if ($diff < 0) {
						$unfriends_list[$key] = $diff * -1;
					}
				} else {
					$unfriends_list[$key] = $friends_of_user_in_db[$key];
				}
			}
		} else {
			//die ( "Error: user doesn't exist<br>");
			$retval = false;
		}
		header('Content-type: application/json');
		echo json_encode('unfriends_list'=>$unfriends_list));
	}
}
