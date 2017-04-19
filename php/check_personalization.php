<?php
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
?>

			
				<div class="fragment-left">
					<h1 style="display:inline;">Sign up </h1><h3 style="display:inline;">* setup completed</h3><hr/>
					<p algin="center">
						<div class="registerStep done">1</div>
						<div class="registerStep done">2</div>
						<div class="registerStep done">3</div>
					</p>

					<?php
						$experience = strip_tags($_POST['experience']);
						$description = strip_tags($_POST['description']);
						$phone_number = strip_tags($_POST['phone_number']);
						$noob_tag = strip_tags($_POST['noob_tag']);

						//Updating the account's data
						$sql_insert_description = "UPDATE accounts 
						SET experience='" . $experience . "', description='" . $description . "'
						WHERE noob_tag='" . $noob_tag . "'";
						//Updating the phone number
						$sql_update_phone_number = "UPDATE accounts
						SET phone_number='" . $phone_number . "'
						WHERE noob_tag='" . $noob_tag . "'
						";

						//Making the checks
						if (!empty($experience) && !empty($description)) 
						{	
							//If the phone number is correct
							if (strlen($phone_number) >= 8 && strlen($phone_number) <= 16 || !$phone_number) 
							{	
								$conn->query($sql_insert_description);
								//If there is a phone number
								if ($phone_number != NULL) 
								{
									$conn->query($sql_update_phone_number);
									echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>Cheers! You have succesfully set up your new account! All additional data can be changed later through account settings!</div><div class='note-content'>You will be redirected to our login page in <span id='countdowntimer'>16</span> seconds or you can click <a href='login.php'>here</a></div></div>";
								} 
								else 
								{	
									echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>Cheers! You have succesfully set up your new account! All additional data can be changed later through account settings!</div><div class='note-content'>You will be redirected to our login page in <span id='countdowntimer'>16</span> seconds or you can click <a href='login.php'>here</a></div></div>";
								}
							} 
							else 
							{
								echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Please don't send us fake phone numbers!</div></div>";
							}
							
						} 
						else 
						{
							echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Please fill out the fields before submitting data next time!</div>";
						}
					?>

					<script type="text/javascript">
						//Just a timer function used to change the location
						//Should add it to a separate js file on a later stage
					    var timeleft = 16;
					    var downloadTimer = setInterval(function(){
					    timeleft--;
					    document.getElementById("countdowntimer").textContent = timeleft;
					    if(timeleft <= 0) {
					        clearInterval(downloadTimer);
					        window.location = 'login.php';
					    }
					    },1000);
					</script>
				</div>
				<br/>
				</body>
				</html>