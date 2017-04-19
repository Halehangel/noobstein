<?php
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
?>

				<div class="fragment-left">
				<h1 style="display:inline;">Sign up </h1><h3 style="display:inline;">* basic account setup</h3><hr/>
					<p algin="center">
						<div class="registerStep done">1</div>
						<div class="registerStep undone">2</div>
						<div class="registerStep undone">3</div>
					</p>


					<form action="check_reg.php" method="POST">
						<label><b>Noob tag*</b></label>
						<br/>
						<?php
						//Taking the url
						$url_gen = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

						if (strpos($url_gen, "gen_name=true"))
						{	
							$sql_gen_tag = "SELECT * FROM tag_gen";
							$result_gen_tag = $conn->query($sql_gen_tag);

							//Adjective
							$random_key_adj = rand(0,mysqli_num_rows($result_gen_tag)-1);
							//Noun
							$random_name = "";
							//Disordered like this just for the sake of better randomizing
							$random_key_noun = rand(0,mysqli_num_rows($result_gen_tag)-1);
							
							$i = 0;
							while ($row_gen_tag = $result_gen_tag->fetch_assoc()) 
							{
								if ($i == $random_key_adj)
								{	
									//Just to make sure the adjective always stays beofre the noun
									$random_name = $row_gen_tag['adjective'] . $random_name;
								}
								if ($i == $random_key_noun) {
									$random_name .= $row_gen_tag['noun'];
								}

								$i++;
							}

							echo "<div style='position:relative; height:20px;'><i class='fa fa-tag fa-input' aria-hidden='true'></i><input id='noobtag' class='typeWBGin' type='text' placeholder='This Is How Others Will See You' name='uname' value='" . $random_name . "'/></div>";
						}
						else
						{
							echo "<input id='noobtag' class='typeWBGin' type='text' placeholder='This Is How Others Will See You' name='uname'/>";
						}
						?>
						
						<br/><br/>
						<a class="button" href="register.php?gen_name=true"><i class="fa fa-random" aria-hidden="true"></i> Generate Random Tag</a><br/>
						<label><b>E-mail*</b></label>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-envelope-o fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="email" placeholder="Make Sure It's Not Fake" name="email" required>
						</div><br/>
						<label><b>Password*</b></label>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-key fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="password" placeholder="Enter Password/min 8 chars/" name="psw" required>
						</div><br/>
						<label><b>Confirm Password*</b></label>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-key fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="password" placeholder="Repeat Your Password" name="psw_confirm" required>
						</div><br/><hr class="blue"/>
						<p>A secret question is a thing that can be used to restore your account so pick carefully!*</p>
						<label><b>Secret Question*</b></label><br/>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-question fa-input' aria-hidden='true'></i>
							<select class="white-select" name="secret_question">
								<option>What's the name of your first pet?</option>
								<option>What's the name of your granny?</option>
								<option>What's the most remarkable place you've ever been?</option>
								<option>Favourite super heroe?</option>
							</select>
						</div>

						<br/>
						<label><b>Secret Answer*</b></label>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-info fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="text" placeholder="Something Noone Else Except You Knows" name="secret_answer">
						</div><br/>
						<label><b>Personal Names*</b></label><br/>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-info fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="text" placeholder="First name" name="first_name">
						</div><br/>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-info fa-input' aria-hidden='true'></i>
							<input class="typeWBGin" type="text" placeholder="Last name" name="last_name">
						</div><br/>
						<label><b>Phone Number</b></label>
						<div style='position:relative; height:20px;'>
							<i class='fa fa-phone fa-input' aria-hidden='true'></i>
							<input class="typeWBGin orange" type="text" placeholder="Must be at least 10 or less than 16" name="phone_number">
						</div><br/>
						<input type="hidden" name="checkbox">
						<p>I <a href="info.php?id=3">agree</a> with the terms of use! <input class="checkbox" type="checkbox" name="checkbox" value="agreed" required></p>
						<button type="submit" name="submit" value="submit"><i class="fa fa-plus" aria-hidden="true"></i> Sign up</button>

					</form>
				</div>
				<div class="fragment-right" style="width:25% !important;">
					<div class="fragmentRightPart">
						<h2>Advantages</h2>
						<p>First of all you earn money, we do so as well, people get their apps... and everyone's happy :)</p>
						
					</div>
					<div id="feedback" class="fragmentRightPart">
						<h2>Feedback</h2>
						<form method="POST">
							<label><b>Email</b></label>
							<input type="email" class="typeWBGin" name="feedback_email" placeholder="Limit 40 characters"><br/>
							<label><b>Content</b></label>
							<textarea class="typeWBGin" name="feedback_content" placeholder="Limit 300 characters"></textarea><br/><br/>
							<button name="feedback_submit" value="feedback_submit" type="submit">Submit</button>
						</form>
						<?php
							$feedback_email = strip_tags($_POST['feedback_email']);
							$feedback_content = strip_tags($_POST['feedback_content']);

							$feedback_email_length = strlen($feedback_email);
							$feedback_content_length = strlen($feedback_content);

							$sql_feedback = "INSERT INTO feedback (email, content) VALUES ('" . $feedback_email . "', '" . $feedback_content . "')";

							if (isset($_POST['feedback_submit'])) {
								if ($feedback_email_length <= 40) {
									if ($feedback_content_length <= 300) {
										$result_feedback = $conn->query($sql_feedback);
										echo "<div class='succes'><div class='exclamation-sign-yellow'>!</div><p class='notification-text'>Thank you for letting us know what you think about our services! Have a nice day! :)</p></div>";
										//should add something from the sort of dont't bother me You have already given me the same content and should add a thing that limits the number of feedbacks daily!
									} else {
										echo "<div class='error'><div class='exclamation-sign-red'>!</div><p class='notification-text'>Data in the content box should be less than 300!</p></div>";
									}
								} else {
									echo "<div class='error'><div class='exclamation-sign-red'>!</div><p class='notification-text'>The email you have entered is way too long!</p></div>";
								}
							}

						?>
					</div>
					<div class="fragmentRightPart">
						<h2>Follow us on social media!</h2>
						<a href="#" target="blank"><img src="../images/facebook.png"></a>
						<a href="#" target="blank"><img src="../images/twitter.png"></a>
						<a href="#" target="blank"><img src="../images/sketchfab.png"></a>
						<a href="#" target="blank"><img src="../images/googlePlus.png"></a>
					</div>
				</div>
				<br/>
				<?php
					//include 'footer.php';
				?>





				

			

		</div><!--Here ends pageWrap-->
		<!--<footer class="footer">
			<div class="footBox">
				<ul class="foot">
					<li class="foot"><a class="foot" href="#">Contacts</a></li>
					<li class="foot"><a class="foot" href="#">Donate</a></li>
					<li class="foot"><a class="foot" href="#">About</a></li>
					<li class="foot"><a class="foot" href="#">License agreement</a></li>
				</ul>
			</div>
		</footer>
		<script src="js/modalClose.js" type="text/javascript"></script>
		<script src="js/SnapPhoto.js" type="text/javascript"></script>-->
	</body>
</html>