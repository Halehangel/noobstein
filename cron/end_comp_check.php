<?php
	session_start();
	require '../php/dbconnect.php';
	include '../php/header.php';
	require '../php/functions.php';
	date_default_timezone_set("Europe/Sofia");

	echo 	"<div class'fragment-left'>";

	//SELECTING COMPS ON THE CURRENT DAY AND HOUR
	$sql = "SELECT * FROM comps WHERE end_day='" . date("w") . "'";
	$result = $conn->query($sql);

	//IF FOUND THEN GO ON AND DO THE FOLLOWING SCRIPT
	if (!mysqli_num_rows($result))
	{
		echo "Nothing found!";
	}
	else
	{	
		while ($row = $result->fetch_assoc())
		{	
			//IF THE HOUR ON THE SERVER MATCHES THE ONE IN THE DB AND THE CURRENT MINUTE IS BIGGER THAN THE SPECIFIED IN THE IF STATEMENT THEN EXECUTE THE CODE BELLOW
			if ($row['end_hour'] == date("G") && $row['end_minute'] <= date("i"))
			{
				$sql_select = "SELECT * FROM current_comps WHERE id_comp='" . $row['id_comp'] . "'";
				$result_select = $conn->query($sql_select);

				if (!mysqli_num_rows($result_select))
				{
					echo "There is no such comp in progress right now!";
				}
				else
				{	
					$num_players = mysqli_num_rows($result_select);
					$num_victorious = round(($num_players/2), 0, PHP_ROUND_HALF_UP);
					

					$array_players = array();

					while ($row_select = $result_select->fetch_assoc())
					{	
						//SO IF FOUND INSERT THEM INTO THE PAST COMPS TABLE
						mysqli_query($conn, "INSERT INTO past_comps (id_comp, id_user, user_pos, user_points, 	dates) VALUES ('" . $row_select['id_comp'] . "', '" . $row_select['id_user'] . "', '" . $row_select['user_pos'] . "', '" . $row_select['user_points'] . "', '" . date('Y-m-d H:i:s') . "')");

						//AND THEN CLEAN UP THE TABLE OF THE CURRENT COMPS
						mysqli_query($conn, "DELETE FROM current_comps WHERE id_comp='" . $row_select['id_comp'] . "' AND id_user='" . $row_select['id_user'] . "'");

						//ALSO SEND A NOTIFICATION TO THE FOLLOWING USER
						$result_game_name = mysqli_query($conn, "SELECT name FROM games WHERE id='" . $row['game_id'] . "'");
						$row_game_name = $result_game_name->fetch_assoc();
						
						mysqli_query($conn, "INSERT INTO notifications (id_user, requester, content, dates, seen) VALUES ('" . $row_select['id_user'] . "', 'Team Noobstein', 'Your competition in " . $row_game_name['name'] . " has ended! All past events can be tracked from our <a href=dashboard.php>previous</a> module!', '" . date('Y-m-d H:i:s') . "', '0')");

						$array_players[$row_select['id_user']] = $row_select['user_points'];
						//RAZPREDELENIE NA PARITE I SUOBSTENIE
					}

					$i = 0;
					
					$result_pick_award = mysqli_query($conn, "SELECT award FROM event_money WHERE event_id='" . $row['id_comp'] . "' AND type='0'");

					$row_pick_award = mysqli_fetch_assoc($result_pick_award);
					$award_per_each = round(($row_pick_award['award']/$num_victorious), 2, PHP_ROUND_HALF_DOWN);

					//DELETING IT AFTERWARDS

					//ALOCATING THE MONEY OF EACH USER
					while ($i < $num_victorious)
					{	
						$top_player = array_search(max($array_players), $array_players);
						mysqli_query($conn, "UPDATE account_balance SET fund = fund + '$award_per_each', total_profits = total_profits + '$award_per_each' WHERE id='" . $top_player . "'");

						//SELECTING THE USER NAME OF THE LUCKY PERSON
						$result_user_name = mysqli_query($conn, "SELECT first_name, last_name FROM accounts WHERE id='" . $top_player . "'");
						$row_user_name = mysqli_fetch_assoc($result_user_name);

						mysqli_query($conn, "INSERT INTO social_events (id_user, content, type, dates) VALUES ('" . $top_player . "', '<b>" . ucfirst(strtolower($row_user_name['first_name'])) . " " . ucfirst(strtolower($row_user_name['last_name'])) . "</b> won <b><i>" . $award_per_each . " $</i></b> in a game of <b>" . $row_game_name['name'] . "</b>!', '1', '" . date('Y-m-d H:i:s') . "')");

						mysqli_query($conn, "INSERT INTO notifications (id_user, requester, content, dates, seen) VALUES ('" . $top_player . "', 'Team Noobstein', 'You have won " . $award_per_each . " in a competition of " . $row_game_name['name'] . "!', '', '0')");

						$result_last_attendance = mysqli_query($conn, "SELECT id FROM past_comps WHERE id_user='" . $top_player . "' ORDER BY id DESC LIMIT 1");
						$row_last_attendance = mysqli_fetch_assoc($result_last_attendance);
						mysqli_query($conn, "UPDATE past_comps SET award='" . $award_per_each . "' WHERE id='" . $row_last_attendance['id'] . "'");
						//Should make something like turning of the notifications and not

						unset($array_players[$top_player]);
						$i++;
					}

					mysqli_query($conn, "UPDATE comps SET current_award = '0' WHERE id_comp='" . $row['id_comp'] . "'");
					//Should clean out the num assigned from the date_check.php and the current_award from over here
					//Now left to output something like a social status that could be viewed by other players
					//Should make a separate table for storing event money and another one called event type that is going to be connected both to the social events one and the current comps one
					mysqli_query($conn, "DELETE FROM event_money WHERE event_id='" . $row['id_comp'] . "' AND type='0'");
				}
			}
			else
			{
				echo "Nothing available for the current moment!";
			}
		}
	}

	echo "</div></body></html>";