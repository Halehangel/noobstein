<?php
	session_start();
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	if ($status == 2 || $status == 1)
	{
		echo "<div class='fragment-left'>Sorry " . $username . ", but this isn't ment for you! Have a nice day";
	}
	else
	{	
		echo "<div class='fragment-left fragment-small'><h1>Password restoration</h1><hr/>";

		if (!strpos($_SERVER['REQUEST_URI'], "error=secret_question"))
		{	
			echo "<b>*Type in your email address so we can send you a confirmation code</b>";
			$form_restore_pass = "<form method='GET'>";
			$form_restore_pass .= "<label><b>Email*</b></label>";
			$form_restore_pass .= "<input class='typeWBGin' type='email' name='email' placeholder='Enter your email adress'><br/>";
			$form_restore_pass .= "Forgotten your email? Try secret <a href='forgotten_password.php?error=secret_question'>question!</a><br/><br/>";
			$form_restore_pass .= "<button type='submit' name='send_email' value='send_email'>Send</button>";
			$form_restore_pass .= "</form><br/>";
		}
		else
		{
			$form_restore_pass = "<form method='POST'>";
			$form_restore_pass .= "<label><b>Alternative e-mail</b></label>";
			$form_restore_pass .= "<input class='typeWBGin' type='email' name='alt_email' placeholder='Optional after you register'";
			$form_restore_pass .= "<label><b>Secret question*</b></label>";
			$form_restore_pass .= "<select class='white-select' name='secret_question'>";
			$form_restore_pass .= "<option disabled='disabled' selected='selected' value=''>Select the one you used when signing up</option>";

			//Selecting secret questions from db
			$query_sq = "SELECT * FROM secret_questions ORDER BY id ASC";
			$result_sq = mysqli_query($conn, $query_sq);
			while ($row_sq = mysqli_fetch_assoc($result_sq))
			{
				$form_restore_pass .= "<option>" . $row_sq['name'] . "</option>";
			}
			$form_restore_pass .= "</select><br/><br/>";
			$form_restore_pass .= "<label><b>Secret answer</b></label>";
			$form_restore_pass .= "<input class='typeWBGin' type='text' name='secret_answer' placeholder='Your secret answer...'><br/><br/>";
			$form_restore_pass .= "<button type='submit' name='restore_sq' value='restore_sq'>Restore</button>";
		}
		

		echo $form_restore_pass;

		if (strpos($_SERVER['REQUEST_URI'], "event=request_code"))
		{
			echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully matched the restore combination! We have sent you a confirmation code via e-mail!</div></div>";
			$rand_string_form = "<form method='POST'>";
			$rand_string_form .= "<label><b>Paste your code right here</b></label>";
			$rand_string_form .= "<input class='typeWBGin' type='text' name='request_code' placeholder='Your request code'><br/><br/>";
			$rand_string_form .= "<button type='submit' name='restore_alt' value='restore_alt'>Submit</button>";
			$rand_string_form .= "</form>";
			echo $rand_string_form;

			//The true restoration trough the use of a secret answer
			if (isset($_POST['restore_alt']))
			{
				$request_code = htmlspecialchars(strip_tags($_POST['request_code']));

				$result_match_code = mysqli_query($conn, "SELECT * FROM password_restoration WHERE request_code='" . $request_code . "'");
				if (!mysqli_num_rows($result_match_code))
				{
					echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Incorrect request code!</div></div>";
				}
				else
				{	
					//Generating a random password
					$random_password = RandomString();

					//Updating the accounts password
					$sql_update_pass = "UPDATE accounts SET password='" . sha1($random_password) . "' WHERE email='" . $_SESSION['alt_email'] . "'";
					$result_update_pass = mysqli_query($conn, $sql_update_pass);

					//Sending the e-mail with the restored password
					require_once '../libs/PHPMailerAutoload.php';

					$mail = new PHPMailer();
					$mail->CharSet = 'UTF-8';
					$mail->Host = 'luna.superhosting.bg';
					$mail->SMTPAuth = true;                         
					$mail->Username = 'noobstei';             
					$mail->Password = '';                       
					$mail->SMTPSecure = 'tls';                         
					$mail->Port = 465;     
					$mail->From = 'blinkascendance@gmail.com';
					$mail->FromName = 'admin@noobstein.uchenici.bg';
					$mail->addAddress($_SESSION['alt_email'], $uname);
					
					$mail->setLanguage('bg', '../libs/');
					$mail->isHTML(true);                               

					$mail->Subject = 'Възстановяване на парола в noobstein.uchenici.bg';
					$mail->Body    = 'Hello, this is your new password <b>' . $random_password . '</b>! If you haven\'t expected a letter from the kind of this feel free to report this issue to our system! We always thank for your support! Best regards, the team of <a href="http://noobstein.uchenici.bg">Noobstein!</a>';
					$mail->AltBody = 'Здравейте, '.$uname.'! За възстановяване на вашата парола копирайте и поставете кода ' . $random_string . ' в полето за валидация или натиснете линка, който още не съм измислил! :)';
					
					if(!$mail->send()) 
					{
						echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=not_sent'>";
					} 
					else 
					{	
						//Echoing out a success message
						echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully restored your password! We have sent you the new one via e-mail!<br/> You can now <a href='login.php'>log</a> into our system!</div></div>";
					}

					//Cleaning the restoration table in the db
					mysqli_query($conn, "DELETE FROM password_restoration WHERE email='" . $_SESSION['alt_email'] . "'");
				}
			}
		}	

		//The secret question part
		if (isset($_POST['restore_sq']))
		{	
			$alt_email = htmlspecialchars(strip_tags($_POST['alt_email']));
			$_SESSION['alt_email'] = $alt_email;
			$sq = htmlspecialchars($_POST['secret_question']);
			$sa = sha1(htmlspecialchars(strip_tags($_POST['secret_answer'])));
			//it sets a \ symbol behind a ' symbol

			if (empty($alt_email) || empty($sq) || empty($sa)) 
			{
				echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Empty fields!</div></div>";
			}
			else
			{	
				//Checking if there is an alternative email
				$query_check_alt = "SELECT * FROM accounts WHERE alt_email='" . $alt_email . "' AND secret_question='" . $sq . "' AND secret_answer='" . $sa . "'";
				$result_check_alt = mysqli_query($conn, $query_check_alt);
				$row_check_alt = mysqli_fetch_assoc($result_check_alt);

				if (!mysqli_num_rows($result_check_alt))
				{
					echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Wrong alternative e-mail/secret answer/secret question combination!</div></div>";
				}
				else
				{	
					//Generating the random string
					$random_string = RandomString();

					$uname = $row_check_alt['first_name'] . ' ' . $row_check_alt['last_name'];

					//Send an email to the following email
					require_once '../libs/PHPMailerAutoload.php';

					$mail = new PHPMailer();
					$mail->CharSet = 'UTF-8';
					$mail->Host = 'luna.superhosting.bg';
					$mail->SMTPAuth = true;                         
					$mail->Username = 'noobstei';             
					$mail->Password = '';                       
					$mail->SMTPSecure = 'tls';                         
					$mail->Port = 465;     
					$mail->From = 'blinkascendance@gmail.com';
					$mail->FromName = 'admin@noobstein.uchenici.bg';
					$mail->addAddress($alt_email, $uname);
					
					$mail->setLanguage('bg', '../libs/');
					$mail->isHTML(true);                               

					$mail->Subject = 'Password restoration in noobstein.uchenici.bg';
					$mail->Body    = 'Hello, <b>'.$alt_email.'</b>!<br>In order to restore the password of your account copy the code <b>' .$random_string. '</b> and paste it into the validation field!<br/> In case you haven\'t expected a letter from the kind of this feel free to inform us so we can maintain your privacy! Best regards, the team of <a href="http://noobstein.uchenici.bg">Noobstein</a>';
					
					if(!$mail->send()) 
					{
						echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=not_sent'>";
					} 
					else 
					{	
						//Echoing out the success message and the form

						//Checking if there is already a row in the restoration table
						$query_check_restore = "SELECT * FROM password_restoration WHERE email='" . $alt_email . "'";
						$result_check_restore = mysqli_query($conn, $query_check_restore);

						if (!mysqli_num_rows($result_check_restore))
						{	
							//Adding a row to the forgotten password table in the db
							mysqli_query($conn, "INSERT INTO password_restoration (email, request_code) VALUES ('" . $alt_email . "', '" . $random_string . "')");
						}
						else
						{
							mysqli_query($conn, "UPDATE password_restoration SET request_code='" . $random_string . "'");
						}

						echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=secret_question&event=request_code'>";
					}
					//Should make the notifications! That's crucial for the security
				}
			}
		}
		

		$email = htmlspecialchars(strip_tags($_GET['email']));
		
		

		if (isset($_GET['send_email']))
		{	
			//Generating random string

			//What about the case where there is no such email in the database???
			$sql_search_email = "SELECT * FROM accounts WHERE email='" . $email . "'";
			$result_search_email = $conn->query($sql_search_email);

			if (!mysqli_num_rows($result_search_email))
			{
				echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=not_in_db'>";
			}
			else
			{

				$_SESSION['email_restore'] = $email;

				$random_string = RandomString();

				//Inserting the data into the database
				$sql_check_restore = "SELECT * FROM password_restoration WHERE email='" . $email . "'";
				$result_check_restore = $conn->query($sql_check_restore);


				if (!mysqli_num_rows($result_check_restore)) 
				{
					$sql_restore = "INSERT INTO password_restoration (email, request_code) VALUES ('" . $email . "', '" . $random_string . "')";
					$result_restore = $conn->query($sql_restore);
				}
				else
				{
					$sql_restore = "UPDATE password_restoration SET request_code='" . sha1($random_string) . "' WHERE email='" . $email . "'";
					$result_restore = $conn->query($sql_restore);
				}

				

				//Send an email to the following email
				require_once '../libs/PHPMailerAutoload.php';

				$mail = new PHPMailer();
				$mail->CharSet = 'UTF-8';
				$mail->Host = 'luna.superhosting.bg';
				$mail->SMTPAuth = true;                         
				$mail->Username = 'noobstei';             
				$mail->Password = '';                       
				$mail->SMTPSecure = 'tls';                         
				$mail->Port = 465;     
				$mail->From = 'blinkascendance@gmail.com';
				$mail->FromName = 'admin@noobstein.uchenici.bg';
				$mail->addAddress($email, $uname);
				
				$mail->setLanguage('bg', '../libs/');
				$mail->isHTML(true);                               

				$mail->Subject = 'Възстановяване на парола в noobstein.uchenici.bg';
				$mail->Body    = 'Hello, <b>'.$email.'</b>!<br>In order to restore the password of your account copy <b>' .$random_string. '</b> and paste it into the validation field!<br/>In case you haven\'t signed up for our services or haven\'t expected a message from the kind of this feel free to inform us at any time! Best regards, the team of <a href="http://noobstein.uchenici.bg">Noobstein</a>';
				$mail->AltBody = 'Здравейте, '.$uname.'! За възстановяване на вашата парола копирайте и поставете кода ' . $random_string . ' в полето за валидация или натиснете линка, който още не съм измислил! :)';
				
				if(!$mail->send()) 
				{
					echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=not_sent'>";
				} 
				else 
				{
					echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=success' >";
				}
			}
		}


		$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

		if (strpos($url, "error=success") )
		{
			//Echoing out the confirmation form of the validation code whenever a code is sent
			echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>We have sent a request code via e-mail!</div></div>";
			$rand_string_form = "<form method='GET'>";
			$rand_string_form .= "<label><b>Paste your code right here</b></label>";
			$rand_string_form .= "<input class='typeWBGin' type='text' name='rand_string' placeholder='Your request code'><br/><br/>";
			$rand_string_form .= "<button type='submit' name='restore' value='restore'>Submit</button>";
			$rand_string_form .= "</form>";
			echo $rand_string_form;
		}
		elseif (strpos($url, "error=not_sent"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Sorry, but couldn't send an e-mail! Please retry after a while!</div></div>";
		}
		elseif (strpos($url, "error=not_in_db"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Sorry, but there is no such account registered with this email!</div></div>";
		}
		elseif (strpos($url, "error=wrong_request_code"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>May you excuse us, but the request code you have entered is incorrect! May you try again!</div></div>";
		}
		elseif (strpos($url, "error=not_sent_update"))
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Sorry, but a problem with the system occured! Please try again later!</div></div>";
		}
		elseif (strpos($url,"error=update_success"))
		{
			echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have succesfully updated your password! Check your e-mail for the new one!</p>You can now freely <a href='http://noobstein.uchenici.bg/php/login.php'>log</a> into our system</div></div>";
		}

		//The real change already
		if (isset($_GET['restore'])) {
			//if the field is empty
			$request_code = html_entity_decode(strip_tags($_GET['rand_string']));

			$query_request_code = "SELECT * FROM password_restoration WHERE request_code='" . $request_code . "'";
			$result_request_code = $conn->query($query_request_code);

			if (!mysqli_num_rows($result_request_code))
			{
				echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=wrong_request_code'>";
			}
			else
			{	
				//Not safe at all
				echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>Wonderful! Now you have been sent a new password via e-mail!</div></div>";

				//Generating a new one
				$random_password = RandomString();

				//The query for the update of the password
				$query_update_password = "UPDATE accounts SET password='" . sha1($random_password) . "' WHERE email='" . $_SESSION['email_restore'] . "'";
				$result_update_password = $conn->query($query_update_password);

				//Sending an email with the new password
				require_once '../libs/PHPMailerAutoload.php';

				$mail = new PHPMailer();
				$mail->CharSet = 'UTF-8';
				$mail->Host = 'luna.superhosting.bg';
				$mail->SMTPAuth = true;                         
				$mail->Username = 'noobstei';             
				$mail->Password = '';                       
				$mail->SMTPSecure = 'tls';                         
				$mail->Port = 465;     
				$mail->From = 'blinkascendance@gmail.com';
				$mail->FromName = 'admin@noobstein.uchenici.bg';
				$mail->addAddress($_SESSION['email_restore'], 'Unknown');
				
				$mail->setLanguage('bg', '../libs/');
				$mail->isHTML(true);                               

				$mail->Subject = 'Restoting a password in <b>noobstein.uchenici.bg</b>';
				$mail->Body    = 'Hello, <b>' . $_SESSION['email_restore'] . '</b>!<br>This is a letter with your updated password in <a href="http://noobstein.uchenici.bg">Noobstein</a>! <br/>New password - <b>' . $random_password . '</b>';
				$mail->AltBody = 'Здравейте, '.$uname.'! За възстановяване на вашата парола копирайте и поставете кода ' . $random_string . ' в полето за валидация или натиснете линка, който още не съм измислил! :)';
				
				if(!$mail->send()) 
				{
					echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=not_sent_update'>";
				} 
				else 
				{
					echo "<meta http-equiv='refresh' content='0; url=forgotten_password.php?error=update_success'>";
				}

				//Deleting the row from the password_restoration table
				$dismiss_pass_restoration = "DELETE FROM password_restoration WHERE email='" . $_SESSION['email_restore'] . "'";
				$result_dismiss = $conn->query($dismiss_pass_restoration);
			}

		}

	}

		echo "</div>";