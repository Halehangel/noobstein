<?php
	session_start();
	require '../php/dbconnect.php';
	include '../php/header.php';
	require '../php/functions.php';
	date_default_timezone_set("Europe/Sofia");

	echo 	"<div class'fragment-left'>";

	//SELECTING COMPS ON THE CURRENT DAY AND HOUR
	$sql = "SELECT * FROM comps WHERE start_day='" . date("w") . "'";
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
			//IF THE HOUR ON THE SERVER MATCHES THE ONE IN THE TABLE AND THE START MINUTE IS LESS THAN OR EQUAL TO THE CURRENT ONE ON THE SERVER THEN EXECUTE THE CODE UNDER
			if ($row['start_hour'] == date("G") && $row['start_minute'] <= date("i"))
			{	
				//CHECKING IF SOMEBODY HAS SIGNED UP FOR THE COMP
				$sql_select = "SELECT * FROM comp_stats WHERE id_comp='" . $row['id_comp'] . "'";
				$result_select = $conn->query($sql_select);
				$num_users = mysqli_num_rows($result_select);

				if (!$num_users)
				{
					echo "Nobody has signed up for this comp!";
				}
				elseif ($num_users >= $row['min_players'])
				{

					while ($row_select = $result_select->fetch_assoc())
					{	
						//CHECKING IF THE USER HAS ALREADY ENTERED FOR THE COMP
						$result_check_current = $conn->query("SELECT * FROM current_comps WHERE id_comp='" . $row_select['id_comp'] . "' AND id_user='" . $row_select['id_user'] . "'");

						//AND IF HE HASN'T THEN GO ON AND INSERT THAT ROW OVER THERE
						if (!mysqli_num_rows($result_check_current))
						{
							$sql_insert = "INSERT INTO current_comps (id_comp, id_user, user_pos, user_points) VALUES ('" . $row_select['id_comp'] . "', '" . $row_select['id_user'] . "', '" . $row_select['user_pos'] . "', '" . $row_select['user_points'] . "')";
							$result_insert = $conn->query($sql_insert);

							//AND CLEAN UP THE comp_stats TABLE AFTERWARDS
							mysqli_query($conn, "DELETE FROM comp_stats WHERE id_comp='" . $row_select['id_comp'] . "' AND id_user='" . $row_select['id_user'] . "'");

							//ALSO SEND A NOTIFICATION TO THE FOLLOWING USER
							$result_game_name = mysqli_query($conn, "SELECT name FROM games WHERE id='" . $row['game_id'] . "'");
							$row_game_name = $result_game_name->fetch_assoc();
							
							mysqli_query($conn, "INSERT INTO notifications (id_user, requester, content, dates, seen) VALUES ('" . $row_select['id_user'] . "', 'Team Noobstein', 'Your competition in " . $row_game_name['name'] . " has started! All events can be tracked from <a href=dashboard.php>dashboard!</a>', '" . date('Y-m-d H:i:s') . "', '0')");

							

							//TRANSFERING THE AWARD TO A SEPARATE TABLE

						}
						else
						{
							echo "The user has already signed up for this competition!";
						}	
					}

					//UPDATING THE NUMBER OF ASSIGNED GUYS TO A ZERO FOR THE COMPETITION SO THE THINGS COULD START MOVING ON AGAIN AND AGAIN AND AGAIN :D

					mysqli_query($conn, "INSERT INTO event_money (event_id, award, type, attendees) VALUES ('" . $row['id_comp'] . "', '" . $row['current_award'] . "', '0', '" . $row['num_assigned'] . "')");

					mysqli_query($conn, "UPDATE comps SET num_assigned = '0', current_award = '0' WHERE id_comp='" . $row['id_comp'] . "'");
				}
				else
				{
					$total_refund = (($row['current_award']/15)*17);
					$repayment_each = $total_refund/$num_users;

					while($row_select = mysqli_fetch_assoc($result_select))
					{
						//UPDATING THE CLIENTS ACCOUNT BALANCE
						mysqli_query($conn, "UPDATE account_balance SET fund = fund + '" . $repayment_each ."' WHERE id='" . $row_select['id_user'] . "'");

						//CLEANING THE TABLE
						mysqli_query($conn, "DELETE FROM comp_stats WHERE id_comp='" . $row_select['id_comp'] . "' AND id_user='" . $row_select['id_user'] . "'");
					}
					$poor_admin = $total_refund - $row['current_award'];

					$result_admin = mysqli_query($conn, "SELECT id FROM accounts WHERE user_index='2'");

					$greedy_users = $poor_admin/mysqli_num_rows($result_admin);

					while ($row_admin = mysqli_fetch_assoc($result_admin))
					{
						mysqli_query($conn, "UPDATE account_balance SET fund = fund - '" . $greedy_users . "' WHERE id='" . $row_admin['id'] . "'");
					}
					//TRQBVA DA GO NAPRAVQ DA CHISTI NQKOI RABOTKI :D

					mysqli_query($conn, "UPDATE comps SET num_assigned='0', current_award='0' WHERE id_comp='" . $row['id_comp'] . "'");
				}
			}
		}
	}
	
	echo "</div></body></html>";

	//SHOULD MAKE THE CHECKS TO SEE WHETHER THERE ARE ENOUGH PLAYERS
	//AND AS WELL CLEAN THE NUM ASSIGNED ONES FROM THE COMP :D