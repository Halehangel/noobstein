<?php
	session_start();
	include 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	include 'functions.php';
	date_default_timezone_set("Europe/Sofia");
			
	echo "<div class='fragment-left'>";

	/*$search_form = "<form method='POST'>";
	$search_form .= "<label><b>In search of a certain game?</b></label>";
	$search_form .= "<input class='typeWBGin orange' type='text' name='game_search' placeholder='Find it from here!' />";
	$search_form .= "<br/><br/><button type='submit' name='submit_search' value='submit_search'><i class='fa fa-search'></i>Search</button>";
	$search_form .= "</form>";

	echo $search_form;*/

	function Games ($conn, $title='Title', $done='1')
	{
		$sql_select_games = "SELECT * FROM games WHERE done='" . $done . "' ORDER BY rating DESC LIMIT 6";
		$result_select_games = $conn->query($sql_select_games);
		$game_tab = "<div class='game-tab'><h1>" . $title . "</h1><hr/><br/>";

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
		//Taking out the games
		while ($row_select_games = $result_select_games->fetch_assoc()) 
		{	
			$game_tab .= "<div class='game-tab-image' style='background-image:url(../images/games/" . $row_select_games['sprite'] . ");'>

			<a href='#" . $row_select_games['type'] . "'><i class='fa fa-" . $game_type_icon[$row_select_games['type']] . " game-type' aria-hidden='true'></i></a>
									<div class='game-description' style='display:block;'>
										<label><b>" . ucfirst(strtolower($row_select_games['name'])) . "</b></label>
										<div class='sub-description'><i class='fa fa-" . $game_type_icon[$row_select_games['type']] . " game-type' aria-hidden='true'></i>" . ucfirst(strtolower($game_types[$row_select_games['type']])) . "<br/>";

			$result_rating = mysqli_query($conn, "SELECT * FROM game_rating WHERE id_game='" . $row_select_games['id'] . "'");

          	$rating = 0;

			while ($row_rating = mysqli_fetch_assoc($result_rating)) {
				$rating += $row_rating['rate'];
			}

			$exact_rating = round($rating/mysqli_num_rows($result_rating), 0, PHP_ROUND_HALF_UP);

			mysqli_query($conn, "UPDATE games SET rating='" . $exact_rating . "' WHERE id='" . $row_select_games['id'] . "'");

			for ($i=0; $i < $exact_rating; $i++) { 
				$game_tab .= "<i class='fa fa-star good' aria-hidden='true'></i>";
			}
			$negative_stars = 5 - $exact_rating;
			$i = 0;
			while ($i < $negative_stars) {
				$game_tab .= "<i class='fa fa-star' aria-hidden='true'></i>";
				$i++;
			}

			//SELECTING THE FAVOURED FROM DB
			$result_favourites = mysqli_query($conn, "SELECT * FROM favourite_games WHERE id_game='" . $row_select_games['id'] . "'");
			if (!mysqli_num_rows($result_favourites))
			{
				$game_tab .= "<div class='hearts'><b>0</b> <i class='fa fa-heart'></i></div>";
			}
			else
			{
				$game_tab .= "<div class='hearts'><b>" . mysqli_num_rows($result_favourites) . "</b> <i class='fa fa-heart favoured'></i></div>";
			}
			
			$game_tab .= "</div><a href='" . $row_select_games['link'] . "' class='button playButton'>Play!</a><br/><br/>
								
							</div>
						</div>";
		}
		$game_tab .= "</div>";
		return $game_tab;
	}

	echo Games($conn, "Most rated");
	echo "<h2>Game types</h2><hr class='blue'/>";
	$game_type_icon = array('bolt', 'futbol-o', 'lightbulb-o');


	$sql_game_types = "SELECT * FROM game_type";
	$result_game_types = $conn->query($sql_game_types);
	$id_counter = 0;
	
	//Listing the game types
	while ($row_game_types = $result_game_types->fetch_assoc())
	{
		$games_info .= "<i id='" . $id_counter . "' class='fa fa-" . $game_type_icon[$row_game_types['id'] - 1] . " info-icon info-" . $game_type_icon[$row_game_types['id'] - 1] . "'></i> <b>" . ucfirst(strtolower($row_game_types['type'])) . " |</b> " . $row_game_types['description'] . "<br/><br/>";
		$id_counter++;
	}

	echo $games_info;

	//UPCOMING GAMES
	echo Games($conn, "In development", 0);
	echo "*Development mode is used to gain feedback from our clients so please be helpful!";

	
	echo "</div>";#End of the left fragment
	echo "<div class='fragment-right' style='width:25% !important;'><div class='fragmentRightPart'>";
	News($conn, $id_user);
	if ($status != 0)
	{
		Feedback($conn, $id_user);
	}
	
	echo Socials();
	echo "</div></div></div></body></html>";
