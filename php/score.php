<?php
	session_start();
	require 'dbconnect.php';

	$points = $_POST['points'];
	$id_current_comps = $_POST['idComp'];

	$games = array('air_slaughter_highscore', 'mathrix_highscore', 'keyboard_ninja_highscore');
	//Should add a high score achievment system

	$result_current_comps = mysqli_query($conn, "SELECT * FROM current_comps WHERE id_current_comps='" . $id_current_comps . "'");

	if (!mysqli_num_rows($result_current_comps))
	{
	}
	else
	{	
		$row_current_comps = mysqli_fetch_assoc($result_current_comps);

		if ($row_current_comps['user_points'] < $points)
		{
			mysqli_query($conn, "UPDATE current_comps SET user_points='" . $points . "' WHERE id_current_comps='" . $id_current_comps . "'");

			//SELECTING THE GAME ID
			$result_game_id = mysqli_query($conn, "SELECT game_id FROM comps WHERE id_comp='" . $row_current_comps['id_comp'] . "'");
			$row_game_id = mysqli_fetch_assoc($result_game_id);

			//SELECTING THE PLAYER DATA
			$result_player_data = mysqli_query($conn, "SELECT " . $games[$row_game_id['game_id']-1] . " FROM accounts WHERE id='" . $row_current_comps['id_user'] . "'");
			$row_player_data = mysqli_fetch_assoc($result_player_data);

			if ($row_player_data[$games[$row_game_id['game_id']-1]] < $points)
			{
				mysqli_query($conn, "UPDATE accounts SET " . $games[$row_game_id['game_id']-1] . "=" . $points . " WHERE id='" . $row_current_comps['id_user'] . "'");
			}
		}

		$id_comp = $row_current_comps['id_comp'];
	}

	$result_order = mysqli_query($conn, "SELECT * FROM current_comps WHERE id_comp='" . $id_comp . "'");

	$order = array();
		$i =0;

		while ($row_order = mysqli_fetch_assoc($result_order))
		{
			$order[$row_order['user_points']] = $row_order['id_current_comps'];
			$i++;
		}

		ksort($order);

		$another_counter = 0;
		while ($another_counter <= $i)
		{	
			if ($another_counter == 0)
			{
				mysqli_query($conn, "UPDATE current_comps SET user_pos='1' WHERE id_current_comps='" . end($order) . "'");
			}
			else
			{
				mysqli_query($conn, "UPDATE current_comps SET user_pos='" . ($another_counter + 1) . "' WHERE id_current_comps='" . prev($order) . "'");
			}
			$another_counter++;
		}


