<?php
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	date_default_timezone_set("Europe/Sofia");
?>

			
				<div class="fragment-left">
					<h1 style="display:inline;">Sign up </h1><h3 style="display:inline;">* account activation</h3><hr/>
					<p algin="center">
						<div class="registerStep done">1</div>
						<div class="registerStep done">2</div>
						<div class="registerStep undone">3</div>
					</p>

					<?php
						$uname = mysqli_real_escape_string($conn, $_POST['uname']);
						$email = mysqli_real_escape_string($conn, $_POST['email']);
						$psw = sha1(mysqli_real_escape_string($conn, $_POST['psw']));
						$psw_confirm = sha1(mysqli_real_escape_string($conn, $_POST['psw_confirm']));
						$secret_question = mysqli_real_escape_string($conn, $_POST['secret_question']);
						$secret_answer = sha1(mysqli_real_escape_string($conn, $_POST['secret_answer']));
						$phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);#should fix this...
						$checkbox = mysqli_real_escape_string($conn, $_POST['checkbox']);
						$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
						$last_name = mysqli_real_escape_string($conn, $_POST['last_name']);


						$random_string = RandomString();

						$sql_insert_user = "INSERT INTO accounts (user_index, noob_tag, email, password, secret_question, secret_answer, phone_number, first_name, last_name, rank) 
						VALUES ('" . $random_string . "', '" . $uname . "', '" . $email . "', '" . $psw . "', '" . $secret_question . "', '" . $secret_answer . "', '" . $phone_number . "','" . $first_name . "','" . $last_name . "', '1')";
						
						$sql_select_user = "SELECT * FROM accounts WHERE noob_tag='" . $uname . "' OR email='" . $email . "'";
						$sql_select_noob_tag = "SELECT * FROM tag_gen";
						$sql_select_noob_id = "SELECT id FROM accounts WHERE noob_tag='" . $uname . "'";

						//selecting the user trough a query
						$result_select_user = $conn->query($sql_select_user);
						$row = $result_select_user->fetch_assoc();

						$psw_length = strlen($psw);

						//Registration
						if (isset($_POST['submit'])) 
						{
							//Checking whether the passwords match
							if ($psw == $psw_confirm && $psw_length >= 8 && $psw_length <= 40)
							{
								//Checking if username is already taken
								if (strtolower($uname) != strtolower($row['noob_tag'])) 
								{
									//Checking if email is already taken
									if (strtolower($email) != strtolower($row['email'])) 
									{	
										//Checking for fake numbers
										if (strlen($phone_number) >= 8 && strlen($phone_number) <= 12 || !$phone_number) 
										{	
											//Checking the length of the names the client has entered
											if (strlen($first_name) <= 40 && strlen($last_name) <= 40) 
											{	
												//Checking if the client has submitted a phone number
												if ($secret_answer != NULL) 
												{
													$result = $conn->query($sql_insert_user);
													$result_select_noob_id = $conn->query($sql_select_noob_id);
													$row = $result_select_noob_id->fetch_assoc();
													//Inserting an account balance of 0
													$sql_insert_account_balance = "INSERT INTO account_balance (id, fund, total_profits) VALUES ('" . $row['id'] . "', '10', '10')";
													$result_insert_account_balance = $conn->query($sql_insert_account_balance);
													//Inserting a row in the achievments table
													$query_insert_achievs = "INSERT INTO achievs (id, total_money_earned, current_week_money, total_spendings, total_points_gained, current_week_points) VALUES ('" . $row['id'] . "','0','0','0','0','0')";
													$result_insert_achievs = $conn->query($query_insert_achievs);
													//Inserting a row in the game achievments table
													$query_game_achievs = "INSERT INTO game_achievs (id) VALUES ('" . $row['id'] . "')";
													$result_game_achievs = $conn->query($query_game_achievs);

													$succes_message = "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>You have successfully signed up into our system!</div></div>";
													$succes_message .= "<form method='GET'><input type='hidden' name='uname' value=" . $uname . "><label><b>We have send you a confirmation code via email!</b></label><input class='typeWBGin' type='text' name='confirmation_code' placeholder='Paste your code here...'><br/><br/><button type='submit' name='submit_validation_code' value='submit_validation_code'>Activate your account!</button></form>";

													echo $succes_message;

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

													$mail->Subject = 'Регистрация в noobstein.uchenici.bg';
													$mail->Body    = 'Здравейте, <b>'.$uname.'</b>!<br>За да активирате на Вашия акаунт в noobstein.uchenici.bg копирайте кода ' .$random_string. ' и го поставете в полето за валидация!<br/> Ако сте мързеливи просто натиснете <a href="http://noobstein.uchenici.bg/php/check_reg.php?uname=' . $uname . '&confirmation_code=' . $random_string . '&submit_validation_code=submit_validation_code">хипервръзката</a>!';
													$mail->AltBody = 'Здравейте, '.$uname.'! За активация на вашия акаунт копирайте и поставете кода ' . $random_string . ' в полето за валидация или натиснете линка, който още не съм измислил! :)';
													
													if (!$mail->send()) 
													{
														$error= 'Съобщението не можа да бъде изпратено. Грешка: ' . $mail->ErrorInfo;
													} 
													else 
													{
														$text= '<h3>Регистрация</h3> Име:' . $uname . '<br> E-mail: ' . $email . '<br> Потребителско име:'.$uname.'<br> На вашата електронна поща беше изпратено писмо с код за активиране.<br/> Влезте за повече игри и забавления <a href="http://noobstein.uchenici.bg/php/login.php"!';
													}
												} 
												else 
												{
													echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Please mate, we need your secret answer in order to maintain your account security and privacy!<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";
												}
												
											} 
											else 
											{
												echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Come on mate even africans and asians don't have names that long!<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";

											}
											
										} 
										else 
										{
											echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>You have entered a fake phone number!<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";
										}
									} 
									else 
									{
										echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The email you have submitted is already taken!<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";
									}
								} 
								else 
								{
									//Generating ideas for a username when one is taken
									$suggested_name1 = $uname . "_" . rand(10,99);
									$suggested_name2 = $uname . rand(10,99);
									$suggested_name3 = str_shuffle($uname) . "_" . rand(10,99);
									while ($suggested_name1 == $uname) 
									{
										$suggested_name1 .= rand(0,9);
									}
									while ($suggested_name2 == $uname) 
									{
										$suggested_name2 .= rand(0,9);
									}
									while ($suggested_name3 == $uname) 
									{
										$suggested_name3 .= rand(0,9);
									}
									echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The noob tag you just entered is already taken! Best regards and try again! Suggested examples: " . $suggested_name1 . ", " . $suggested_name2 . ", " . $suggested_name3 . "<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";
								}
							} 
							else 
							{
								echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Passwords don't match or the password you have entered is too short!<br/>You will be sent back to the registration form in <span id='countdowntimer'>10</span> seconds.</div></div>";
							}
						}

						//I should try making the checkbox part of the code!!!!

						//Working over the confirmation code part
						$confirmation_code = mysqli_real_escape_string($conn, $_GET['confirmation_code']);
						$noob_tag = mysqli_real_escape_string($conn, $_GET['uname']);

						$sql_activate_acc = "UPDATE accounts
						SET user_index='1'
						WHERE noob_tag = '" . $noob_tag . "'
						";
						//Selecting the request code that is stored in the user_index
						$sql_get_activation_code = "SELECT user_index, id FROM accounts WHERE noob_tag='" . $noob_tag . "'";
						$result_get_activation_code = $conn->query($sql_get_activation_code);
						$row_get_activation_code = $result_get_activation_code->fetch_assoc();

						$activation_code = $row_get_activation_code['user_index'];
						
						//Request code validation
						if (isset($_GET['submit_validation_code'])) 
						{	
							if (!empty($confirmation_code)) 
							{	
								//Checking whether both codes match
								if ($confirmation_code == $activation_code) 
								{	
									//Final activation
									$result_confirmation = $conn->query($sql_activate_acc);
									//Message with a form that contains a hidden value
									echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>Congrats on your account activation!<br/>You can now <a href='login.php'>log</a> into our system or go ahead and <form action='personalization.php' method='POST' style='display:inline;'><input type='hidden' name='noob_tag' value=" . $noob_tag . "><button class='no-styling' type='submit'>personalize</button></form> your account!</div></div>";

									$note = "Greetings newbie! Thank you for signing up into our system!<br/>Joining us you become a member of a fast developing vast community of web gamers! You can now enter competitions from the home page, track your progress and achievments from dashboard and a lot of other things!";
									mysqli_query($conn, "INSERT INTO notifications (id_user, requester, content, dates, seen) VALUES ('" . $row_get_activation_code['id'] . "', 'Team NoobStein', '" . $note . "', '" . date("Y-m-d H:i:s") . "', '0')");

								} 
								else 
								{
									echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Incorrect confirmation code! Please try again later!</div></div>";
								}
							} 
							else 
							{
								echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Please fill out the fields before submitting next time!</div></div>";
							}
						}

					?>
				</div><!--Should fix this before massive exploit-->
				<br/>
				<script type="text/javascript">
					    var timeleft = 10;
					    var downloadTimer = setInterval(function(){
					    timeleft--;
					    document.getElementById("countdowntimer").textContent = timeleft;
					    if(timeleft <= 0) {
					        clearInterval(downloadTimer);
					        window.location = 'register.php';
					    }
					    },1000);
					</script>
