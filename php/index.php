<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	include 'class.php';

	date_default_timezone_set('Europe/Sofia');

	if ($status == 1) {
		$array_random_msg = array("All competitions in Noobstein are based on weekly schedules as each has a maximum length of 4 days!", "Based on what competitions you enter you can win different amounts of money specified in the table", "All competitions can be followed on a later stage from dashboard");
		$sql_select_comps = "SELECT * FROM comps WHERE start_day='" . date("w") . "' ORDER BY end_day DESC, end_hour ASC, end_minute ASC";
		echo "	<div class='fragment-left' style='width:80%; border-right:none'>";

		if ($_SESSION['just_came_in'] == true)
		{
			echo "<h1>Welcome back, " . $username . "!</h1><hr/>";
			$_SESSION['just_came_in'] = false;
		}
		else
		{
			echo "<h1>Competitions</h1><hr/>";
		}
		
		echo "<h3>*" . ucfirst($array_random_msg[array_rand($array_random_msg)]) . "!</h3><br/>";

		$query_select_free_comps = "SELECT tax, num_assigned, start_day FROM comps";
		$result_select_free_comps = $conn->query($query_select_free_comps);
		
		
		$free_comps = 0;
		while ($row_select_free_comps = $result_select_free_comps->fetch_assoc()) {

			if ($row_select_free_comps['tax'] == 0) {
				if ($row_select_free_comps['start_day'] >= date("w") && ($row_select_free_comps['start_day'] - date("w")) <= 3) {
					$free_comps ++;
				} elseif ((6 - $row_select_free_comps['start_day'] + date("w")) <= 3) {
					$free_comps ++;
				}
			}
		}



		/*$free_comp_award = $row_select_all_money['money'] * 0.15;
		$single_free_comp_award = $free_comp_award / $free_comps;*/

		//The other option is trough storing all the money in a table, but I personally think this is smarter and more effective!

		$result_select_comps = $conn->query($sql_select_comps);

		echo "<div class='comp-table comp-head'>Today's competitions</div>";
		echo "<div class='comp-table'>
			<div class='game'><b>Game</b></div>
			<div class='assigned'><b>Subs</b></div>
			<div class='dates'><b>Start time</b></div>
			<div class='profits'><b>Award</b></div>
			<div class='tax'><b>Tax</b></div>
		</div>";
		$i = 0;
		while ($row_select_comps = $result_select_comps->fetch_assoc()) 
		{	
			//An array that holds each day of the week
			$day_of_week = array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');

			$start_day = $day_of_week[$row_select_comps['start_day']];

			$end_day = $day_of_week[$row_select_comps['end_day']];

			
			$input_name = 'pay_tax' . $i;
			$comp_tables_output= "";
			if ($row_select_comps['num_assigned'] == 0) 
			{
				$num_assigned = '0';
			} 
			else 
			{
				$num_assigned = $row_select_comps['num_assigned'];
			}


			$query_game_id = "SELECT name FROM games  WHERE games.id='" . $row_select_comps['game_id'] . "'";

			$result_game_id = $conn->query($query_game_id);
			$row_game_id = $result_game_id->fetch_assoc();


			//starting to fill out the table
			$comp_tables_output .= "<div class='comp-table'>";
			if ($num_assigned < $row_select_comps['min_players'])
			{
				$comp_tables_output .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div> <div class='assigned'><div class='box' style='background-color:grey; border-color:rgb(120,120,120);'>" . $num_assigned . " <i class='fa fa-male' aria-hidden='true'></i></div></div>";
			}
			else
			{
				$comp_tables_output .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div> <div class='assigned'><div class='box'>" . $num_assigned . " <i class='fa fa-male' aria-hidden='true'></i></div></div>";
			}
			
			//Aesthetically forming the end minute
			if ($row_select_comps['start_minute'] >= 0 && $row_select_comps['start_minute'] <= 9)
			{
				$start_minute = "0" . $row_select_comps['start_minute'];
			}
			else
			{
				$start_minute = $row_select_comps['start_minute'];
			}

			if ($row_select_comps['end_day'] > $row_select_comps['start_day'])
			{
				$difference = abs($row_select_comps['start_day'] - $row_select_comps['end_day']);
			}
			else
			{
				$difference = abs(7 + $row_select_comps['end_day'] - $row_select_comps['start_day']);
			}

			if ($difference == 1)
			{
				$difference .= " day";	
			}
			else
			{
				$difference .= " days";
			}

			$comp_tables_output .= "<div class='dates'>&nbsp;<div class='box orange-bg'>" . $start_day . " " . $row_select_comps['start_hour'] . ":" . $start_minute . "</div><div class='additional'>" . $difference . "</div></div>";
			$comp_tables_output .= "<div class='profits'><b> &nbsp;&nbsp;" . $row_select_comps['current_award'] . " \$</b></div>";

			//entry check
			if ($row_select_comps['tax'] > 0) 
			{
				//The payed version
				$query_entry_check = "SELECT id_user FROM comp_stats WHERE id_user='" . $id_user . "' AND id_comp='" . $row_select_comps['id_comp'] . "'";
				$result_entry_check = $conn->query($query_entry_check);
				if ($row_entry_check = $result_entry_check->fetch_assoc()) 
				{
					
					$comp_tables_output .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row_select_comps['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='background-color:rgb(150, 150, 150);border-color:rgb(130,130,130); padding:5px 5px; text-align:right;' disabled='disabled'><i class='fa fa-check' aria-hidden='true'></i></button></form></div>";
				} 
				else 
				{

					$comp_tables_output .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row_select_comps['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px;'>" . $row_select_comps['tax'] . "$</button></form></div>";
				}
			} 
			else 
			{
				//The free one
				$query_entry_check = "SELECT id_user FROM comp_stats WHERE id_user='" . $id_user . "' AND id_comp='" . $row_select_comps['id_comp'] . "'";
				$result_entry_check = $conn->query($query_entry_check);
				if ($row_entry_check = $result_entry_check->fetch_assoc()) 
				{

					$comp_tables_output .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row_select_comps['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='background-color:rgb(150, 150, 150);border-color:rgb(130,130,130); padding:5px 5px; text-align:right;' disabled='disabled'><i class='fa fa-check' aria-hidden='true'></i></button></form></div>";
				} 
				else 
				{
					$comp_tables_output .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row_select_comps['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px;'>Free</button></form></div>";
				}
			}

			//must fix multiple entry
			$comp_tables_output .= "</div>";
			
			echo $comp_tables_output;
			if (isset($_POST['pay_tax'. $i])) 
			{
				//Counting the current award value of the following competition
				$pay_tax = $row_select_comps['tax'];
				$current_award = round(0.75 * $pay_tax, 2, PHP_ROUND_HALF_DOWN);

				//Just for the sake of consistency in the output of the starting minute
				if ($row_select_comps['start_minute'] == 0)
				{
					$row_select_comps['start_minute'] = "00";
				}

				//Generating the message for the succes in signing up output
				$comp_start = $start_day . ' at ' . $row_select_comps['start_hour'] . ':' . $row_select_comps['start_minute'];

				//Checking for enough money and paying
				if($row['fund'] >= $pay_tax) 
				{
					//Updating the account balance
					$row['fund'] -= $pay_tax;
					$query_pay_tax = "UPDATE account_balance SET fund='" . $row['fund'] . "' WHERE id='" . $id_user . "'";
					$result_pay_tax = $conn->query($query_pay_tax);
					
					//Updating the comps table
					$num_players = ++$row_select_comps['num_assigned'];
					$query_update_num_players = "UPDATE comps SET num_assigned='" . $num_players . "', current_award=current_award+'" . $current_award . "' WHERE id_comp='" . $row_select_comps['id_comp'] . "'";
					$result_update_num_players = $conn->query($query_update_num_players);

					//Updating the award in comps table where the entry fee is 0
					$query_select_free_award = "SELECT current_award FROM comps WHERE tax='0'";
					$result_select_free_award = $conn->query($query_select_free_award);
					$whole_free_comp_award = 0;

					while ($row_select_free_award = $result_select_free_award->fetch_assoc()) 
					{
						$whole_free_comp_award += $row_select_free_award['current_award'];
					}

					//Just adding it
					$free_comp_add = round(0.15 * $pay_tax, 2, PHP_ROUND_HALF_DOWN);
					$whole_free_comp_award += $free_comp_add;
					//That's for a single one in 3 days from the following day the user is on
					$free_comp_award = round($whole_free_comp_award/$free_comps, 2, PHP_ROUND_HALF_DOWN);

					//Updating the award of the free comps
					$query_update_free_award = "UPDATE comps SET current_award=current_award+'" . $free_comp_add . "' WHERE tax='0'";
					$result_update_free_award = $conn->query($query_update_free_award);

					//Well after all we are people as well :)
					$left_for_admin = $pay_tax - $current_award - $free_comp_add;
					$query_select_admin_money = "UPDATE account_balance LEFT JOIN accounts ON account_balance.id=accounts.id SET account_balance.fund=account_balance.fund+'" . $left_for_admin ."' WHERE accounts.user_index='2'";
					$result_select_admin_money = $conn->query($query_select_admin_money);

					//Adding another player to the comp_stats one
					$query_insert_comp_stats = "INSERT INTO comp_stats (id_comp, id_user, user_pos, user_points) VALUES ('" . $row_select_comps['id_comp'] . "', '" . $id_user . "', '1', '0')";
					$result_insert_comp_stats = $conn->query($query_insert_comp_stats);

					echo "<meta http-equiv='refresh' content='0; url=index.php?error=success' >";

					//Generating some text so the users could stay warned
				} 
				else 
				{
					//The same here as well
					echo "<meta http-equiv='refresh' content='0; url=index.php?error=fail' >";
				}

			}
			$i++;
		}

		$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if (strpos($url, "error=success"))
		{	
			$msg = "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>You have successfully signed up for competition in " . $comp_start . "! You can track your competions in dashboard!</div></div></div>";
			echo $msg;
		}
		elseif (strpos($url, "error=fail"))
		{
			$msg = "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Not enough money<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>We recommend you try again later or sign up for a free competition!</div></div></div>";
			echo $msg;
		}
		
		//SEARCH BAR
		echo "<div class='comp-search-bar'><h2>Search for a competition</h2><hr/>";
		$form_search_comp = "<form method='POST'>";
		$form_search_comp .= "<div class='column' style='vertical-align:top;'><label><b>Pick a day</b></label>";
		$form_search_comp .= "<br/><select class='white-select' name='day' style='width:180px; padding-right:10px;'>";

		$day_of_week_full = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

		for ($i = 0; $i <= 6; $i++)
		{
			$form_search_comp .= "<option value='" . $i . "'>" . $day_of_week_full[$i] . "</option>";
		}

		$form_search_comp .= "</select></div>";
		$form_search_comp .= "<div class='column' style='vertical-align:top;'><label><b>Price</b></label>";
		$form_search_comp .= "<br/><select class='white-select' id='price' name='price_type' style='width:180px; padding-right:10px;'><option>All</option><option>Free</option><option>Paid</option></select></div>";

		$form_search_comp .= "<div class='column' id='paid' style='width:500px;'><label><b>Please specify a price</b></label>";
		$form_search_comp .= "<br/><input class='typeWBGin' style='width:120px; display:inline;' type='text' name='higher_than' placeholder='Higher than'/>";
		$form_search_comp .= " <input class='typeWBGin' style='width:120px; display:inline;' type='text' name='less_than' placeholder='Less than'/></div>";

		$form_search_comp .= "<br/><br/><button type='submit' name='search_comp' value='search_comp'><i class='fa fa-search' aria-hidden='true'></i> Search</button>";
		$form_search_comp .= "</form></div>";

		echo $form_search_comp;

		//SHOULD MAKE THEM ALL ON ONE ROW SO IT WILL BE MUCH BETTER IN AN AESTHETIC ASPECT

		//SEARCHED

		if (isset($_POST['search_comp']))
		{
			$day = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['day']));
			$price_type = strtolower(htmlspecialchars(mysqli_real_escape_string($conn, $_POST['price_type'])));
			$higher = (float)htmlspecialchars(mysqli_real_escape_string($conn, $_POST['higher_than']));
			$less = (float)htmlspecialchars(mysqli_real_escape_string($conn, $_POST['less_than']));

			switch ($price_type) {
				case 'all':
					$result = mysqli_query($conn, "SELECT * FROM comps WHERE start_day='" . $day . "'");
					break;
				case 'free':
					$result = mysqli_query($conn, "SELECT * FROM comps WHERE start_day='" . $day . "' AND tax='0'");
					break;
				case 'payed':
					$result = mysqli_query($conn, "SELECT * FROM comps WHERE start_day='" . $day . "' AND tax>'" . $higher . "' AND tax<'" . $less . "'");
					break;
			}

			if (!mysqli_num_rows($result))
			{
				echo "<br/>Nothing found that matches your needs";
			}
			else
			{	
				$day_of_week = array('SUN', 'MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT');
				//Otherwise start taking out data
				$comp_table = "<br/><div class='comp-table comp-head'>Your search for " . $day_of_week_full[$day] . "</div>";
				$comp_table .= "<div class='comp-table'>
				<div class='game'><b>Game</b></div>
				<div class='assigned'><b>Subs</b></div>
				<div class='dates'><b>Start time</b></div>
				<div class='profits'><b>Award</b></div>
				<div class='tax'><b>Tax</b></div>
			</div>";
				while ($row = mysqli_fetch_assoc($result))
				{	
				//The limitation dates
				$start_day = $day_of_week[$row['start_day']];
				$end_day = $day_of_week[$row['end_day']];

				$input_name = 'pay_tax' . $i;
				if ($row['num_assigned'] == 0) 
				{
					$num_assigned = '0';
				} 
				else 
				{
					$num_assigned = $row['num_assigned'];
				}


				$query_game_id = "SELECT name FROM games  WHERE games.id='" . $row['game_id'] . "'";

				$result_game_id = $conn->query($query_game_id);
				$row_game_id = $result_game_id->fetch_assoc();


				//starting to fill out the table
				$comp_table .= "<div class='comp-table'>";
				if ($num_assigned < $row['min_players'])
				{
					$comp_table .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div> <div class='assigned'><div class='box' style='background-color:grey; border-color:rgb(120,120,120);'>" . $num_assigned . " <i class='fa fa-male' aria-hidden='true'></i></div></div>";
				}
				else
				{
					$comp_table .= "<div class='game'><b>" . ucfirst(strtolower($row_game_id['name'])) . "</b></div> <div class='assigned'><div class='box'>" . $num_assigned . " <i class='fa fa-male' aria-hidden='true'></i></div></div>";
				}
				
				//Aesthetically forming the end minute
				if ($row['start_minute'] >= 0 && $row['start_minute'] <= 9)
				{
					$end_minute = "0" . $row['start_minute'];
				}
				else
				{
					$end_minute = $row['start_minute'];
				}

				if ($row['end_day'] > $row['start_day'])
				{
					$difference = abs($row['start_day'] - $row['end_day']);
				}
				else
				{
					$difference = abs(7 + $row['end_day'] - $row['start_day']);
				}

				if ($difference == 1)
				{
					$difference .= " day";	
				}
				else
				{
					$difference .= " days";
				}

				$comp_table .= "<div class='dates'>&nbsp;<div class='box orange-bg'>" . $start_day . " " . $row['start_hour'] . ":" . $start_minute . "</div><div class='additional'>" . $difference . "</div></div>";
				$comp_table .= "<div class='profits'><b>" . $row['current_award'] . " \$</b></div>";

				//entry check
				if ($row['tax'] > 0) 
				{
					//The payed version
					$query_entry_check = "SELECT id_user FROM comp_stats WHERE id_user='" . $id_user . "' AND id_comp='" . $row['id_comp'] . "'";
					$result_entry_check = $conn->query($query_entry_check);
					if ($row_entry_check = $result_entry_check->fetch_assoc()) 
					{
						
						$comp_table .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='background-color:rgb(150, 150, 150);border-color:rgb(130,130,130); padding:5px 5px; text-align:right;' disabled='disabled'><i class='fa fa-check' aria-hidden='true'></i></button></form></div>";
					} 
					else 
					{

						$comp_table .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px;'>" . $row['tax'] . "$</button></form></div>";
					}
				} 
				else 
				{
					//The free one
					$query_entry_check = "SELECT id_user FROM comp_stats WHERE id_user='" . $id_user . "' AND id_comp='" . $row['id_comp'] . "'";
					$result_entry_check = $conn->query($query_entry_check);
					if ($row_entry_check = $result_entry_check->fetch_assoc()) 
					{

						$comp_table .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='background-color:rgb(150, 150, 150);border-color:rgb(130,130,130); padding:5px 5px; text-align:right;' disabled='disabled'><i class='fa fa-check' aria-hidden='true'></i></button></form></div>";
					} 
					else 
					{
						$comp_table .= "<div class='tax'><form method='POST'><input type='hidden' name='tax' value='" . $row['tax'] . "'><button type='submit' name='" . $input_name .  "' value='pay_tax' style='bottom:5px;'>Free</button></form></div>";
					}
				}

				//must fix multiple entry
				$comp_table .= "</div>";
				
				if (isset($_POST['pay_tax'. $i])) 
				{
					//Counting the current award value of the following competition
					$pay_tax = $row['tax'];
					$current_award = round(0.75 * $pay_tax, 2, PHP_ROUND_HALF_DOWN);

					//Just for the sake of consistency in the output of the starting minute
					if ($row['start_minute'] == 0)
					{
						$row['start_minute'] = "00";
					}

					//Generating the message for the succes in signing up output
					$comp_start = $start_day . ' at ' . $row['start_hour'] . ':' . $row['start_minute'];

					//Checking for enough money and paying
					if($row['fund'] >= $pay_tax) 
					{
						//Updating the account balance
						$row['fund'] -= $pay_tax;
						$query_pay_tax = "UPDATE account_balance SET fund='" . $row['fund'] . "' WHERE id='" . $id_user . "'";
						$result_pay_tax = $conn->query($query_pay_tax);
						
						//Updating the comps table
						$num_players = ++$row['num_assigned'];
						$query_update_num_players = "UPDATE comps SET num_assigned='" . $num_players . "', current_award=current_award+'" . $current_award . "' WHERE id_comp='" . $row['id_comp'] . "'";
						$result_update_num_players = $conn->query($query_update_num_players);

						//Updating the award in comps table where the entry fee is 0
						$query_select_free_award = "SELECT current_award FROM comps WHERE tax='0'";
						$result_select_free_award = $conn->query($query_select_free_award);
						$whole_free_comp_award = 0;

						while ($row_select_free_award = $result_select_free_award->fetch_assoc()) 
						{
							$whole_free_comp_award += $row_select_free_award['current_award'];
						}

						//Just adding it
						$free_comp_add = round(0.15 * $pay_tax, 2, PHP_ROUND_HALF_DOWN);
						$whole_free_comp_award += $free_comp_add;
						//That's for a single one in 3 days from the following day the user is on
						$free_comp_award = round($whole_free_comp_award/$free_comps, 2, PHP_ROUND_HALF_DOWN);

						//Updating the award of the free comps
						$query_update_free_award = "UPDATE comps SET current_award='" . $free_comp_award . "' WHERE tax='0'";
						$result_update_free_award = $conn->query($query_update_free_award);

						//Well after all we are people as well :)
						$left_for_admin = $pay_tax - $current_award - $free_comp_add;
						$query_select_admin_money = "UPDATE account_balance LEFT JOIN accounts ON account_balance.id=accounts.id SET account_balance.fund=account_balance.fund+'" . $left_for_admin ."' WHERE accounts.user_index='2'";
						$result_select_admin_money = $conn->query($query_select_admin_money);

						//Adding another player to the comp_stats one
						$query_insert_comp_stats = "INSERT INTO comp_stats (id_comp, id_user, user_pos, user_points) VALUES ('" . $row['id_comp'] . "', '" . $id_user . "', '0', '0')";
						$result_insert_comp_stats = $conn->query($query_insert_comp_stats);

						echo "<meta http-equiv='refresh' content='0; url=index.php?error=success' >";

						//Generating some text so the users could stay warned
					} 
					else 
					{
						//The same here as well
						echo "<meta http-equiv='refresh' content='0; url=index.php?error=fail' >";
					}

				}
				$i++;

				}
			}
			echo $comp_table;
		}
			echo "</div>";
		?>
			<script type='text/javascript'>
				setInterval(function () {
					var price = document.getElementById('price');
					var paid = document.getElementById('paid');
					if (price.value == 'Paid')
					{
						paid.style.display = 'inline-block';
					}
					else
					{
						paid.style.display = 'none';
					}
				}, 50);


			</script>
			</body>
		</html>
		<?php
		

		//SEARCHED
	} 
	elseif ($status == 2) 
	{
		//The admin part
		echo "<div class='fragment-left fragment-small'><h1>Admin panel</h1><hr/>";

		//The news form
		$form = "<form method='GET'>";
		$form .= "<input type='hidden' name='publish_date' value='" . date("Y-m-d H:i:s") . "'></input>";
		$form .= "<label><b>News title*</b></label><div style='position:relative; height:20px;'><i class='fa fa-pencil-square-o fa-input'></i><input class='typeWBGin type-small' type='text' name='news_title' placeholder='Title'></div><br/>";
		$form .= "<label><b>Content*</b></label><br/>
					<div style='position:relative; height:20px;'>
						<i class='fa fa-quote-right fa-input' aria-hidden='true'></i>
						<textarea class='typeWBGin type-small' type='text' placeholder='Min 20 symbols and max 300' name='news_content'></textarea>
					</div><br/><br/><br/>";
		$form .= "<button type='submit' name='submit_news' value='submit_news'><i class='fa fa-paper-plane' aria-hidden='true'></i> Publish</button>";
		$form .= "</form>";

		echo $form;

		if (isset($_GET['submit_news']))
		{
			$title = htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $_GET['news_title'])));
			$content = htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $_GET['news_content'])));
			$publish_date = $_GET['publish_date'];

			if (empty($title) || empty($content))
			{
				echo "<meta http-equiv='refresh' content='0; url=index.php?error=news_empty'>";
			}
			else
			{
				if (strlen($content) < 20 || strlen($content) > 300)
				{
					echo "<meta http-equiv='refresh' content='0; url=index.php?error=content_mismatch'>";
				}
				else
				{
					$sql_news = "INSERT INTO news (title, content, date) VALUES ('" . $title . "', '" . $content . "', '" . $publish_date . "')";
					$result_news = mysqli_query($conn, $sql_news);
					echo "<meta http-equiv='refresh' content='0; url=index.php?error=news_success'>";
				}
			}
		}

		$url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if (strpos($url, "error=news_empty"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Empty fields!</div></div>";
		}
		elseif (strpos($url, "error=content_mismatch"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Content mismatch</div><div class='note-content'>The number of symbols in your content does not match the requirements!</div></div>";
		}
		elseif (strpos($url, "error=news_success"))
		{
			echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully updated the news section!</div></div>";
		}
		echo "</div>";

		//Client feedback managing
		echo "<div class='fragment-left fragment-small' style='border:none;'><h1>Client feedback</h1><hr/>";

		//The email comment searching form
		$form_search_feedback = "<form method='POST'>";
		$form_search_feedback .= "<label><b>Search emails</b></label>";
		$form_search_feedback .= "<input class='typeWBGin' type='text' name='email' placeholder='User email'>";
		$form_search_feedback .= "<br/><br/><button type='submit' name='search' value='search'>Search</button>";
		if (strpos($_SERVER['REQUEST_URI'], "search="))
		{
			$form_search_feedback .= " <button style='background-color:rgb(252,55,55);border-color:rgb(216,36,36);' type='submit' name='end_search' value='end_search'><i class='fa fa-times'></i>Close</button>";
		}
		$form_search_feedback .= "</form>";

		echo $form_search_feedback;

		//If searched for a certain user
		if (isset($_POST['search']))
		{	
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));
			if (empty($email))
			{
				echo "<meta http-equiv='refresh' content='0; url=index.php?error=empty_search'>";
			}
			else
			{
				echo "<meta http-equiv='refresh' content='0; url=index.php?search=" . $email . "'>";
			}
		}

		//Ending the search
		if (isset($_POST['end_search']))
		{
			echo "<meta http-equiv='refresh' content='0; url=index.php'>";
		}


		if (strpos($_SERVER['REQUEST_URI'], "search="))
		{	
			//Taking the exact name of the user trough the use of the explode function
			$searched_email = explode("=", $_SERVER['REQUEST_URI']);

			//Making the search
			$sql_comments = "SELECT * FROM feedback WHERE client_access='0' AND email='" . $searched_email[1] ."'";
			$result_comments = mysqli_query($conn, $sql_comments);

			//If there is no match between the db and the search
			if (!mysqli_num_rows($result_comments))
			{
				echo "No new comments from <b>" . $searched_email[1] . "</b>";
			}
			else
			{	
				//If there is then take out the comments of the user
				while ($row_comments = mysqli_fetch_assoc($result_comments))
				{
					$comments .= "<div class='comment'>";

					$comments .= "<div class='head'><img src='../images/avatar_thumb.png'>" . $row_comments['email'] . "</div>";


					$comments .= "<div class='content'>\"" . $row_comments['content'] . "\"</div>";
					$comments .= "<div class='date'>Published on " . $row_comments['date'] . "</div>";
					$comments .= "</div>";
				}

				echo $comments;
			}
		}
		elseif (strpos($_SERVER['REQUEST_URI'], "error=empty_search"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Empty field!</div></div>";
		}
		else
		{
			$sql_comments = "SELECT * FROM feedback WHERE client_access='0' ORDER BY id DESC ";
			$result_comments = mysqli_query($conn, $sql_comments);

			//Deleting comments
			if (strpos($_SERVER['REQUEST_URI'], 'delete='))
			{
				$deleted_id = explode('=', $_SERVER['REQUEST_URI']);
				$sql_deleted_id = "SELECT * FROM feedback WHERE id='" . $deleted_id[1] . "'";
				$result_deleted_id = mysqli_query($conn, $sql_deleted_id);

				while ($row_deleted_id = mysqli_fetch_assoc($result_deleted_id))
				{
					echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Do you really want to delete a comment of <b>" . $row_deleted_id['email'] . "</b>?</div><div class='note-content'>";
					$form_confirm = "<form method='POST'>";
					$form_confirm .= "<input type='hidden' name='id' value='" . $row_deleted_id['id'] . "'>";
					$form_confirm .= "<button type='submit' name='agree' value='agree'>Yes</button>";
					$form_confirm .= " <button type='submit' name='disagree' value='disagree'>No</button>";
					$form_confirm .= " <button type='submit' name='spam' value='spam'>Set as spam</button>";
					$form_confirm .= "</form>";

					echo $form_confirm;
					echo "</div></div>";
				}
			}

			//A message for when a comment is successfully deleted
			if (strpos($_SERVER['REQUEST_URI'], "delete_success="))
			{
				echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully deleted a comment!</div></div>";
			}

			//A message for when a comment is successfully edited
			if (strpos($_SERVER['REQUEST_URI'], "edit_success="))
			{
				echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully edited a comment!</div></div>";
			}

			//Granting permission to the comments
			if (strpos($_SERVER['REQUEST_URI'], 'permit='))
			{
				$permitted_id = explode('=', $_SERVER['REQUEST_URI']);

				$result_permit = mysqli_query($conn, "SELECT email FROM feedback WHERE id='" . $permitted_id[1] . "'");
				$row_permit = mysqli_fetch_assoc($result_permit);

				echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have cuccessfully granted public visibility to a comment of <b>" . $row_permit['email'] . "!</b></div></div>";
			}

			//Editing the comments
			if (strpos($_SERVER['REQUEST_URI'], "edit="))
			{	
				$edit_id = explode('=', $_SERVER['REQUEST_URI']);
				//The editing form
				$form_edit = "<form method='POST'>";
				$form_edit .= "<label><b>Editing a comment</b></label>";
				$form_edit .= "<input type='hidden' name='id' value='" . $edit_id[1] . "'>";

				//Selecting the data of the comment
				$result_comment_edit = mysqli_query($conn, "SELECT * FROM feedback WHERE id='" . $edit_id[1] . "'");
				$row_comment_edit = mysqli_fetch_assoc($result_comment_edit);

				$form_edit .= "<input class='typeWBGin' type='email' name='email' value='" . $row_comment_edit['email'] . "' disabled='disabled'>";
				$form_edit .= "<textarea class='typeWBGin' name='content' autofocus='autofocus'>" . $row_comment_edit['content'] . "</textarea>";
				$form_edit .= "<br/><br/><button type='submit' name='edit_final' value='edit_final'>Edit</button>";
				$form_edit .= "</form>";

				echo $form_edit;

				//Now should make it actually work
			}

			//If you want to delete it
			if (isset($_POST['agree']))
			{
				$id = $_POST['id'];
				mysqli_query($conn, "DELETE FROM feedback WHERE id='" . $id . "'");

				echo "<meta http-equiv='refresh' content='0; url=index.php?delete_success=" . $id . "'>";
			}

			//If you don't want to delete it
			if (isset($_POST['disagree']))
			{
				//Just refresh the page
				echo "<meta http-equiv='refresh' content='0; url=index.php'>";
			}

			//If you want to set a comment as spam
			if (isset($_POST['spam']))
			{
				$id = $_POST['id'];
				mysqli_query($conn, "UPDATE feedback SET client_access='2' WHERE id='" . $id . "'");

				echo "<meta http-equiv='refresh' content='0; url=index.php?spam=" . $id . "'>";
			}

			//If you want to edit a comment
			if (isset($_POST['edit_final']))
			{
				$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
				$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));
				$content = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['content']));

				if (empty($content))
				{
					echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Empty fields!</div></div>";
				}
				else
				{
					if (strlen($content) > 300)
					{
						echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The content shouldn't be longer than 300 symbols!</div></div>";
					}
					else
					{
						mysqli_query($conn, "UPDATE feedback SET content='" . $content . "' WHERE id='" . $id . "'");

						//Echoing success message
						echo "<meta http-equiv='refresh' content='0; url=index.php?edit_success=" . $id . "'>";

						//Should add an edited on date part instead a published one after it is edited, so that means I should just update the date and create an additional table in the db for that!
					}
				}
			}


			//Checking for new comments
			if (!mysqli_num_rows($result_comments))
			{
				echo "No new comments";
			}
			else
			{	
				//Echoing out the new ones
				$comments = "";
				while($row_comments = mysqli_fetch_assoc($result_comments))
				{
					$comments .= "<div class='comment'>";

					$result_id = mysqli_query($conn, "SELECT id FROM accounts WHERE email='" . $row_comments['email'] . "'");
					$row_id = mysqli_fetch_assoc($result_id);

					$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_id['id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

					if (!mysqli_num_rows($result_profile_image))
					{
						$comments .= "<div class='head'><img class='avatar-thumb' src='../images/avatars/avatar_thumb.png'>" . $row_comments['email'] . "<form method='POST'>";
					}
					else
					{
						$row_profile_image = mysqli_fetch_assoc($result_profile_image);
						$comments .= "<div class='head'><img class='avatar-thumb' src='../images/avatars/" . $row_profile_image['file_name'] . "'>" . $row_comments['email'] . "<form method='POST'>";
					}

					

					$comments .= "<input type='hidden' name='id' value='" . $row_comments['id'] ."'>";
					$comments .= "<button type='submit' name='edit' value='edit'><i class='fa fa-pencil'></i></button>";
					$comments .= "<button type='submit' name='delete' value='delete'><i class='fa fa-trash'></i></button>";
					$comments .= "<button type='submit' name='permit' value='permit'><i class='fa fa-thumbs-up'></i></button>";
					$comments .= "</form></div>";
					$comments .= "<div class='content'>\"" . $row_comments['content'] . "\"</div>";
					$comments .= "<div class='date'>Sent on " . date("M d Y H:i", $row_comments['date']) . "</div>";
					$comments .= "</div>";
				}

				echo $comments;
			}

			//Deleting a comment from the db
			if (isset($_POST['delete']))
			{	
				$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
				//mysqli_query($conn, "DELETE FROM feedback WHERE id='" . $id . "'");
				echo "<meta http-equiv='refresh' content='0; url=index.php?delete=" . $id . "'>";
			}

			//Permitting a comment and making it visible
			if (isset($_POST['permit']))
			{
				$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));

				mysqli_query($conn, "UPDATE feedback SET client_access='1' WHERE id='" . $id . "'");

				echo "<meta http-equiv='refresh' content='0; url=index.php?permit=" . $id . "'>";
			}

			//Editing button
			if (isset($_POST['edit']))
			{
				$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));

				echo "<meta http-equiv='refresh' content='0; url=index.php?edit=" . $id . "'>";
			}
			
		}


		echo "</div>";
	}
	else
	{	
		//If not logged in
		echo "<div class='fragment-left'>";
		echo 'This information is visible only when logged in!';
		echo "</div>";
	}
