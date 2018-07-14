<?php

class database
{

    const SERVERNAME = 'localhost:3306';
    //const SERVERNAME = 'itzikm.co.il:3306';
    //const SERVERNAME = 'localhost:3307';
    const username = 'itzik_dbuser';
    const password = 'qwe123';
    const dbname = 'db_for_facebook';

    protected $conn;
    protected $user_id;
    protected $user_name;
    protected $user_email;
    protected $user_in_db = false;

    public function __construct()
    {
        // Create connection
        $this->conn = new mysqli(static::SERVERNAME, static::username, static::password, static::dbname);

        // Check connection
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
	return $this;
    }

    public function getUserById(string $user_id)
    {
        $sql = "SELECT id, name, email, should_schedule, schedule_time FROM users where id=" . $user_id;
        $result = $this->conn->query($sql);
        if ($result->num_rows == 1) {
            // output data of each row
            $row = $result->fetch_assoc();
            $this->user_id = $row["id"];
	    $this->user_name = $row["name"];
	    $this->is_schedule = $row["should_schedule"];
	    $this->schedule_time = $row["schedule_time"];
	    $this->user_in_db = true;
	    return true;
        } else {
		if ($result->num_rows > 1) {
			die ("More than 1 user record in database");
		}
		return false;
	}
    }

	public function getFriendsOfUser()
	{
		$sql = "SELECT id, friend_name, count FROM friends_of_users where id=" . $this->user_id;
		$result = $this->conn->query($sql);
		while($row = $result->fetch_assoc()) {
			$friends_of_user_in_db[$row['friend_name']] = $row['count'];
		}
		return $friends_of_user_in_db;
	}

	public function getId() {
		return $this->user_id;
	}
	public function getName() {
		return $this->user_name;
	}
	public function getEmail() {
		return $this->user_email;
	}
	public function userExists() {
		return $this->user_in_db;
	}
	public function isSchedule() {
		return $this->is_schedule;
	}
	public function getScheduleTime() {
		return $this->schedule_time;
	}

	public function insertUser (string $id, string $name, string $email, bool $is_schedule, String $schedule_time)
	{
		$id =  mysqli_real_escape_string($this->conn, $id);
		$name =  mysqli_real_escape_string($this->conn, $name);
		$email =  mysqli_real_escape_string($this->conn, $email);
		$is_schedule =  mysqli_real_escape_string($this->conn, $is_schedule);
		$schedule_time =  mysqli_real_escape_string($this->conn, $schedule_time);
		if ($email == null || $email == NULL || $email == "") {
			$email = "a@a.com";
		}
		$write_is_schedule="";
		if ($is_schedule) {
			$write_is_schedule = "true";
		} else {
			$write_is_schedule = "false";
		}
		//$escaped_name=str_replace("'","\'",$name);
		$sql = "INSERT INTO users (id, name, email, should_schedule, Schedule_time)
			VALUES ('" . $id . "', '" . $name . "', '" . $email . "', " . $write_is_schedule . ", '" . $schedule_time ."')";

		if ($this->conn->query($sql) === TRUE) {
	    		$this->user_id = $id;
	    		$this->user_name = $name;
	    		$this->user_email = $email;
	    		$this->is_schedule = $is_schedule;
	    		$this->schedule_time = $schedule_time;
			return true;
		} else {
			die ("Failed to insert user to database");
	    	}
	}

        //
        // Delete all friends
        //
	public function deleteUser ()
	{
                $sql = "delete from users where id = '" . $this->user_id . "'";
                $result = $this->conn->query($sql);
		if (!$result) {
			die ("Failed to delete user");
		}
		return true;
	}

        //
        // Delete all friends
        //
	public function deleteAllFriendsOfUser ()
	{
                $sql = "delete from friends_of_users where id = '" . $this->user_id . "'";
                $result = $this->conn->query($sql);
		if (!$result) {
			die ("Failed to delete user friends");
		}
	}

	
        //
        // Add all friends
        //
	public function addFriendsToUser (array $totalForUser)
	{
                foreach ($totalForUser as $key => $val) {
			//$escaped_key=str_replace("'","\'",$key);
			$key =  mysqli_real_escape_string($this->conn, $key);
                        $sql = "INSERT INTO friends_of_users (id, friend_name, count)
                                VALUES ('" . $this->user_id . "', '" . $key . "', " . $val . ")";
                        $result = $this->conn->query($sql);

                        if ($result == FALSE) {
                                die("Failed to insert data (" . $sql . ")" . $this->conn->connect_error);
                        }
                }
	}

	public function close ()
	{
		$this->conn->close();
	}
}
