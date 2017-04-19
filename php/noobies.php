<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	echo "<div class='fragment-left'><h1>Noobies</h1><hr/>";

	echo "*<b>Noobies</b> are a kind of throphies <b>each player</b> can earn through participation in <b>competitions</b> and achieving new <b>highscores!</b> Noobies are also used in our <b>social system!</b> More <b>available</b> soon!<hr class='blue'/>";

	function Noobies($game_id, $conn, $title, $id_user, $i=0)
	{	
		$result_noobies = mysqli_query($conn, "SELECT * FROM noobies WHERE game='" . $game_id . "' ORDER BY value ASC");

		$hs_array = array('air_slaughter_highscore', 'mathrix_highscore', 'keyboard_ninja_highscore', 'street_pong_highscore');

		$result_highscore = mysqli_query($conn, "SELECT * FROM accounts WHERE id='" . $id_user . "'");
		$row_highscore = mysqli_fetch_assoc($result_highscore);
		$highscore = $row_highscore[$hs_array[$game_id - 1]];

		$noobie_tabs = '<h2>' . $title . '</h2>';
		while ($row_noobies = mysqli_fetch_assoc($result_noobies))
		{	
			if ($highscore >= $row_noobies['value'])
			{
				$noobie_tabs .= "<div class='noobie-tab' style='background-image:url(../images/noobies/" . $row_noobies['noobie_name'] . ".png);";
			}
			else
			{
				$noobie_tabs .= "<div class='noobie-tab' style='background-image:url(../images/noobies/" . $row_noobies['noobie_name'] . ".png); opacity: 0.5;";
			}
			
			if ($i == 0)
			{
				$noobie_tabs .= "margin-left:0;";#Nothing personal css
			}
			$noobie_tabs .= "'><div class='type'>" . ucfirst(strtolower($row_noobies['noobie_name'])) . "</div></div>";
			$i++;
		}

		return $noobie_tabs;
	}
	
	echo Noobies(1, $conn, "Air slaughter", $id_user) . "<br/><br/>";
	echo Noobies(2, $conn, "Mathrix", $id_user);

	echo "</div><div class='fragment-right' style='width:25% !important;'><div class='fragmentRightPart'>";

	News($conn, $id_user);
	Feedback($conn, $id_user);
	echo Socials();
	echo "";

	echo "</div></body></html>";
