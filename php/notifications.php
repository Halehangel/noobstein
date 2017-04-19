<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	include 'class.php';

	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	if ($status == 1)
	{
		echo "<div class='fragment-left fragment-small'><h1>Notifications</h1><hr/>";
		$form_search_notes = "<form method='POST'>";
		$form_search_notes .= "<label><b>Search old notifications</b></label>";
		$form_search_notes .= "<input class='typeWBGin' type='text' name='title' placeholder='Search by title'/>";
		//Probably I should add some searching by dates or so :D
		$form_search_notes .= "<br/><br/><button type='submit' name='search_notes' value='search_notes'><i class='fa fa-search' aria-hidden='true'></i>Search</button>";
		$form_search_notes .= "</form>";

		echo $form_search_notes;
		
		//bind_result instead of get_result
		if (strpos($url, "page="))
		{
			$page = explode("=", $url);
			$current_page = end($page);
		}
		else
		{
			$current_page = 1;
		}

		//Notifications output
		$result = $conn->query("SELECT * FROM notifications WHERE id_user='" . $id_user . "' ORDER BY id DESC");
		$limit = 5;

		$note_counter = 0;
		$num_rows = mysqli_num_rows($result);

		//Initializing the limits of the page
		$top_limit = $current_page * $limit;
		$bottom_limit = ($current_page - 1) * $limit;

		while ($row = $result->fetch_assoc())
		{	
			$note_counter++;

			if ($note_counter > $bottom_limit && $note_counter <= $top_limit)
			{	
				if ($row['seen'] == 0)
				{
					echo "<div class='notifications'><div class='head'>Sent by " . ucfirst(strtolower($row['requester'])) . "<i class='fa fa-eye views' aria-hidden='true'><div class='msg'>Not seen</div></i></div><div class='content'>" . str_replace('\\', '', $row['content']) . "</div><div class='date'>Sent on " . date("M j Y", strtotime($row['dates'])) . " in " . date("H:i", strtotime($row['dates'])) . "</div></div>";
					mysqli_query($conn, "UPDATE notifications SET seen='1' WHERE id='" . $row['id'] . "'");
				}
				else
				{
					echo "<div class='notifications'><div class='head'>Sent by " . ucfirst(strtolower($row['requester'])) . "</div><div class='content'>" . str_replace('\\', '', $row['content']) . "</div><div class='date'>Sent on " . date("M j Y", strtotime($row['dates'])) . " in " . date("H:i", strtotime($row['dates'])) . "</div></div>";
				}
			}	
		}

		$num_pages = round((mysqli_num_rows($result)/$limit), 0, PHP_ROUND_HALF_UP);

		
		echo "<br/>" . Pagination($current_page, $num_pages, "notifications");
		echo "</div>";#Closing the left fragment
	}
	else
	{
		echo "<div class='fragment-left'>Visible only when logged in</div>";
	}
	

