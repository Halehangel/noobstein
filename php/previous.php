<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	include 'class.php';

	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

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
		$sql_select_comps = "SELECT * FROM past_comps LEFT JOIN comps ON past_comps.id_comp=comps.id_comp WHERE past_comps.id_user='" . $id_user . "' ORDER BY id DESC";
		$result_select_comps = $conn->query($sql_select_comps);

		//Starting to build up the page
		echo "<div class='fragment-left' style='width:80%; border-right:none'>
		<h1>Previous competitions</h1><hr/><br/>";
		echo "<div class='comp-table comp-head'>Upcoming competitions</div>";
		echo "<div class='comp-table'>
			<div class='game'><b>Game</b></div>
			<div class='dates'><b>Ended on</b></div>
			<div class='profits'><b>Award</b></div>
			<div class='profits'><b>Points</b></div>
			<div class='tax'><b>Position</b></div>
		</div>";

		//Instancing a counter just to give the rows buttons some unique names
		

		if (!mysqli_num_rows($result_select_comps))
		{
			echo "<div class='comp-table'>No upcomming events...<a class='button' href='index.php' style='top:-3px;float:right;'>Sign up</a></div>";
		}
		else
		{	
			//Taking the number of the current page
			if (strpos($url, "page="))
			{
				$page = explode("=", $url);
				$current_page = end($page);
			}
			else
			{
				$current_page = 1;
			}

			//Initializing a limit for the num of rows on a page
			$limit = 12;

			//Row counter
			$i = 0;

			//Initializing the limits of the page
			$top_limit = $current_page * $limit;
			$bottom_limit = ($current_page - 1) * $limit;

			//Checking the day of the week and referring a string to it
			while ($row_select_comps = $result_select_comps->fetch_assoc()) 
			{
				$i++;#Incrementing that counter

				if ($i > $bottom_limit && $i <= $top_limit)
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

					$comp_tables_output .= "<div class='dates'>&nbsp;&nbsp;<div class='box orange-bg'>" . date("M d H:i", strtotime($row_select_comps['dates'])) . "</div></div>";
					//Selecting the award
					$comp_tables_output .= "<div class='profits' style='text-align:center;'><b>" . round($row_select_comps['award'], 2, PHP_ROUND_HALF_DOWN) . " \$</b></div>";
					$comp_tables_output .= "<div class='profits' style='text-align:center;'>" . $row_select_comps['user_points'] . "</div>";

					//Button to check the stats
					$comp_tables_output .= "<div class='tax'><b>" . $row_select_comps['user_pos'];
					if ($row_select_comps['user_pos'] <= 3)
					{
						$comp_tables_output .= " <i class='fa fa-trophy'></i>";
					}
					$comp_tables_output .= "</b></div>";
					$comp_tables_output .= "</div>";

					echo $comp_tables_output;
				}
			}
		}

		$num_pages = round((mysqli_num_rows($result_select_comps)/$limit), 0, PHP_ROUND_HALF_UP);
		echo Pagination($current_page, $num_pages, "previous");
		

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
