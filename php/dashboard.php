<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	include 'class.php';

	if ($status == 0) 
	{
		echo "<div class='fragment-left'>";	
		echo 'This information is visible only when logged in!';
		echo "</div>";		
	} 
	elseif ($status == 1) 
	{

		/*
		==================================================
		UPCOMMING
		==================================================
		*/
		$sql_select_comps = "SELECT * FROM comps LEFT JOIN comp_stats ON comps.id_comp=comp_stats.id_comp WHERE comp_stats.id_user='" . $id_user . "'";
		$result_select_comps = $conn->query($sql_select_comps);

		//Starting to build up the page
		echo "<div class='fragment-left' style='width:80%; border-right:none'>
		<h1>Dashboard</h1><hr/><br/>";
		echo "<div class='comp-table comp-head'>Upcoming competitions</div>";
		echo "<div class='comp-table'>
			<div class='game'><b>Game</b></div>
			<div class='assigned'><b>Subs</b></div>
			<div class='dates'><b>Start date</b></div>
			<div class='profits'><b>Award</b></div>
			<div class='tax'><b>Score</b></div>
		</div>";

		//Instancing a counter just to give the rows buttons some unique names
		

		if (!mysqli_num_rows($result_select_comps))
		{
			echo "<div class='comp-table'>No upcomming events...<a class='button' href='index.php' style='top:-3px;float:right;'>Sign up</a></div>";
		}
		else
		{	
			$i = 0;
			//Checking the day of the week and referring a string to it
			while ($row_select_comps = $result_select_comps->fetch_assoc()) 
			{

				$day_of_week = array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');
				$start_day = $day_of_week[$row_select_comps['start_day']];
				$end_day = $day_of_week[$row_select_comps['end_day']];

				//Generating the unique name for the view button
				$input_name = 'view' . $i;

				//Starting the string for the output of the data in the rows
				$comp_tables_output= "";

				//Checking the number of the assigned users for the competition
				$num_assigned = $row_select_comps['num_assigned'];

				//Selecting the game name trough the use of the comp id
				$query_game_id = "SELECT name FROM games  WHERE games.id='" . $row_select_comps['game_id'] . "'";
				$result_game_id = $conn->query($query_game_id);
				$row_game_id = $result_game_id->fetch_assoc();


				//Starting to fill out the table
				$comp_tables_output .= "<div class='comp-table'>";
				$comp_tables_output .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div>";

				//Change in the color if there are enough players to start the competition
				if ($num_assigned >= $row_select_comps['min_players']) 
				{
					$comp_tables_output .= "<div class='assigned'><div class='box'>" . $num_assigned . " <i class='fa fa-male'></i></div></div>";
				} 
				else 
				{
					$comp_tables_output .= "<div class='assigned'><div class='box' style='background-color:rgb(150,150,150); border-color:rgb(130,130,130);'>" . $num_assigned . " <i class='fa fa-male'></i></div></div>";
				}

				//Properly formatting the start minute
				if ($row_select_comps['start_minute'] >= 0 && $row_select_comps['start_minute'] < 10)
				{
					$start_minute = '0' . $row_select_comps['start_minute'];
				}
				else
				{
					$start_minute = $row_select_comps['start_minute'];
				}

				$comp_tables_output .= "<div class='dates'>&nbsp;&nbsp;<div class='box orange-bg'>" . $start_day . " " . $row_select_comps['start_hour'] . ":" . $start_minute . "</div></div>";
				//Selecting the award
				$comp_tables_output .= "<div class='profits'>&nbsp;&nbsp;&nbsp;<b>" . $row_select_comps['current_award'] . " \$</b></div>";

				//Button to check the stats
				$comp_tables_output .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row_select_comps['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px; padding-right:0px;padding-left:6px; text-align=center;'><i class='fa fa-caret-down' aria-hidden='true'></i></button></form></div>";
				$comp_tables_output .= "</div>";

				echo $comp_tables_output;

				//Viewing out the temporary scores
				if(isset($_POST['view' . $i])) 
				{
					//Selecting the stats in the competition
					$sql_select_score = "SELECT * FROM comp_stats WHERE id_comp='" . $row_select_comps['id_comp'] . "'  ORDER BY user_points DESC";
					$result_select_score = $conn->query($sql_select_score);

					//A counter for the positions in the competition
					$pos = 1;

					$current_score = "<div onclick='HidePopup();' class='modal' style='display:block;'><div class='close-comp' onclick='HidePopup();'><i class='fa fa-times'></i></div><div class='comp-score'>";
					$current_score .= "<div class='row'><div class='pos'>Pos</div><div class='noob-tag'>Name</div><div class='score'>Score</div></div>";
					//Echoing out the data about the current competition
					while ($row_select_score = $result_select_score->fetch_assoc()) 
					{

						//Selecting player name in the row
						$sql_select_player = "SELECT noob_tag FROM accounts WHERE id='" . $row_select_score['id_user'] . "'";
						$result_select_player = $conn->query($sql_select_player);
						$row_select_player = $result_select_player->fetch_assoc();

						//Filling up the rows of the table
						//Must change the design or at least the colors just because it's so crappy...
						$current_score .= "<div class='row'>";
						$current_score .= "<div class='pos'>" . $pos . "</div>";
						$current_score .= "<div class='noob-tag'><b>" . $row_select_player['noob_tag'] . "</b></div>";
						$current_score .= "<div class='score'>" . $row_select_score['user_points'] . " points</div>";
						$current_score .= "</div>";
						
						

						//Incrementing the position
						$pos++;
					}
					$current_score .= "</div></div>";
					echo $current_score;
				}

				//Incrementing the counter troughout the use of which we generate the unique button name
				$i++;
			}
		}
		

		/*
		==================================================
		CURRENT COMPETITIONS
		==================================================
		*/

		//Starting to build up the page
		$sql_current = "SELECT * FROM comps LEFT JOIN current_comps ON comps.id_comp=current_comps.id_comp WHERE current_comps.id_user='" . $id_user . "'";
		$result_current = $conn->query($sql_current);
		echo "<br/><br/><div class='comp-table comp-head'>Current competitions</div>";
		echo "<div class='comp-table'>
			<div class='game'><b>Game</b></div>
			<div class='assigned'><b>Subs</b></div>
			<div class='dates'><b>End date</b></div>
			<div class='profits'><b>Award</b></div>
			<div class='tax'><b>Score</b></div>
		</div>";

		//Instancing a counter just to give the rows buttons some unique names
		

		if (!mysqli_num_rows($result_current))
		{
			echo "<div class='comp-table'>No current events...</div>";
		}
		else
		{
			$i = 0;
			//Checking the day of the week and referring a string to it
			while ($row_current = $result_current->fetch_assoc()) 
			{

				$day_of_week = array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');
				$start_day = $day_of_week[$row_current['start_day']];
				$end_day = $day_of_week[$row_current['end_day']];

				//Generating the unique name for the view button
				$input_name = 'view_current' . $i;

				//Starting the string for the output of the data in the rows
				$comp_tables_output= "";

				$result_current_data = mysqli_query($conn, "SELECT * FROM event_money WHERE event_id='" . $row_current['id_comp'] . "' AND type='0' ORDER BY id DESC LIMIT 1");
				$row_current_data = mysqli_fetch_assoc($result_current_data);
				//Checking the number of the assigned users for the competition
				$num_assigned = $row_current_data['attendees'];

				//Selecting the game name trough the use of the comp id
				$query_game_id = "SELECT name FROM games  WHERE games.id='" . $row_current['game_id'] . "'";
				$result_game_id = $conn->query($query_game_id);
				$row_game_id = $result_game_id->fetch_assoc();


				//Starting to fill out the table
				$comp_tables_output .= "<div class='comp-table'>";
				$comp_tables_output .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div>";



				//Change in the color if there are enough players to start the competition
				if ($num_assigned >= $row_current['min_players']) 
				{
					$comp_tables_output .= "<div class='assigned'><div class='box'>" . $num_assigned . " <i class='fa fa-male'></i></div></div>";
				} 
				else 
				{
					$comp_tables_output .= "<div class='assigned'><div class='box' style='background-color:rgb(150,150,150); border-color:rgb(130,130,130);'>" . $num_assigned . " <i class='fa fa-male'></i></div></div>";
				}
				//Formatting the end minute
				if ($row_current['end_minute'] >= 0 && $row_current['end_minute'] < 10)
				{
					$end_minute = '0' . $row_current['end_minute'];
				}
				else
				{
					$end_minute = $row_current['end_minute'];
				}
				$comp_tables_output .= "<div class='dates'>&nbsp;&nbsp;<div class='box orange-bg'>" . $end_day . " " . $row_current['end_hour'] . ":" . $end_minute . "</div></div>";
				//Selecting the award

				
				$comp_tables_output .= "<div class='profits'>&nbsp;&nbsp;&nbsp;<b>" . $row_current_data['award'] . " \$</b></div>";

				//SELECTING GAME LINK
				$result_game_id = mysqli_query($conn, "SELECT game_id FROM comps WHERE id_comp='" . $row_current['id_comp'] . "'");
				$row_game_id = mysqli_fetch_assoc($result_game_id);

				$result_game_link = mysqli_query($conn, "SELECT link FROM games WHERE id='" . $row_game_id['game_id'] . "'");
				$row_game_link = mysqli_fetch_assoc($result_game_link);

				$link = explode('?', $row_game_link['link']);
				$exact_link = $link[0];
				//Button to check the stats
				$comp_tables_output .= "<div class='tax' style='width: auto;'><form method='POST'><a class='button orange-button' href='" . $exact_link . "?current_comp=" . $row_current['id_current_comps'] . "' style='bottom: 5px; padding-top:2px; padding-bottom:2px;'><i class='fa fa-gamepad' aria-hidden='true'></i> Play</a> &nbsp;&nbsp;<input type='hidden' name='tax' value='" . $row_current['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px; padding-right:0px;padding-left:6px; text-align=center;'><i class='fa fa-caret-down' aria-hidden='true'></i></button></form></div>";
				$comp_tables_output .= "</div>";

				echo $comp_tables_output;

				//Viewing out the temporary scores
				if(isset($_POST['view_current' . $i])) 
				{
					//Selecting the stats in the competition
					$sql_score = "SELECT * FROM current_comps WHERE id_comp='" . $row_current['id_comp'] . "'  ORDER BY user_points DESC";
					$result_score = $conn->query($sql_score);

					//A counter for the positions in the competition
					$pos = 1;

					$current_score = "<div onclick='HidePopup();' class='modal' style='display:block;'><div class='close-comp' onclick='HidePopup();'><i class='fa fa-times'></i></div><div class='comp-score'>";
					$current_score .= "<div class='row'><div class='pos'>Pos</div><div class='noob-tag'>Name</div><div class='score'>Score</div></div>";
					//Echoing out the data about the current competition
					while ($row_score = $result_score->fetch_assoc()) 
					{
						//Selecting player name in the row
						$sql_select_player = "SELECT noob_tag FROM accounts WHERE id='" . $row_score['id_user'] . "'";
						$result_select_player = $conn->query($sql_select_player);
						$row_select_player = $result_select_player->fetch_assoc();

						//Filling up the rows of the table
						//Must change the design or at least the colors just because it's so crappy...
						$current_score .= "<div class='row'>";
						$current_score .= "<div class='pos'>" . $pos . "</div>";
						$current_score .= "<div class='noob-tag'><b>" . $row_select_player['noob_tag'] . "</b></div>";
						$current_score .= "<div class='score'>" . $row_score['user_points'] . " points</div>";
						$current_score .= "</div>";
						
						

						//Incrementing the position
						$pos++;
					}
					$current_score .= "</div></div>";
					echo $current_score;
				}

				//Incrementing the counter troughout the use of which we generate the unique button name
				$i++;
			}
		}

		
?>

	</div>

	<script type="text/javascript">
		//Used for the countdown function trough the use of which we reload the page
	    var timeleft = 10;
	    var downloadTimer = setInterval(function(){
	    timeleft--;
	    document.getElementById("countdowntimer").textContent = timeleft;
	    if(timeleft <= 0) {
	        clearInterval(downloadTimer);
	        window.location = 'index.php';
	    }
	    },1000);
	    function HidePopup() {
			document.getElementsByClassName('modal')[0].style.display='none';
		}

	</script>
	</body>
</html>

<?php
	} elseif ($status == 2) {#For admin panel
	echo"<div class='fragment-left'>";

	echo 'You have logged as an admin!';

	echo "</div>";

	}
