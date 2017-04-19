<?php
	session_start();
	
	if (!isset($_SESSION['username']))
	{
		$status = 0;
	} 
	else
	{
		$status = $_SESSION['user_index'];
		$id_user = $_SESSION['id'];

		//should fix the username inheritance thingie when they enter  through the use of email
		$username = $_SESSION['username'];


		/*$sql_active_insert = "INSERT INTO active_users (user_id, noob_tag) VALUES ('" . $id_user . "', " . $username ."')";
		$result_active_insert = $conn->query($sql_active_insert);*/

		/*echo $status . ' ' . $id_user . ' ' . $username;
		$endtime =strtotime(date('Y-m-d h:i:s'));
		require_once("dbconnect.php");
		$query = "SELECT * FROM active_users WHERE noob_tag='" . $username . "'";
		$result = $conn->query($query);
		var_dump($result);
		if (!$result) {
			$query_update = "UPDATE active_users SET dt='" . $endtime . "' WHERE id_user='" . $id_user . "'";
			$result_update = $conn->query($query_update);
			echo "It' all fine";
		}
		else
		{
			$query_insert = "INSERT INTO active_users (id_user, noob_tag, dt) VALUES ('" . $id_user . "', '" . $username . "', '" . $endtime . "')";
			$result_insert = $conn->query($query_insert);
			echo "I'm a fool!";

		}*/
	}
