<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
	
?>
				<div class="fragment-left fragment-small">
					<h1>Login</h1><hr/>

					<form action="check_login.php" method="POST">
						<label><b>Noob tag or Email</b></label>
						 <br/>
						 <input id="noobtag" class="typeWBGin" type="text" placeholder="You can use both of them to log in..." name="login_name"/>
						<br/>
						<label><b>Password</b></label>
						<input class="typeWBGin" type="password" placeholder="Enter your password" name="psw" required>
						<p>Forgoten <a href="forgotten_password.php">password?</a></p>
						<button type="submit" name="submit" value="submit">Login</button>
					</form>
					<?php
						if (strpos($_SERVER['REQUEST_URI'], "msg=farewell"))
						{
							echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Successfully logged out</div><div class='note-content'>Hope we see you again later!</div></div>";
						}
					?>
				</div>
				<div class="fragment-left fragment-small" style="margin-left:6%; border-right:none;">
					<div class="">
						
					</div>
				</div>
				<br/>
		</div>
	</body>
</html>