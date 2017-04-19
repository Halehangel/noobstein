<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	if ($status == 0) {

		echo "<div class='fragment-left'>";
		echo "<b>This content is not ment for you!</b>";
		echo "</div></body></html>";

	} elseif ($status == 1) {
		$sql_select_update_data = "SELECT phone_number, description FROM accounts WHERE id='" . $id_user . "'";
		$result_select_update_data = $conn->query($sql_select_update_data);
		$row = $result_select_update_data->fetch_assoc();

		$result_bg = mysqli_query($conn, "SELECT * FROM profile_images WHERE id_user='" . $id_user ."' AND type='0' ORDER BY id DESC LIMIT 1");
		$result_avatar = mysqli_query($conn, "SELECT * FROM profile_images WHERE id_user='" . $id_user . "' AND type='1' ORDER BY id DESC LIMIT 1");

		if (!mysqli_num_rows($result_bg))
		{
			$image_form = "<div class='account-background-image' style='background-image: url(../images/backgrounds/default_background.png);'>";
			$image_form .= "<a href='settings.php?edit=background' style='color:rgb(70,70,70)'><i class='fa fa-camera' aria-hidden='true'></i></a>";#Doing this just for the colour
		}
		else
		{	
			$row_bg = mysqli_fetch_assoc($result_bg);#Just to take the bg image

			$image_form = "<div class='account-background-image' style='background-image: url(../images/backgrounds/" . $row_bg['file_name'] . ");'>";
			$image_form .= "<a href='settings.php?edit=background' style='color:white'><i class='fa fa-camera' aria-hidden='true'></i></a>";
		}

		
		$image_form .= "</div><br/><br/>";

		if (!mysqli_num_rows($result_avatar))
		{
			$avatar_form = "<a class='avatar' href='settings.php?edit=avatar' style='background-image:url(../images/avatars/default_avatar.png);'></a>";
		}
		else
		{	
			$row_avatar = mysqli_fetch_assoc($result_avatar);

			$avatar_form = "<a class='avatar' href='settings.php?edit=avatar' style='background-image:url(../images/avatars/" . $row_avatar['file_name'] . ");'></a>";
		}
		


		
		$account_update_data_form = "<form method='POST'>" ;
		$account_update_data_form .= "<label><b>Phone number</b></label>";
		$account_update_data_form .= "<input class='typeWBGin orange' type='text' placeholder='Between 10 and 16 numbers' name='phone_number' value='" . $row['phone_number'] . "'/>";
		$account_update_data_form .= "<label><b>Description</b></label>";
		$account_update_data_form .= "<textarea class='typeWBGin orange' type='text' placeholder='Between 10 and 16 numbers' name='description'>" . $row['description'] . "</textarea>";
		$account_update_data_form .= "<br/><br/>
			<button type='submit' name='submit_update' value='submit_update'>Update</button><br/>
		</form>";
		echo "<div class='fragment-left'>
		<h1>Account setup</h1><hr/>";
		echo $image_form;
		echo $avatar_form;

		//Whenever the user clicks on the camera icon to change the background
		if (strpos($url, "edit=background"))
		{
			echo "<div class='note-popup'><a class='close-comp' href='settings.php'><i class='fa fa-times'></i></a><div class='image-edit'>";
			echo "<div class='image'>";
			if (!mysqli_num_rows($result_bg))
			{
				echo "<img src='../images/backgrounds/default_background.png' style='max-width:700px;'><br/>Currently you have no background set!";
			}
			else
			{
				echo "<img src='../images/backgrounds/" . $row_bg['file_name'] . "' style='max-width:700px;'>";
			}
			
			echo "</div>";
			echo "<div class='gadgets'><h1>Wallpaper</h1><hr/>*This is a simple way to represent your interests in front of other users<br/>";

			$bg_form = "<form method='POST' enctype='multipart/form-data'>";
			$bg_form .= "<input type='hidden' name='image_type' value='0'>";
			$bg_form .= "<label><b>Select a file</b></label>";
			//Input
			$bg_form .= "<br/><br/><div class='file-input'>";
			$bg_form .= "<span class='input-value'>Browse</span>";
			$bg_form .= "<input type='file' name='image' required='required'/>";
			$bg_form .= "</div>";

			$bg_form .= "<br/><br/><button type='submit' name='upload_image' value='upload_image' class='orange-button'>Update</button>";

			$bg_form .= "</form>";
			echo $bg_form;
			echo "<br/><hr class='blue'>Some content is going to be put over here... I just still don't know what!";
			ImageUpload($conn, $id_user, 5000000);
			echo "</div></div></div>";
		}
		elseif (strpos($url, "edit=avatar")) {
			echo "<div class='note-popup'><a class='close-comp' href='settings.php'><i class='fa fa-times'></i></a><div class='image-edit'>";
			echo "<div class='image'>";
			if (!mysqli_num_rows($result_avatar))
			{
				echo "<img src='../images/avatars/default_avatar.png' style='max-width:700px;'><br/>Currently you have no background set!";
			}
			else
			{
				echo "<img src='../images/avatars/" . $row_avatar['file_name'] . "' style='max-width:700px;'>";
			}
			
			echo "</div>";
			echo "<div class='gadgets'><h1>Avatar</h1><hr/>*This is a simple way to represent your interests in front of other users<br/>";

			$bg_form = "<form method='POST' enctype='multipart/form-data'>";
			$bg_form .= "<input type='hidden' name='image_type' value='1'>";
			$bg_form .= "<label><b>Select a file</b></label>";
			//Input
			$bg_form .= "<br/><br/><div class='file-input'>";
			$bg_form .= "<span class='input-value'>Browse</span>";
			$bg_form .= "<input type='file' name='image' required='required'/>";
			$bg_form .= "</div>";

			$bg_form .= "<br/><br/><button type='submit' name='upload_image' value='upload_image' class='orange-button'>Update</button>";

			$bg_form .= "</form>";
			echo $bg_form;
			echo "<br/><hr class='blue'>Some content is going to be put over here... I just still don't know what!";
			ImageUpload($conn, $id_user, 5000000);
			echo "</div></div></div>";
		}
		

		
		

		echo $account_update_data_form;
		echo $_SESSION['succes_text'];
		$_SESSION['succes_text'] = '';

		$phone_number = strip_tags($_POST['phone_number']);
		$description = strip_tags($_POST['description']);
		if (isset($_POST['submit_update'])) 
		{
			if (strlen($description)<= 300) 
			{
				if (!$phone_number) 
				{
					$sql_update = "UPDATE accounts SET description='" . $description . "' WHERE id='" . $id_user . "'";
					$result = $conn->query($sql_update);
					echo '<meta http-equiv="refresh" content="0; url=settings.php" >';
					$_SESSION['succes_text'] = "<div class='succes'><div class='exclamation-sign-yellow'>!</div><p class='notification-text'>You have succesfully updated your account information!</p></div>";

				} 
				elseif (strlen($phone_number) >= 10 && strlen($phone_number) <= 16) 
				{
					$sql_update = "UPDATE accounts SET phone_number='" . $phone_number . "', description='" . $description . "' WHERE id='" . $id_user . "'";
					$result = $conn->query($sql_update);
					echo '<meta http-equiv="refresh" content="0; url=settings.php" >';
					$_SESSION['succes_text'] = "<div class='succes'><div class='exclamation-sign-yellow'>!</div><p class='notification-text'>You have succesfully updated your account information!</p></div>";

				} 
				else 
				{

					$_SESSION['succes_text'] = "<div class='error'><div class='exclamation-sign-red'>!</div><p class='notification-text'>Please mate don't send us fake numbers!</p></div>";

				}
				//Should make the phone number checking procedure, but I'm way to sleepy right now zzzz
			} 
			else 
			{
				echo "<div class='error'><div class='exclamation-sign-red'>!</div><p class='notification-text'>The description you entered is too long!</p></div>";
			}
			
		}

		echo "<br/><h2>Noobies earned</h2><hr/>";

		$result_noobies = mysqli_query($conn, "SELECT * FROM noobies");
		$noobie_type = array('classic');
		$i = 0;

		$games = array("air_slaughter_highscore", "mathrix_highscore");

		//SELECTING THE HIGHSCORES
		$result_high_score = mysqli_query($conn, "SELECT air_slaughter_highscore, mathrix_highscore FROM accounts WHERE id='" . $id_user . "'");
		$row_high_score = mysqli_fetch_assoc($result_high_score);
		function Noobies($row_noobies, $i)
		{
			$noobie_tab = "<div class='noobie-tab' style='background-image:url(../images/noobies/" . $row_noobies['noobie_name'] . ".png);";
			if ($i == 0)
			{
				$noobie_tab .= "margin-left:0;";#Nothing personal css
			}
			$noobie_tab .= "'><div class='type'>" . ucfirst(strtolower($row_noobies['noobie_name'])) . "</div></div>";

			return $noobie_tab;
		}

		while ($row_noobies = mysqli_fetch_assoc($result_noobies))
		{	

			

			switch ($row_noobies['game']) {
				case 1:
					if ($row_noobies['value'] < $row_high_score[$games[0]])
					{
						echo Noobies($row_noobies, $i);
					}
					break;
				case 2:
					if ($row_noobies['value'] < $row_high_score[$games[1]])
					{
						echo Noobies($row_noobies, $i);
					}
			}


			
			$i++;
		}

		$game_tab = "<div class='game-tab'><h2>Favourite games</h2><hr/>";

		$sql_game_types = "SELECT * FROM game_type";
		$result_game_types = $conn->query($sql_game_types);

		//Instantiating an array that's holding the game types
		$game_types = array();
		$i = 0;
		while ($row_game_types = $result_game_types->fetch_assoc()) 
		{
			$game_types[$i] = $row_game_types['type'];
			$i++;
		}
		$game_type_icon = array('bolt', 'futbol-o', 'lightbulb-o');

		$result_favourite_games = mysqli_query($conn, "SELECT id_game FROM favourite_games WHERE id_user='" . $id_user . "'");

		if (!mysqli_num_rows($result_favourite_games))
		{
			$game_tab .= "*<b>Currently</b> you haven't assigned any of our games as a <b>favourite</b> of yours!<br/><br/>";
			$game_tab .= "<a class='button' href='games.php'>Explore our games</a>";
		}
		else
		{	
			$game_counter = 0;#Had a bug here
			while ($row_favourite_games = mysqli_fetch_assoc($result_favourite_games))
			{
				//SELECTING THE GAME DATA
				$result_game_data = mysqli_query($conn, "SELECT * FROM games WHERE id='" . $row_favourite_games['id_game'] . "'");
				$row_game_data = mysqli_fetch_assoc($result_game_data);


				if (($game_counter % 3) == 0)
				{
					$game_tab .= "<div class='game-tab-image' style='background-image:url(../images/games/" . $row_game_data['sprite'] . "); margin-left:0; margin-right: 3%;'>";
				}
				else
				{
					$game_tab .= "<div class='game-tab-image' style='background-image:url(../images/games/" . $row_game_data['sprite'] . ");'>";
				}

				$game_counter++;

				
				

				$game_tab .="<a href='#" . $row_game_data['type'] . "'><i class='fa fa-" . $game_type_icon[$row_game_data['type']] . " game-type' aria-hidden='true'></i></a>";
				$game_tab .= "<div class='game-description' style='display:block;'>";
				$game_tab .= "<label><b>" . ucfirst(strtolower($row_game_data['name'])) . "</b></label>";
				$game_tab .= "<div class='sub-description'><i class='fa fa-" . $game_type_icon[$row_game_data['type']] . " game-type' aria-hidden='true'></i>" . ucfirst(strtolower($game_types[$row_game_data['type']])) . "<br/>";

				for ($i=0; $i < $row_game_data['rating']; $i++) { 
					$game_tab .= "<i class='fa fa-star good' aria-hidden='true'></i>";
				}
				$negative_stars = 5 - $row_game_data['rating'];
				$i = 0;
				while ($i < $negative_stars) {
					$game_tab .= "<i class='fa fa-star' aria-hidden='true'></i>";
					$i++;
				}

				//SELECTING THE FAVOURED FROM DB
				$result_favourites = mysqli_query($conn, "SELECT * FROM favourite_games WHERE id_game='" . $row_game_data['id'] . "'");
				if (!mysqli_num_rows($result_favourites))
				{
					$game_tab .= "<div class='hearts'><b>0</b> <i class='fa fa-heart'></i></div>";
				}
				else
				{
					$game_tab .= "<div class='hearts'><b>" . mysqli_num_rows($result_favourites) . "</b> <i class='fa fa-heart favoured'></i></div>";
				}
				
				$game_tab .= "</div><a href='" . $row_game_data['link'] . "' class='button playButton'>Play!</a>
									<button class='playButton orange-button'>Demo</button>
								</div>
							</div>";
			}
		}
		$game_tab .= "</div>";

		echo $game_tab;

		//SHOULD MAKE THE FAVOURITE GAME SELECTION
		echo "</div><div class='fragment-right' style='width:25% !important;'><div class='fragmentRightPart'>";

		News($conn, $id_user);
		Feedback($conn, $id_user);
		echo Socials();
		echo "</div></div>";
		?>
			<script type="text/javascript">
				$(function(){
					$('.file-input > input').on('change', function () {

						var inputValue = $(this).val();

						$('.input-value').html(inputValue);
					})
				});
			</script>
				</body>
			</html>

		<?php

	} 
	elseif ($status == 2) 
	{
		echo "<div class='fragment-left' style='width:80%; border-right:none'>";
		echo '<h1>Competition control panel</h1><hr/>';
		$day_of_week_full = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");

		/*$comp_search_form = "<div class='comp-search-bar' style='top:-50px;'><form method='POST'>";
		$comp_search_form .= "<div class='column'  style='vertical-align:top;'><label><b>Search a comp</b></label>";
		$comp_search_form .= "<select class='white-select'>";
		$comp_search_form .= "<option value='0' disabaled='disabled' selected='selected'>Day</option>";

		
		$i = 0;

		while ($i < 7)
		{	
			$comp_search_form .= "<option value='" . ($i+1) . "'>" . $day_of_week_full[$i] . "</option>";
			$i++;
		}

		$comp_search_form .= "</select></div>";
		$comp_search_form .= "<div class='column' style='vertical-align:top; margin-left:30px'><label><b>Price</b></label>";
		$comp_search_form .= "<br/><select class='white-select' id='price' name='price_type' style='width:180px; padding-right:10px;'><option>All</option><option>Free</option><option>Paid</option></select></div>";

		$comp_search_form .= "<div class='column' id='paid' style='width:500px;'><label><b>Please specify a price</b></label>";
		$comp_search_form .= "<br/><input class='typeWBGin' style='width:120px; display:inline;' type='text' name='higher_than' placeholder='Higher than'/>";
		$comp_search_form .= " <input class='typeWBGin' style='width:120px; display:inline;' type='text' name='less_than' placeholder='Less than'/></div>";

		$comp_search_form .= "<br/><br/><button type='submit' name='search_comp' value='search_comp'><i class='fa fa-search' aria-hidden='true'></i> Search</button>";


		$comp_search_form .= "</form>";
		$comp_search_form .= "</div>";

		echo $comp_search_form;*/


		echo "<h2>Start a temporary competition</h2>";

		$comp_start_form = "<form method='GET'>";

		function DateSelect($name, $day_of_week_full)
		{
			$comp_start_form .= "<label><b>Comp " . $name . " dates*</b></label><br/>";

			$comp_start_form .= "<select class='white-select tiny' name='" . $name . "_day'>";
			$comp_start_form .= "<option value='0' disabaled='disabled' selected='selected'>Day</option>";

			$i = 0;

			while ($i < 7)
			{	
				$comp_start_form .= "<option value='" . ($i+1) . "'>" . $day_of_week_full[$i] . "</option>";
				$i++;
			}

			$comp_start_form .= "</select><select class='white-select real-tiny' name='" . $name . "_hour'>";

			$comp_start_form .= "<option value='0' disabled='disabaled' selected='selected'>Hour</option>";
			for ($i = 1; $i <= 24; $i++)
			{
				$comp_start_form .= "<option value='" . $i . "'>" . $i . "</option>";
			}
			$comp_start_form .= "</select><select class='white-select real-tiny' name='" . $name . "_minute'>";
			$comp_start_form .= "<option value='0' disabled='disabaled' selected='selected'>Min</option>";
			for ($i=5; $i <= 60; $i+=5) { 
				$comp_start_form .= "<option value='" . $i . "'>" . $i . "</option>";
			}

			$comp_start_form .= "</select>";

			return $comp_start_form;
		}

		$comp_start_form .= DateSelect("start", $day_of_week_full) . "<br/><br/>";
		$comp_start_form .= DateSelect("end", $day_of_week_full) . "<br/><br/>";
		
		$comp_start_form .= "<label><b>Additional*</b></label><br/>";
		$comp_start_form .= "<input class='typeWBGin tiny' name='tax' placeholder='Tax' />";
		$comp_start_form .= "<select class='white-select tiny' name='game_id' style='margin-left: 30px;'>";
		$comp_start_form .= "<option value='0' disabled='disabled' selected='selected'>Game</option>";
		//Selecting the games and putting the form
		$result_games = mysqli_query($conn, "SELECT * FROM games ORDER BY id ASC");

		while ($row_games = mysqli_fetch_assoc($result_games))
		{
			$comp_start_form .= "<option value='" . ($row_games['id']) . "'>" . $row_games['name'] . "</option>";
		}

		$comp_start_form .= "</select><select class='white-select real-tiny' name='min_players'>";
		$comp_start_form .= "<option value='0' disabled='disabled' selected='selected'>Min players</option>";
		for ($i=2; $i <= 8; $i*=2) { 
			$comp_start_form .= "<option value='" . $i . "'>" . $i . "</option>";
		}
		
		$comp_start_form .= "</select>";

		$comp_start_form .= "<br/><br/><button class='orange-button' name='submit_new_comp' value='submit_new_comp'><i class='fa fa-calendar' aria-hidden='true'></i>Create</button>";

		$comp_start_form .= "</form>";

		echo $comp_start_form;

		if (isset($_GET['submit_new_comp']))
		{
			$start_day = (int)mysqli_real_escape_string($conn, $_GET['start_day']);
			$start_hour = (int)mysqli_real_escape_string($conn, $_GET['start_hour']);
			$start_minute = (int)mysqli_real_escape_string($conn, $_GET['start_minute']);

			$end_day = (int)mysqli_real_escape_string($conn, $_GET['end_day']);
			$end_hour = (int)mysqli_real_escape_string($conn, $_GET['end_hour']);
			$end_minute = (int)mysqli_real_escape_string($conn, $_GET['end_minute']);

			$tax = (int)mysqli_real_escape_string($conn, $_GET['tax']);
			$game_id = (int)mysqli_real_escape_string($conn, $_GET['game_id']);
			$min_players = (int)mysqli_real_escape_string($conn, $_GET['min_players']);

			function StartComp($conn, $tax, $start_day, $start_hour, $start_minute, $end_day, $end_hour, $end_minute, $game_id, $min_players)
			{
				mysqli_query($conn, "INSERT INTO comps (tax, start_day, start_hour, start_minute, end_day, end_hour, end_minute, game_id, min_players, num_assigned, current_award, type) VALUES ('" . $tax . "', '" . ($start_day-1) . "', '" . $start_hour . "', '" . $start_minute . "', '" . ($end_day-1) . "', '" . $end_hour . "', '" . $end_minute . "', '" . $game_id . "', '" . $min_players . "', '0', '0' ,'1')");
				echo "<meta http-equiv='refresh' content='0; url=settings.php?competition=success&tax=" . $tax . "&start_day=" . $start_day . "&end_day=" . $end_day . "&game_id=" . $game_id . "&min_players=" . $min_players . "'>";
			}

			if (empty($start_day) || empty($start_hour) || empty($start_minute) || empty($end_day) || empty($end_hour) || empty($end_minute) || empty($game_id))
			{
				echo "Fill out the form before submitting next time please!";
			}
			else
			{
				if (empty($min_players))
				{
					echo "Every comp needs a minimum of 2 players!";
				}
				else
				{
					if ($end_day > $start_day && abs($end_day-$start_day) >= 4)
					{
						echo "The comp is too long!";
					}
					elseif ($end_day < $start_day && abs(6+$end_day-$start_day) >= 4)
					{
						echo "Again too long!";
					}
					elseif($end_day === $start_day)
					{
						if ($end_hour > $start_hour)
						{
							StartComp($conn, $tax, $start_day, $start_hour, $start_minute, $end_day, $end_hour, $end_minute, $game_id, $min_players);
						}
						elseif($end_hour === $start_hour)
						{
							if ($end_minute > $start_minute)
							{
								StartComp($conn, $tax, $start_day, $start_hour, $start_minute, $end_day, $end_hour, $end_minute, $game_id, $min_players);
							}
							else
							{
								echo "Minutes don't match our requirements!";
							}
						}
						else
						{
							echo "Hours don't match the requirements";
						}
					}
					else
					{
						StartComp($conn, $tax, $start_day, $start_hour, $start_minute, $end_day, $end_hour, $end_minute, $game_id, $min_players);
					}
				}
			}
		}

		if (strpos($_SERVER['REQUEST_URI'], 'competition=success'))
		{
			$tax = (int)mysqli_real_escape_string($conn, $_GET['tax']);
			$start_day = (int)(mysqli_real_escape_string($conn, $_GET['start_day'])-1);
			$end_day = (int)(mysqli_real_escape_string($conn, $_GET['end_day'])-1);
			$game_id = (int)mysqli_real_escape_string($conn, $_GET['game_id']);
			$min_players = (int)mysqli_real_escape_string($conn, $_GET['min_players']);

			$result_game_name = mysqli_query($conn, "SELECT name FROM games WHERE id='" . $game_id . "'");
			$row_game_name = mysqli_fetch_assoc($result_game_name);

			echo "You have <b>successfully</b> created a competition with a <b>tax of " . $tax . "$</b> starting on <b>" . $day_of_week_full[$start_day] . "</b> and ending on <b>" . $day_of_week_full[$end_day] . "</b> and a <b>minimum number</b> of players of <b>" . $min_players . "</b> for a game of <b>" . $row_game_name['name'] . "!</b>";
		}


		echo "</div>";
	}
