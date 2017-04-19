<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	
	date_default_timezone_set("Europe/Sofia");
	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	echo "<div class='fragment-left fragment-small'><h1>Friends</h1><hr/>";

	//JUST A SIMPLE FORM INTENDED FOR THE SEARCH OF USERS	
	$friend_search_form = "<form method='POST'>";
	$friend_search_form .= "<label><b>Search profiles</b></label>";
	$friend_search_form .= "<input class='typeWBGin' type='text' name='noob_tag' placeholder='Noob tag' />";
	$friend_search_form .= "<br/><br/><button type='submit' name='search_user' value='search_user'><i class='fa fa-search'></i>Search</button>";
	$friend_search_form .= "</form><br/>";

	echo $friend_search_form;

	//NOW THE SEARCH
	if (isset($_POST['search_user']))
	{
		$noob_tag = mysqli_real_escape_string($conn, $_POST['noob_tag']);

		$sql_accounts = "SELECT * FROM accounts";
		$result_accounts = mysqli_query($conn, $sql_accounts);

		if (empty($noob_tag))
		{
			echo "Empty field!";
		}
		else
		{	
			$i = 0;
			while ($row_accounts = mysqli_fetch_assoc($result_accounts)) 
			{
				if (strpos(strtolower($row_accounts['noob_tag']), strtolower($noob_tag)) !== false && $row_accounts['id'] != $id_user)
				{	
					$i++;
					$sql_check_friendship = "SELECT * FROM friendships WHERE user_one_id='" . $id_user . "' AND user_two_id='" . $row_accounts['id'] . "' OR user_one_id='" . $row_accounts['id'] . "' AND user_two_id='" . $id_user . "'";
					$result_check_friendship = mysqli_query($conn, $sql_check_friendship);

					
					$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_accounts['id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");
					$friend_section .=  "<div class='friend-tab'>";

					if (!mysqli_num_rows($result_profile_image))
					{
						$friend_section .= "<div class='avatar-thumb'></div>";
					}
					else
					{
						$row_profile_image = mysqli_fetch_assoc($result_profile_image);
						$friend_section .= "<div class='avatar-thumb' style='background-image:url(../images/avatars/" . $row_profile_image['file_name'] . ");'></div>";
					}
					if (mysqli_num_rows($result_check_friendship))
					{	
						$row_check_friendship = mysqli_fetch_assoc($result_check_friendship);
						if ($row_check_friendship['status'] == 1)
						{
							$friend_section .= $row_accounts['noob_tag'] . "<div class='options'><i class='fa fa-check' aria-hidden='true' title='Friends'></i>Friends <a href='friends.php?view_profile=" . $row_accounts['id'] . "'><i class='fa fa-user' aria-hidden='true'></i> View</a></div></div>";
						}
						elseif ($row_check_friendship['status'] == 2)
						{
							$friend_section .= $row_accounts['noob_tag'] . "<div class='options'><i class='fa fa-ban' aria-hidden='true' title='Blocked'></i>Blocked <a href='friends.php?view_profile=" . $row_accounts['id'] . "'><i class='fa fa-user' aria-hidden='true'></i> View</a></div></div>";
						}
						else
						{
							$friend_section .= $row_accounts['noob_tag'] . "<div class='options'><i class='fa fa-clock-o' aria-hidden='true' title='Friends'></i>Waiting <a href='friends.php?view_profile=" . $row_accounts['id'] . "'><i class='fa fa-user' aria-hidden='true'></i> View</a></div></div>";
						}
						
					}
					else
					{	

						$friend_section .= $row_accounts['noob_tag'] . "<div class='options'><form method='GET'>";
						$friend_section .= "<input type='hidden' name='id' value='" . $row_accounts['id'] . "'/>";
						$friend_section .= "<button type='submit' name='friend_request" . $i . "' value='friend_request'><i class='fa fa-plus-square' aria-hidden='true' title='Add friend'></i> Add</button> &nbsp;";
						$friend_section .= "</form><a href='friends.php?view_profile=" . $row_accounts['id'] . "'><i class='fa fa-user' aria-hidden='true'></i> View</a></div></div>";
					}
				}
			}
			
			
			if ($i != 0)
			{
				echo "<div class='friend-section' style='width: 100%;'>" . $friend_section . "</div>";
			}
			else
			{
				echo "Nothing found";
			}
		}	
	}

	if (strpos($url, "friend_request") !== false)
	{
		$split_url = explode('=', $url);
		$second_split_url = explode('&', $split_url[1]);
		$requested_id = $second_split_url[0];

		mysqli_query($conn, "INSERT INTO friendships (user_one_id, user_two_id, status) VALUES ('" . $id_user . "', '" . $requested_id . "', '0')");
		echo "<meta http-equiv='refresh' content='0; url=friends.php?request_send=" . $requested_id . "'>";
	}
	echo "<br/><h2>Requests</h2><hr class='blue'/><br/>";

	$sql_requests = "SELECT * FROM friendships WHERE user_two_id='" . $id_user . "' AND status='0'";
	$result_requests = mysqli_query($conn, $sql_requests);

	if (!mysqli_num_rows($result_requests))
	{
		echo "No new requests!";
	}
	else
	{	
		$friend_requests = "<div class='friend-section' style='width:100%;'>";
		while ($row_requests = mysqli_fetch_assoc($result_requests)) {
			$result_account_data = mysqli_query($conn, "SELECT noob_tag FROM accounts WHERE id='" . $row_requests['user_one_id'] . "'");
			$row_account_data = mysqli_fetch_assoc($result_account_data);
			
			$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_requests['user_one_id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");
			
			$friend_requests .= "<div class='friend-tab'>";
			if (!mysqli_num_rows($result_profile_image))
			{
				$friend_requests .= "<div class='avatar-thumb'></div>";
			}
			else
			{
				$row_profile_image = mysqli_fetch_assoc($result_profile_image);
				$friend_requests .= "<div class='avatar-thumb' style='background-image: url(../images/avatars/" . $row_profile_image['file_name'] . ");'></div>";
			}

			$friend_requests .= $row_account_data['noob_tag'];
			$friend_requests .= "<div class='options'><form method='POST'>";
			$friend_requests .= "<input type='hidden' name='id' value='" . $row_requests['user_one_id'] . "' />";
			$friend_requests .= "<button type='submit' name='accept' value='accept' style='font-size:13px; font-family:fira-sans;'><i class='fa fa-thumbs-up'></i>Accept</button>";
			$friend_requests .= "</form>";
			$friend_requests .= "</div></div>";
		}
		$friend_requests .= "</div>";

		echo $friend_requests;
	}

	if (isset($_POST['accept']))
	{
		$requester_id = mysqli_real_escape_string($conn, $_POST['id']);

		mysqli_query($conn, "UPDATE friendships SET status='1' WHERE user_one_id='" . $requester_id . "' AND user_two_id='" . $id_user . "'");
		echo "<meta http-equiv='refresh' content='0; url=friends.php'>";
		exit();
	}
	



	echo "</div>";#END OF THE LEFT PART

	if (strpos($url, "view_profile="))
	{	
		//TAKING THE ID
		$id_array = explode("=", $url);
		$id = end($id_array);

		//SELECTING THE USER DATA
		$result_acc_data = mysqli_query($conn, "SELECT * FROM accounts WHERE id='" . $id . "'");
		$row_acc_data = mysqli_fetch_assoc($result_acc_data);

		//STARTING THE FRAGMENT
		echo "<div class='fragment-left fragment-medium' style='border:none;'><h1>" . $row_acc_data['noob_tag'] . "</h1><hr/>";

		//SELECTING PROFILE IMAGES
		$result_profile_background = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE type='0' AND id_user='" . $id . "' ORDER BY id DESC LIMIT 1");

		if (!mysqli_num_rows($result_profile_background))
		{
			echo "<div class='account-background-image' style='background-image: url(../images/backgrounds/default_background.png);'></div>";
		}
		else
		{
			$row_profile_background = mysqli_fetch_assoc($result_profile_background);
			echo "<div class='account-background-image' style='background-image: url(../images/backgrounds/" . $row_profile_background['file_name'] . ");'></div>";
		}

		$result_profile_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE type='1' AND id_user='" . $id . "' ORDER BY id DESC LIMIT 1");

		if (!mysqli_num_rows($result_profile_avatar))
		{
			echo "<a class='avatar' style='background-image: url(../images/avatars/default_avatar.png);'></a>";
		}
		else
		{	
			$row_profile_avatar = mysqli_fetch_assoc($result_profile_avatar);
			echo "<a class='avatar' style='background-image: url(../images/avatars/" . $row_profile_avatar['file_name'] . ");'></a>";
		}

		//ECHOING OUT THE DESCRIPTION
		echo "<br/><h3>Description</h3> ";
		if (empty($row_acc_data['description']))
		{
			echo "None";
		}
		else
		{
			echo str_replace("\\", "", $row_acc_data['description']);
		}

		//FAVOURITE GAMES
		//NOOBIES EARNED
		//ACHIEVMENTS
		$stats = "<br/><br/><h3>Game skills</h3>";
		$result_stats = mysqli_query($conn, "SELECT * FROM accounts WHERE id='" . $id . "'");
		$row_stats = mysqli_fetch_assoc($result_stats);

		

		$stats .= "<div class='text-box'>Air Slaughter Highscore<div class='stat'>" . $row_stats['air_slaughter_highscore'] . "</div></div>";
		$stats .= "<div class='text-box'>Mathrix Highscore<div class='stat'>" . $row_stats['mathrix_highscore'] . "</div></div>";
		$stats .= "<div class='text-box'>Keyboard Ninja Highscore<div class='stat'>" . $row_stats['keyboard_ninja_highscore'] . "</div></div>";

		/*$stats .= ProgressBar($row_stats['current_week_money'], "Current week money");
		$stats .= ProgressBar($row_stats['total_spendings'], "Total spendings");
		$stats .= ProgressBar($row_stats['total_points_gained'], "Total points gained");
		$stats .= ProgressBar($row_stats['current_week_points'], "Current week points");*/
		//AND JUST AS A REMINDER THEY SHOULD BE CIRCLED... WELL UNDER THAT I MEAN THAT WE SHOULD BE MAKING THEM FOR AN EXAMPLE TO CIRCLE NUMER WITH 3 ZEROES THAT WILL HOLD A IDENTIFYING LETTER LIKE k OR SO!!!!

		echo $stats;
		
	}
	echo "</div></body></html>";
