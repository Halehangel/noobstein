<?php
	require 'session_start.php';
	require 'dbconnect.php';
	require 'functions.php';
	include 'header.php';
		
	echo "<div class='fragment-left fragment-small'>";

	$login_name = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['login_name']));
	$psw = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['psw']));

	$sql = "SELECT * FROM accounts WHERE noob_tag='" . $login_name . "' OR email='" . $login_name . "'";

	$result = $conn->query($sql);

	if (!$row = $result->fetch_assoc()) 
	{
		echo "<h1>Login Error</h1><hr/><div class='note-popup'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Incorrect username/email</div><div class='note-content'>You will be redirected to our login page in <span id='countdowntimer'>5</span> seconds!</div></div></div>";
	} 
	else 
	{
		if (sha1($psw) == $row['password'])
		{
			echo "<h1>Authentication succes!</h1><hr/>";
			$_SESSION['id'] = $row['id'];
			$_SESSION['username'] = $row['noob_tag'];
			$_SESSION['user_index'] = $row['user_index'];

			$_SESSION['just_came_in'] = true;

			//Including the user into the active
			$sql_active_insert = "INSERT INTO active_users (id_user) VALUES ('" . $_SESSION['id'] . "')";
			$result_active_insert = $conn->query($sql_active_insert);

			echo "<meta http-equiv='refresh' content='0; url=index.php'>";
		} 
		else 
		{
			echo "<h1>Authentication error</h1><hr/>";
			echo "<div class='note-popup'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Wrong password</div><div class='note-content'>You will be redirected to our login page in <span id='timer'>5</span> seconds!</div></div></div>";
		}
	}


//Should fix this
?>	
	</div>

		<script type="text/javascript">
		function CountDown(id) {
			var timeleft = 5;
		    var downloadTimer = setInterval(function(){
		    timeleft--;
		    document.getElementById(id).textContent = timeleft;
		    if(timeleft <= 0) {
		        clearInterval(downloadTimer);
		        window.location = 'login.php';
		    }
		    },1000);
		}
		CountDown("timer");
		</script>
	</body>
	</html>