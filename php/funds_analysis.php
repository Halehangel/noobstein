<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	
	echo "<div class='fragment-left fragment-small'><h1>Analysis</h1><hr/>";

	echo "<div class='funds-text funds-big'>" . round($row['fund'],2, PHP_ROUND_HALF_UP) . "$</div>";
	$achievs = "<h2 style='margin-top:50px;'>Stats</h2><hr class='blue'/>";
	$achievs .= "<div class='text-box'>Total money earned<div class='stat'>" . round($row['total_profits'],2, PHP_ROUND_HALF_DOWN) . "$</div></div>";

	$query_highscore = "SELECT * FROM accounts WHERE id='" . $id_user . "'";
	$result_highscore = mysqli_query($conn, $query_highscore);

	while ($row_highscore = mysqli_fetch_assoc($result_highscore))
	{
		$achievs .= "<div class='text-box'>Air Slaughter Highscore<div class='stat'>" . $row_highscore['air_slaughter_highscore'] . "</div></div>";
		$achievs .= "<div class='text-box'>Mathrix Highscore<div class='stat'>" . $row_highscore['mathrix_highscore'] . "</div></div>";
		$achievs .= "<div class='text-box'>Keyboard Ninja Highscore<div class='stat'>" . $row_highscore['keyboard_ninja_highscore'] . "</div></div>";

	}

	echo $achievs;
	echo "<br/>";

	echo	"</div></body></html>";