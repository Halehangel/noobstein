<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	
	$id = (int)$_GET['id'];

	$sql = "SELECT * FROM info WHERE id='" . $id . "'";
	$result = $conn->query($sql);
	$row = $result->fetch_assoc();

	echo "<div class='fragment-left fragment-small'>";	
	echo "<h1>" . $row['title'] . "</h1><hr/>";
	echo $row['content'];
	echo "</div></body></html>";
?>