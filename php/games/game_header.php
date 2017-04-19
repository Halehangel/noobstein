<?php
$nav = '<header><div class="nav-bar"><div class="nav-box-1"><ul class="nav">';
			$url_nav = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			function AddSubNav($nav_smth, $value='title.php', $conn, $id_user)
			{
				if ($nav_smth[1] != NULL)
				{	
					//Adding the dropdown-content
					$nav_str .= "<div class='dropdown-content' style='color:black;' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>";
					$nav_str .= "<a class='title' href='../../" . $value . "'>" . str_replace('.php', '', ucfirst($value)) . "</a>";
					
					//Starting a counter for the array
					$i = 1;

					while ($nav_smth[$i] != NULL)
					{	
						$clear_nav = str_replace("_", " ", ucfirst(strtolower($nav_smth[$i])));
						$nav_str .= "<a href='../../" . str_replace("&_", "", strtolower($nav_smth[$i])) . ".php'>" . $clear_nav;

						if (strcmp(strtolower($nav_smth[$i]), "notifications") == 0)
						{	
							$result_search_new_notes = $conn->query("SELECT * FROM notifications WHERE id_user='" . $id_user . "' AND seen='0'");
							if (mysqli_num_rows($result_search_new_notes))
							{
								$nav_str .= "<div class='note-circle'>" . mysqli_num_rows($result_search_new_notes) . "</div>";
							}
						}

						$nav_str .= "</a>";
						$i++;
					}
					//Closing the dropdown-content
					$nav_str .= "</div>";
				}
				return $nav_str;
			}#End of SubNav function

			if ($status == 0) {

				//Should make an url connected menu color changer type of thingie...

				$nav_array = array(
					"home" => "../index.php",
					"gamepad" => "games.php"
				);

				/*$nav_array['home'] = "../index.php";
				$nav_array['gamepad'] = array("gamepad" => "games.php", "Submenu 1", "Submenu 2");*/
				$nav_array_secondary = array(
					"user-plus" => "register.php"
					);

				
				//Done for now
				foreach ($nav_array as $key=>$value) {

					//If there is a subnav
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					if (strpos($url_nav, $value))
					{	

						$nav .= "<li class='nav'>
									<a class='nav' href='../../" . $value . "' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
										<i class='fa fa-" . $key . " fa-nav' style='color:#49a7ff'></i>
									</a>";
						//Just to start taking out the main parts of the submenu
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);

						$nav .= "</li>";
					}
					else
					{
						$nav .= "<li class='nav'>
							<a class='nav' href='../../" . $value . "' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
								<i class='fa fa-" . $key . " fa-nav'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
				}
				$display_block = 'document.getElementById("id01").style.display="block"';

				//The login button
				if (strpos($url_nav, "login.php"))
				{
					$nav .= "<li class='nav-secondary'>
							<a class='nav color-orange' onclick='" . $display_block . "' style='color:rgb(255,160,0);'>
								<i class='fa fa-sign-in fa-nav'></i>
							</a>
						</li>";
				}
				else
				{
					$nav .= "<li class='nav-secondary'>
							<a class='nav color-orange' onclick='" . $display_block . "'>
								<i class='fa fa-sign-in fa-nav'></i>
							</a>
						</li>";
				}
				
				//The secondary part of the navigation
				foreach ($nav_array_secondary as $key=>$value) {
					//If there is a subnav
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					//If you are on the page change the color... just for a better look
					if (strpos($url_nav, $value))
					{
						$nav .= "<li class='nav-secondary' title='Settings' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'>
								<i class='fa fa-" . $key ." fa-nav' style='color:rgb(255,160,0);'></i>
							</a>";

						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);

						$nav .=	"</li>";
					}
					else
					{
						$nav .= "<li class='nav-secondary' title='Settings' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
				}




		?>
			<div id="id01" class="modal">
				<form class="modal-content animate" action="../../check_login.php" method="POST">
					<div class="imgcontainer">
						<span onclick="document.getElementById('id01').style.display='none'" class="close" title="Close Modal">&times;</span>
						<img src="../../../images/avatars/default_avatar.png" alt="Avatar" class="login-avatar">
					</div>

					<div class="container">
						<label><b>Username</b></label>
						<input class="typeWBGin" type="text" placeholder="Enter Username" name="login_name" required><br/>

						<label><b>Password</b></label>
						<input class="typeWBGin" type="password" placeholder="Enter Password" name="psw" required><br/><br/>


						<button type="submit">Login</button>
						<!--<input type="checkbox" checked="checked"> Remember me-->
					</div>

					<div class="container" style="background-color:#f1f1f1">
						<button type="button" onclick="document.getElementById('id01').style.display='none'" class="orangeButton">Cancel</button>
					</div>
				</form>
			</div>
			
		<?php
			} elseif($status == 1) {
				$nav_array = array(
					"home" => "index.php",
					"tasks" => "dashboard.php Previous",
					"gamepad" => "games.php"
					);
				$nav_array_secondary = array(
					"cog" => "settings.php Change_password Noobies Funds_&_Analysis",
					"users" => "social.php Friends Notifications"
					);
				//Should add a word count function to make the functions more exact :)

				
				foreach ($nav_array as $key=>$value) {
					//If there is a subnav
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					if (strpos($url_nav, $value))
					{
						$nav .= "<li class='nav'  title='Home' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav' style='color:#49a7ff;'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";

					}
					else
					{
						$nav .= "<li class='nav' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
				}
				foreach ($nav_array_secondary as $key=>$value) {
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					//Checking for notifications
					

					if (strpos($url_nav, $value))
					{	
						$nav .= "<li class='nav-secondary' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'><i class='fa fa-" . $key ." fa-nav' style='color:rgb(255,160,0);'></i>";

						if (str_replace(".php", "", strtolower($value)) == "friends")
						{
							$result_search_new_notes = $conn->query("SELECT * FROM notifications WHERE id_user='" . $id_user . "' AND seen='0'");
							if (mysqli_num_rows($result_search_new_notes))
							{
								$nav .= "<div class='note-circle' style='z-index:-1;'>" . mysqli_num_rows($result_search_new_notes) . "</div>";
							}
						}
						
						$nav .= "</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					else
					{
						$nav .= "<li class='nav-secondary' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'><i class='fa fa-" . $key . " fa-nav'></i>";
						if (str_replace(".php", "", strtolower($value)) == "friends")
						{
							$result_search_new_notes = $conn->query("SELECT * FROM notifications WHERE id_user='" . $id_user . "' AND seen='0'");
							if (mysqli_num_rows($result_search_new_notes))
							{
								$nav .= "<div class='note-circle' style='z-index:-1;'>" . mysqli_num_rows($result_search_new_notes) . "</div>";
							}
						}
						$nav .= "</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
					
				}

				$sql_select_funds = "SELECT fund FROM account_balance LEFT JOIN accounts ON account_balance.id=accounts.id WHERE account_balance.id='" . $id_user . "'";
				$result = $conn->query($sql_select_funds);
				$row = $result->fetch_assoc();
				if ($row['fund'] <= 1000) {
					$nav.= "<li class='nav-secondary' title='Funds'>
							<a class='nav' class='funds_analysis.php'><div class='funds-text'>" . round($row['fund'], 0, PHP_ROUND_HALF_UP) . "$</div></a>
						</li>";
				} else {
					$nav .= "<div class='funds-text'>" . round($row['fund']/1000, 0, PHP_ROUND_HALF_UP) . "k$</div></a>
						</li>";
				}
				$nav .= "<li class='nav-secondary' title='Logout'>
							<form method='POST'>
								<button class='no-styling logout' type='submit' name='submit_logout' value='submit_logout'><i class='fa fa-sign-out fa-nav'></i></button>
							</form>
						</li>";
				
			} 
			elseif($status == 2) 
			{
				//Setting up the admin panel navigation
				$nav_array = array(
					"home" => "index.php",
					"gamepad" => "games.php"
					);
				$nav_array_secondary = array(
					"cog" => "settings.php User_permissions",
					"users" => "friends.php"
					);
				//Should add a word count function to make the functions more exact :)

				
				foreach ($nav_array as $key=>$value) {
					//If there is a subnav
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					if (strpos($url_nav, $value))
					{
						$nav .= "<li class='nav'  title='Home' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav' style='color:#49a7ff;'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";

					}
					else
					{
						$nav .= "<li class='nav' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
				}
				foreach ($nav_array_secondary as $key=>$value) {
					if (strpos($value, ' '))
					{
						$nav_smth = explode(' ', $value, 5);
						$value = $nav_smth['0'];
					}

					if (strpos($url_nav, $value))
					{
						$nav .= "<li class='nav-secondary' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'>
								<i class='fa fa-" . $key ." fa-nav' style='color:rgb(255,160,0);'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					else
					{
						$nav .= "<li class='nav-secondary' onmouseover='HideFragmentLeft();' onmouseout='RevealFragmentLeft();'>
							<a class='nav color-orange' href='../../" . $value . "'>
								<i class='fa fa-" . $key . " fa-nav'></i>
							</a>";
						$nav .= AddSubNav($nav_smth, $value, $conn, $id_user);
						$nav .=	"</li>";
					}
					unset($nav_smth);
					
				}

				$sql_select_funds = "SELECT fund FROM account_balance LEFT JOIN accounts ON account_balance.id=accounts.id WHERE account_balance.id='" . $id_user . "'";
				$result = $conn->query($sql_select_funds);
				$row = $result->fetch_assoc();
				if ($row['fund'] <= 1000) {
					$nav.= "<li class='nav-secondary' title='Funds'>
							<a class='nav'><div class='funds-text'>" . round($row['fund'], 0, PHP_ROUND_HALF_UP) . "$</div></a>
						</li>";
				} else {
					$nav .= "<div class='funds-text'>" . round($row['fund']/1000, 0, PHP_ROUND_HALF_UP) . "k$</div></a>
						</li>";
				}
				$nav .= "<li class='nav-secondary' title='Logout'>
							<form method='POST'>
								<button class='no-styling logout' type='submit' name='submit_logout' value='submit_logout'><i class='fa fa-sign-out fa-nav'></i></button>
							</form>
						</li>";
			}

			$nav .= "</ul></div></div>";
			echo $nav;
			//Logout
			if (isset($_POST['submit_logout'])) {
				session_destroy();
				$sql_drop_active = "DELETE FROM active_users WHERE id_user='" . $id_user . "'";
				$result_drop_active = $conn->query($sql_drop_active);
				echo '<meta http-equiv="refresh" content="0; url=../../login.php?msg=farewell" >';
				exit();
			}	