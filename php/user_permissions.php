<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	echo "<div class='fragment-left'><h1>User permissions</h1><hr/>";
	echo "*This page gives you the full control over user permission related actions<br/>";
	
	$form_search_user = "<form method='POST'>";
	$form_search_user .= "<label><b>Search for a user</b></label>";
	$form_search_user .= "<input class='typeWBGin' type='text' name='search_user' placeholder='Type in a name...'>";
	$form_search_user .= "<br/><br/><button type='submit' name='search_submit' value='search_submit'><i class='fa fa-search' aria-hidden='true'></i>Search</button>";
	if (strpos($url, "search_user="))
	{
		$form_search_user .= " <button style='background-color:rgb(252,55,55);border-color:rgb(216,36,36);' type='submit' name='end_search' value='end_search'><i class='fa fa-times' aria-hidden='true'></i>Refresh</button>";
	}
	$form_search_user .= "</form>";

	echo $form_search_user;

	//If the admin goes on and searches
	if (isset($_POST['search_submit']))
	{
		$search_user = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['search_user']));

		if (empty($search_user))
		{
			echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>Empty fields!</div></div></div>";
		}
		else
		{
			echo "<meta http-equiv='refresh' content='0; url=user_permissions.php?search_user=" . $search_user . "'>";
			exit();	
		}
	}

	//If he desides to reset the search
	if (isset($_POST['end_search']))
	{
		echo "<meta http-equiv='refresh' content='0; url=user_permissions.php'>";
		exit();
	}

	if (strpos($url, "search_user="))
	{
		$user = explode("=", $url);

		$result_select_all = mysqli_query($conn, "SELECT * FROM accounts");


		$user_block = "<div class='user-block'>";
		//Initiating a counter for the number of rows that match the search
		$i = 0;
		while ($row_select_all = mysqli_fetch_assoc($result_select_all))
		{	
			if (strpos(strtolower($row_select_all['noob_tag']), strtolower($user[1])) !== false)
			{
					
					$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_select_all['id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

					if (!mysqli_num_rows($result_profile_image))
					{
						$user_block .= "<div class='row'><div class='avatar-thumb' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
					}
					else
					{	
						$row_profile_image = mysqli_fetch_assoc($result_profile_image);
						$user_block .= "<div class='row'><div class='avatar-thumb' style='background-image: url(../images/avatars/" . $row_profile_image['file_name'] . ");'></div>";
					}
					

					$user_block .= ucfirst(strtolower($row_select_all['first_name'])) . " " . ucfirst(strtolower($row_select_all['last_name'])) . " (<b><i>" . $row_select_all['email'] . "</i></b>)</div>";
					//Should fix this
					$i++;
			}
		}
		$user_block .= '</div>';

		//If there are results echo out the div
		if ($i != 0)
		{
			echo $user_block;
		}
	}
	else
	{
		$result_select_all = mysqli_query($conn, "SELECT * FROM accounts WHERE user_index<>'2' ORDER BY id DESC");

		$user_block = "<div class='user-block'>";

		//Initializing a limit for the number of accounts to be output
		$limit = 10;

		//Selecting the page on which the accounts should be taken
		if (strpos($url, "permission="))
		{
			$id = explode("=", $url);
			$result_select_id = mysqli_query($conn, "SELECT first_name, email, user_index FROM accounts WHERE id='" . $id[1] . "'");
			$row_select_id = mysqli_fetch_assoc($result_select_id);
			
			//Should make a ban period that's going to be accomplished probably trough the use of another table :) called banned
		}
		elseif (strpos($url, "delete="))
		{
			$id = explode("=", $url);
			$result_select_id = mysqli_query($conn, "SELECT first_name, email FROM accounts WHERE id='" . $id[1] . "'");
			$row_select_id = mysqli_fetch_assoc($result_select_id);
			echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>You have successfully deleted account number " . $id[1] . "</div></div></div>";
		}

		if (strpos($url, "page="))
		{
			$page = explode("=", $url);
			$current_page = end($page);
		}
		else
		{
			$current_page = 1;
		}
		
		$account_counter = 0;
		$num_rows = mysqli_num_rows($result_select_all);

		//Initializing the limits of the pages
		$top_limit = $current_page * $limit;
		$bottom_limit = ($current_page - 1) * $limit;

		
		while ($row_select_all = mysqli_fetch_assoc($result_select_all))
		{	
			$account_counter++;

			if ($account_counter > $bottom_limit && $account_counter <= $top_limit)
			{
				$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_select_all['id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

				if (!mysqli_num_rows($result_profile_image))
				{
					$user_block .= "<div class='row'><div class='avatar-thumb' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
				}
				else
				{	
					$row_profile_image = mysqli_fetch_assoc($result_profile_image);
					$user_block .= "<div class='row'><div class='avatar-thumb' style='background-image: url(../images/avatars/" . $row_profile_image['file_name'] . ");'></div>";
				}
				$user_block .= ucfirst(strtolower($row_select_all['first_name'])) . " " . ucfirst(strtolower($row_select_all['last_name'])) . " (<b><i>" . $row_select_all['email'] . "</i></b>)";

				//The form part
				$user_block .= "<form class='inline-icon' method='POST'>";
				$user_block .= "<input type='hidden' name='id' value='" . $row_select_all['id'] . "'>";
				$user_block .= "<input type='hidden' name='email' value='" . $row_select_all['email'] . "'>";
				$user_block .= "<button type='submit' name='ban' value='ban'><i class='fa fa-ban'></i></button>";
				$user_block .= "<button type='submit' name='delete' value='delete'><i class='fa fa-trash'></i></button>";
				$user_block .= "<button type='submit' name='edit' value='edit'><i class='fa fa-pencil'></i></button>";
				$user_block .= "<button type='submit' name='message' value='message'><i class='fa fa-commenting'></i></button>";
				$user_block .= "</form>";
				$user_block .= "</div>";
			}
		}

		$user_block .= "</div>";

		//If you want to ban somebody, etc...
		if (isset($_POST['ban']))
		{
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));

			$permission_form = "<form method='POST'>";
			$permission_form .= "<input type='hidden' name='id' value='" . $id . "'>";#nobody will ever notice :D
			$permission_form .= "<input type='hidden' name='email' value='" . $email . "'>";
			$permission_form .= "<label><b>Email</b></label>";
			$permission_form .= "<input class='typeWBGin' type='email' name='disabled_email' value='" . $email . "' disabled='disabled'>";
			$permission_form .= "<label><b>Status</b></label>";
			$permission_form .= "<select class='white-select' name='user_index'>";

			$result_select_user_index = mysqli_query($conn, "SELECT user_index FROM accounts WHERE id='" . $id . "'");
			$row_select_user_index = mysqli_fetch_assoc($result_select_user_index);

			switch ($row_select_user_index['user_index']) 
			{
				case 1:
					$permission_form .= "<option selected='selected' value='1'>Regular</option>";
					$permission_form .= "<option value='3'>Banned</option>";
					$permission_form .= "<option value='2'>Admin</option>";
					break;
				
				case 3:
					$permission_form .= "<option value='1'>Regular</option>";
					$permission_form .= "<option selected='selected' value='3'>Banned</option>";
					$permission_form .= "<option value='2'>Admin</option>";
					break;
			}

			
			$permission_form .= "</select>";
			$permission_form .= "<br/><br/><button type='submit' name='permission_change' value='change_permission'><i class='fa fa-refresh' aria-hidden='true'></i>Change</button>";
			$permission_form .= "</form>";

			echo $permission_form;


		}

		//If you want to delete an account
		if (isset($_POST['delete']))
		{	
			//Taking out the fundamental variables
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));

			//Account deleting form
			$form_delete = "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Do you really want to delete the account of " . $email . "?<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>";

			$form_delete .= "<form method='POST'>";
			$form_delete .= "<input type='hidden' name='id' value='" . $id . "'>";
			$form_delete .= "<input type='hidden' name='email' value='" . $email . "'>";
			$form_delete .= "<button type='submit' name='confirm_delete' value='confirm_delete'>Yes</button>";
			$form_delete .= " <button type='submit' name='decline_delete' value='decline_delete'>No</button>";
			$form_delete .= "</form>";
			$form_delete .= "</div></div></div>";

			echo $form_delete;
		}

		//If you want to edit account data
		if (isset($_POST['edit']))
		{	
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));

			$result_select_description = mysqli_query($conn, "SELECT first_name, last_name, phone_number, description, alt_email FROM accounts WHERE id='" . $id . "'");
			$row_select_description = mysqli_fetch_assoc($result_select_description);

			echo "<br/><h3>" . $row_select_description['first_name'] . " " . $row_select_description['last_name'] . "'s </b> description</h3><hr/>";

			$form_edit_account = "<form method='POST'>";
			$form_edit_account .= "<input type='hidden' name='id' value='" . $id . "'>";
			$form_edit_account .= "<input type='hidden' name='email' value='" . $email . "'>";
			//Should make them orange
			$form_edit_account .= "<label><b>Phone number</b></label>";
			$form_edit_account .= "<input class='typeWBGin orange' type='text' name='phone_number' value='" . $row_select_description['phone_number'] . "' placeholder='Between 8 and 16 symbols'>";
			$form_edit_account .= "<label><b>Alt e-mail</b></label>";
			$form_edit_account .= "<input class='typeWBGin orange' type='email' name='alt_email' value='" . $row_select_description['alt_email'] . "' placeholder='A reference e-mail'>";
			$form_edit_account .= "<label><b>Description</b></label>";
			$form_edit_account .= "<textarea class='typeWBGin orange' name='description' placeholder='Max 300 symbols'>" . str_replace("\\", "", $row_select_description['description']) . "</textarea>";
			$form_edit_account .= "<br/><br/><button type='submit' name='submit_description' value='submit_description'><i class='fa fa-refresh' aria-hidden='true'></i>Change</button>";
			$form_edit_account .= "</form>";

			echo $form_edit_account;
	
			//Should add inner messaging as well as such a one via e-mail			
		}

		//SHOULD TOTALLY ORDER THIS MESSHOUSE!!!!
		//Whenever you want to message an account
		if (isset($_POST['message']))
		{
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));

			echo "<br/><h3>Message to $email</h3><hr/>";
			$form_msg = "<form method='POST'>";
			$form_msg .= "<input type='hidden' name='id' value='" . $id . "'>";
			$form_msg .= "<input type='hidden' name='email' value='" . $email . "'>";
			$form_msg .= "<label><b>Message</b></label>";
			$form_msg .= "<textarea class='typeWBGin orange' name='msg' placeholder='Message /max 300 symbols/...'></textarea>";
			$form_msg .= "<br/><br/><button type='submit' name='submit_msg' value='submit_msg'><i class='fa fa-paper-plane' aria-hidden='true'></i>Send</button>";
			$form_msg .= "</form>";

			echo $form_msg;
		}

		if (isset($_POST['submit_msg']))
		{
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));
			$msg = nl2br(htmlspecialchars(mysqli_real_escape_string($conn, $_POST['msg'])));

			if (strlen($msg) > 300)
			{
				echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>The message you have entered is too long - max 300 symbols! (yours <b><i>" . strlen($msg) . "</i></b>)</div></div></div>";
			}
			else
			{
				mysqli_query($conn, "INSERT INTO notifications (user_id, content, dates, seen) VALUES ('" . $id . "', '" . $msg . "', '" . date("Y-m-d H:i:s") . "', '0')");

				echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>You have successfully messaged <b><i>$email!</i></b></div></div></div>";
			}
		}

		//After you submit the user related data as an admin
		if ($_POST['submit_description'])
		{	
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));
			$phone_number = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['phone_number']));
			$alt_email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['alt_email']));
			$description = nl2br(htmlspecialchars(mysqli_real_escape_string($conn, $_POST['description'])));

			if (empty($id) || empty($phone_number) || empty($alt_email) || empty($description))
			{
				echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>Empty fields!</div></div></div>";
			}
			else
			{
				if (strlen($phone_number) < 8 || strlen($phone_number) > 16)
				{
					echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>The length of the phone number does not match the requirements! Must be between 8 and 16 symbols! (<b><i>yours " . strlen($phone_number) . "</i></b>)</div></div></div>";
				}
				else
				{
					if (strlen($alt_email) > 40)
					{
						echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>The alt e-mail you have entered is too long! Required under 40 symbols! (<b><i>yours " . strlen($alt_email) . "</i></b>)</div></div></div>";
					}
					else
					{
						if (strlen($description) > 300)
						{
							echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>The description longer than 300 symbols! (<b><i>yours " . strlen($description) . "</i></b>)</div></div></div>";
						}
						else
						{
							//Actual update
							mysqli_query($conn, "UPDATE accounts SET phone_number='" . $phone_number . "', alt_email='" . $alt_email . "', description='" . $description . "' WHERE id='" . $id . "'");

							//Echoing out a message for the admin
							echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>You have successfully updated the data of <b><i>$email!</i></b></div></div></div>";

							//And sending a message via e-mail for the client
							require_once '../libs/PHPMailerAutoload.php';
					
							$mail = new PHPMailer();
							$mail->CharSet = 'UTF-8';
							$mail->Host = 'luna.superhosting.bg';
							$mail->SMTPAuth = true;                         
							$mail->Username = 'noobstei';             
							$mail->Password = '';                       
							$mail->SMTPSecure = 'tls';                         
							$mail->Port = 465;     
							$mail->From = 'admin@noobstein.uchenici.bg';
							$mail->FromName = 'admin@noobstein.uchenici.bg';
							$mail->addAddress($email);
							
							$mail->setLanguage('bg', '../libs/');
							$mail->isHTML(true);                               

							$mail->Subject = 'Account actions in noobstein.uchenici.bg';

							$mail->Body    = "Greetings, your account description in <b><a href='http://noobstein.uchenici.bg/'>NoobStein</a></b> has recently changed to as it follows:<br/><b>Phone number -</b> $phone_number<br/><b>Alt e-mail -</b> $alt_email<br/><b>Description -</b> $description";
							$mail->AltBody = "Greetings, your account description in <b><a href='http://noobstein.uchenici.bg/'>NoobStein</a></b> has recently changed to as it follows:<br/><b>Phone number -</b> $phone_number<br/><b>Alt e-mail -</b> $alt_email<br/><b>Description -</b> $description";
							
							if (!$mail->send()) 
							{
								$error= "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>Email was not send due to an issue: <b><i>" . $mail->ErrorInfo . "</i></b></div></div></div>";
								echo $error;
							}
						}
					}
				}
			}

		}
		

		//Confirmation parts of the code
		if (isset($_POST['permission_change']))
		{	
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$user_index = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['user_index']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));

			mysqli_query($conn, "UPDATE accounts SET user_index='" . $user_index . "' WHERE id='" . $id . "'");

			switch ($row_select_id['user_index']) {
				case 1:
					$user_status = "<b>regular</b>";
					break;
				case 2:
					$user_status = "<b>admin</b>";
					break;
				case 3:
					$user_status = "<b>banned</b>";
					break;
			}

			echo "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>You have changed the status of<b>" . $email . "</b> to " . $user_status . "!</div></div></div>";

			//Send an email to the specified user
			require_once '../libs/PHPMailerAutoload.php';
					
			$mail = new PHPMailer();
			$mail->CharSet = 'UTF-8';
			$mail->Host = 'luna.superhosting.bg';
			$mail->SMTPAuth = true;                         
			$mail->Username = 'noobstei';             
			$mail->Password = '';                       
			$mail->SMTPSecure = 'tls';                         
			$mail->Port = 465;     
			$mail->From = 'admin@noobstein.uchenici.bg';
			$mail->FromName = 'admin@noobstein.uchenici.bg';
			$mail->addAddress($email);
			
			$mail->setLanguage('bg', '../libs/');
			$mail->isHTML(true);                               

			$mail->Subject = 'Account actions in noobstein.uchenici.bg';

			switch ($user_index) {
				case 1:
					$mail->Body    = 'Greetings, your account status into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently changed to regular! Enjoy your day!';
					$mail->AltBody = 'Greetings, your account status into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently changed to regular! Enjoy your day!';
					break;
				case 2:
					$mail->Body    = 'Greetings, You buddy have been <b>promoted</b> to an admin of <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b>! Enjoy your day!<br/>Remember our <b>number one priority</b> is to keep clients comming in!';
					$mail->AltBody = 'Greetings, You buddy have been <b>promoted</b> to an admin of <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b>! Enjoy your day!<br/>Remember our <b>number one priority</b> is to keep clients comming in!';
					break;
				case 3:
					$mail->Body    = 'Sorry, but your account into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently been banned! Possible reasons reported for hacking, irresponsible admins...';
					//Should make it take out data warnings for which time in a row!
					//And perhaps an if statement to check if has already been banned for a few times and then deleting it :D
					$mail->AltBody = 'Sorry, but your account into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently been banned! Possible reasons reported for hacking, irresponsible admins...';
					break;
			}
			
			
			if (!$mail->send()) 
			{
				$error= "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>Email was not send due to an issue: <b>" . $mail->ErrorInfo . "</b></div></div></div>";
				echo $error;
			}

			//Should make a ban option for a few days :D
		}

		if (isset($_POST['confirm_delete']))
		{
			//Taking out the data and actually deleting the account
			$id = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['id']));
			$email = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['email']));
			//Deleting the user from accounts
			mysqli_query($conn, "DELETE FROM accounts WHERE id='" . $id . "'");
			//Deleting the user from account balance
			mysqli_query($conn, "DELETE FROM account_balance WHERE id='" . $id . "'");
			//Deleting the user from achievs
			mysqli_query($conn, "DELETE FROM achievs WHERE id='" . $id . "'");
			//Remove him from the active users table
			mysqli_query($conn, "DELETE FROM active_users WHERE id_user='" . $id . "'");
			//Deleting the user from comp stats
			mysqli_query($conn, "DELETE FROM comp_stats WHERE id_user='" . $id . "'");
			//Deleting the user from the current comp stats
			mysqli_query($conn, "DELETE FROM current_comps WHERE id_user='" . $id . "'");
			//Deleting the user related comments
			mysqli_query($conn, "DELETE FROM feedback WHERE email='" . $email . "'");
			//Deleting the user from friendships
			mysqli_query($conn, "DELETE FROM friendships WHERE user_one_id='" . $id . "' OR user_two_id='" . $id . "'");
			//Deleting the user from game achievs
			mysqli_query($conn, "DELETE FROM game_achievs WHERE id='" . $id . "'");
			//Deleting the user from password restoration table
			mysqli_query($conn, "DELETE FROM password_restoration WHERE email='" . $email . "'");
			//Deleting the user from past comp stats
			mysqli_query($conn, "DELETE FROM past_comp_stats WHERE id_user='" . $id_user . "'");

			//Sending a message to user
			require_once '../libs/PHPMailerAutoload.php';
					
			$mail = new PHPMailer();
			$mail->CharSet = 'UTF-8';
			$mail->Host = 'luna.superhosting.bg';
			$mail->SMTPAuth = true;                         
			$mail->Username = 'noobstei';             
			$mail->Password = '';                       
			$mail->SMTPSecure = 'tls';                         
			$mail->Port = 465;     
			$mail->From = 'admin@noobstein.uchenici.bg';
			$mail->FromName = 'admin@noobstein.uchenici.bg';
			$mail->addAddress($email, $uname);
			
			$mail->setLanguage('bg', '../libs/');
			$mail->isHTML(true);                               

			$mail->Subject = 'Account actions in noobstein.uchenici.bg';
			$mail->Body    = 'Sorry, but your account into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently been deleted! Possible reasons banned 5 times, reported for hacking, irresponsible admins...';
			$mail->AltBody = 'Sorry, but your account into <b><a href="http://noobstein.uchenici.bg/">NoobStein</a></b> has recently been deleted! Possible reasons banned 5 times, reported for hacking, irresponsible admins...';
			
			if (!$mail->send()) 
			{
				$error= "<div class='note-popup' onclick='HidePopup();'><div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error<i onclick='HidePopup();' class='fa fa-times-circle close-right'></i></div><div class='note-content'>Email was not send due to an issue: <b>" . $mail->ErrorInfo . "</b></div></div></div>";
				echo $error;
			}
			

			//Referring to another page
			echo "<meta http-equiv='refresh' content='0; url=user_permissions.php?delete=" . $id . "'>";
		}

		//Getting the number of the pages
		$num_pages = round((mysqli_num_rows($result_select_all) / $limit), 0, PHP_ROUND_HALF_UP);

		//Call to a paginating function
		echo Pagination($current_page, $num_pages, "user_permissions");
		echo $user_block;
		echo Pagination($current_page, $num_pages, "user_permissions");
	}

	echo "</div>";
