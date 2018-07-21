<?php

class facebookr
{
	protected $fb;
	protected $accessToken;

	public function __construct()
	{
	}

	function getFriends() 
	{
		$command = escapeshellcmd('../python_server/scrape_friends.py');
		$output = shell_exec($command);
		echo $output;
	}
}
