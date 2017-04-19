<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	echo "<div class='fragment-left fragment-small'><h1>Change password</h1><hr/>";
	echo "*We always keep your personal data secure! If you are aware of password theft please feel free to inform us!";
					
	$form = "<form method='POST'>";
	$form .= "<label><b>Old password*</b></label><input class='typeWBGin' type='password' name='old_pass' placeholder='Type here'>";
	$form .= "<label><b>New password*</b></label><input class='typeWBGin' type='password' name='new_pass' placeholder='Min 8 symbols'>";
	$form .= "<label><b>Confirm the new password*</b></label><input class='typeWBGin' type='password' name='confirm_pass' placeholder='Repeat it'>";
	$form .= "<br/><br/><button type='submit' name='change_pass' value='change_pass'><i class='fa fa-repeat'></i>Change</button>";

	$form .= "</form>";

	echo $form;

	

	$old_pass = sha1(htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $_POST['old_pass']))));
	$new_pass = htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $_POST['new_pass'])));
	$confirm_pass = htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, $_POST['confirm_pass'])));

	if (isset($_POST['change_pass']))
	{
		if (empty($old_pass) && empty($new_pass) && empty($confirm_pass))
		{	
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Please fill out the fields!</div></div>";
		}
		else
		{
			//Select the actual one
			$sql_select_old = "SELECT password FROM accounts WHERE id='" . $id_user . "'";
			$result_select_old = mysqli_query($conn, $sql_select_old);
			$row_select_old = mysqli_fetch_assoc($result_select_old);

			//Check if the old and the new passwords match
			if ($old_pass != $row_select_old['password'])
			{
				echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The password you have entered doesn't match the old one!</div></div>";
			}
			else
			{	
				if (strlen($new_pass) < 8)
				{
					echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Password must be longer!</div></div>";
				}
				else
				{
					if ($new_pass != $confirm_pass)
					{
						echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The new passwords don't match!</div></div>";
					}
					else
					{
						$sql_update_pass = "UPDATE accounts SET password='" . sha1($new_pass) . "' WHERE id='" . $id_user . "'";
						$result_update_pass = mysqli_query($conn, $sql_update_pass);
						echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully updated your password!</div></div>";
					}
				}
				
			}
		}
	}

	echo "</div>";

	