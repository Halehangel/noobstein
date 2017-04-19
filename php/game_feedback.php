<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	$game_id = $_GET['game'];
	if (isset($_GET['game']))
	{
		$_SESSION['game'] = $game_id;
	}


	$game_array = array('Air Slaughter', 'Mathrix', 'Keyboard Ninja', 'Street Pong', 'El Doom De Barrage');

	echo "<div class='fragment-left fragment-small'><h1>Game commenting</h1><hr/>";
	$form = "<form method='GET'>";
	$form .= "<textarea class='typeWBGin' name='comment_text' placeholder='Type a comment...' /></textarea>";
	$form .= "<select class='white-select select-small' name='game_id'>";

	foreach ($game_array as $key => $value) 
	{
		if (($key+1) == $game_id)
		{
			$form .= "<option value='" . ($key+1) . "' selected='selected'>" . $value . "</option>";
		}
		else
		{
			$form .= "<option value='" . ($key+1) . "'>" . $value . "</option>";
		}
	}
	$form .= "</select>";

	$form .= "<br/><br/><button type='submit' name='submit_comment' value='submit_comment'><i class='fa fa-paper-plane' aria-hidden='true'></i> Post</button>";
	$form .= "</form>";

	echo $form;

	if (isset($_GET['submit_comment']))
	{	
		date_default_timezone_set("Europe/Sofia");
		$content = mysqli_real_escape_string($conn, $_GET['comment_text']);
		$game_id = mysqli_real_escape_string($conn, $_GET['game_id']);

		mysqli_query($conn, "INSERT INTO game_comments (id_game, content, id_user, dates) VALUES ('" . $game_id . "', '" . $content . "', '" . $id_user . "', '" . date("Y-m-d H:i:s") . "')");

		if ($_SESSION['game'] != 0)
		{	
			$result_game_link = mysqli_query($conn, "SELECT link FROM games WHERE id='" . $_SESSION['game'] . "'");
			$row_game_link = mysqli_fetch_assoc($result_game_link);

			echo "<meta http-equiv='refresh' content='0; url=" . $row_game_link['link'] . "'>";
			$_SESSION['game'] = 0;
		}
		//Should add some sessions
	}

	echo "</div>";

	