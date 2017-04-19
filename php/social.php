<?php
	require 'session_start.php';
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';

	date_default_timezone_set("Europe/Sofia");
	$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	if ($status == 1)
	{
		echo "<div class='fragment-left'>";
		echo "<h1>Social</h1><hr/>";

		//INITIATING AN ARRAY THAT HOLDS THE TYPES OF THE EVENTS
		$result_event_types = mysqli_query($conn, "SELECT * FROM social_event_types");
		$event_types = array();
		while ($row_event_types = mysqli_fetch_assoc($result_event_types))
		{
			$event_types[$row_event_types['id']] = $row_event_types['name'];
		}

		if (strpos($url, "comment="))
		{
			$id_event = end(explode('=', $url));
			//SELECTING THE EVENT
			$result_event = mysqli_query($conn, "SELECT * FROM social_events WHERE id='" . $id_event . "'");

			while ($row_event = mysqli_fetch_assoc($result_event)) 
			{
				$social_section = "<div class='social-event-section'><div class='event-box'>";

				//SELECTING AN AVATAR
				$result_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user = '" . $row_event['id_user'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

				if (!mysqli_num_rows($result_avatar))
				{
					$social_section .= "<div class='head'><div class='avatar' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
				}
				else
				{
					$row_avatar = mysqli_fetch_assoc($result_avatar);
					$social_section .= "<div class='head'><div class='avatar' style='background-image: url(../images/avatars/" . $row_avatar['file_name'] . ");'></div>";
				}

				//SELECTING THE USER RELATED DATA
				$result_user_data = mysqli_query($conn, "SELECT * FROM accounts WHERE id='" . $row_event['id_user'] . "'");
				$row_user_data = mysqli_fetch_assoc($result_user_data);

				$social_section .= "<b>" . $row_user_data['noob_tag'] . "</b><div class='event-type'>" . ucfirst($event_types[$row_event['type']]) .  "</div></div>";

				//NOW ADDING THE CONTENT
				$social_section .= "<div class='content'>" . str_replace("\\", "", $row_event['content']) . "</div>";
				$social_section .= "<div class='date'>Posted on " . date("M d Y H:i", strtotime($row_event['dates'])) . "</div>";

				//TAKING THE COMMENTS
				$result_comment_edit = mysqli_query($conn, "SELECT * FROM event_comments WHERE id_event='" . $id_event . "'");

				$social_section .= "<div class='comment-row'><a href='social.php?comment=" . $row_event['id'] . "'><i class='fa fa-commenting' aria-hidden='true'></i> Comment</a> | " . mysqli_num_rows($result_comment_edit);
				mysqli_num_rows($result_comment_edit) == 1 ? $social_section .= " comment</div>" : $social_section .= " comments</div>";

				//NOW ADDING THE INPUT
				$social_section .= "<div class='comment-row'><form method='POST'>";
				$social_section .= "<textarea class='typeWBGin' type='text' name='comment' placeholder='Write a comment' style='max-width:85%; resize:none;'></textarea>";
				$social_section .= "<br/><br/><button type='submit' name='submit_comment' value='submit_comment'><i class='fa fa-paper-plane'></i>Post</button>";
				$social_section .="</form></div>";


				$i = 0;
				while ($row_comment_edit = mysqli_fetch_assoc($result_comment_edit))
				{
					if ($i < 20)
					{	

						//SELECTING THE AVATAR
						$result_comment_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_comment_edit['author_id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");
						$row_comment_avatar = mysqli_fetch_assoc($result_comment_avatar);

						//SELECTING THE NOOB TAG
						$result_noob_tag = mysqli_query($conn, "SELECT noob_tag FROM accounts WHERE id='" .  $row_comment_edit['author_id'] . "'");
						$row_noob_tag = mysqli_fetch_assoc($result_noob_tag);

						//AND THEN ADDING THE AVATAR
						if (!mysqli_num_rows($result_comment_avatar))
						{
							$social_section .= "<div class='comment-row'><div class='avatar' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
						}
						else
						{
							$social_section .= "<div class='comment-row'><div class='avatar' style='background-image: url(../images/avatars/" . $row_comment_avatar['file_name'] .");'></div>";
						}

						$social_section .= "<b>" . $row_noob_tag['noob_tag'] . "</b><div class='date-of-post'>Commented on " . date("M d Y H:i", strtotime($row_comment_edit['dates'])) . "</div><br/>" . str_replace("\\", "", $row_comment_edit['content']) . "</div>";
					}
					$i++;
				}

				$social_section .= "</div></div>";

				//INSERTING THE COMMENT INTO THE DB
				if (isset($_POST['submit_comment']))
				{	
					$comment = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['comment']));

					if (strlen($comment) > 300)
					{
						echo "Too long comment";
					}
					else
					{
						mysqli_query($conn, "INSERT INTO event_comments (id_event, author_id, content, dates) VALUES ('" . $row_event['id'] . "', '" . $id_user . "', '" . $comment . "', '" . date("Y-m-d H:i:s") . "')");
						echo "<meta http-equiv='refresh' content='0; url=social.php'>";
					}
				}
			}			
		}#END OF THE COMMENT EDITING SECTION
		else
		{
			//FORM FOR PUBLISHING SOCIAL EVENTS
			$post_form = "<form method='POST'>";
			$post_form .= "<textarea class='typeWBGin' name='post_content' placeholder='Write something for the public...'></textarea>";
			$post_form .= "<br/><br/><button type='submit' name='post_submit' value='post_submit'><i class='fa fa-pencil'></i>Post</button>";
			$post_form .= "</form><br/>";

			echo $post_form;

			//NOW REALLY PUBLISHING THE SOCIAL EVENTS
			if (isset($_POST['post_submit'])) {
				$content = htmlspecialchars(mysqli_real_escape_string($conn, $_POST['post_content']));

				if (strlen($content) > 300)
				{
					echo "Too long!";
				}
				else
				{
					mysqli_query($conn, "INSERT INTO social_events (id_user, content, type, dates) VALUES ('" . $id_user . "', '" . $content . "', '2', '" . date("Y-m-d H:i:s") . "')");
					echo "<meta http-equiv='refresh' content='0; url=social.php'>";
				}
			}

			//SELECTING THE EVENTS FROM social_events
			$result_friend_events = mysqli_query($conn, "SELECT * FROM social_events ORDER BY id DESC");

			//SETTING THE VARIABLE THAT HOLDS ALL OF THE SOCIAL INFO
			$social_section = "<div class='social-event-section'>";

			//COUNTER INITIALIZATION
			

			//ECHOING RESULTS FOR EVERY EVENT
			$i = 0;
			while ($row_friend_events = mysqli_fetch_assoc($result_friend_events))
			{
				$result_friendships = mysqli_query($conn, "SELECT * FROM friendships WHERE user_one_id='" . $id_user . "' AND user_two_id='" . $row_friend_events['id_user'] . "'");
				$result_friendships_sec = mysqli_query($conn, "SELECT * FROM friendships WHERE user_one_id='" . $row_friend_events['id_user'] . "' AND user_two_id='" . $id_user . "'");
				//NOT CHECKING CORRECTLY THE FRIENDSHIPS

				if (mysqli_num_rows($result_friendships) != null || mysqli_num_rows($result_friendships_sec) != null || $id_user == $row_friend_events['id_user'])
				{
					if ($row_friend_events['id_user'] == $id_user && $row_friend_events['type'] == 1)
					{
						//NO CONTENT FOR WHEN THE USER ID MATCHES AND THE TYPE IS EQUAL TO 1 JUST BECAUSE THERE IS NO NEED OFF SHOWING THE CLIENT HIS OWN PROGRESS THAT HE COULD FOLLOW FROM ELSEWHERE
					}
					else
					{
						//SHOULD MAKE IT SO THAT IT SELECTS ONLY YOUR FRIENDS!!!
						//INCREMENTING THE COUNTER IF THE EVENT IS A GOOD ONE
						$i++;

						//SELECTING USER DATA THAT MATCHES THE ID OF THE USER
						$result_user_data = mysqli_query($conn, "SELECT * FROM accounts WHERE id='" . $row_friend_events['id_user'] . "'");
						$row_user_data = mysqli_fetch_assoc($result_user_data);

						//OPENING THE A DIV THAT HOLDS THE EVENT INFO
						$social_section .= "<div class='event-box'>";

						//SELECTING AN AVATAR
						$result_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user = '" . $row_friend_events['id_user'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

						
						if (!mysqli_num_rows($result_avatar))
						{
							$social_section .= "<div class='head'><div class='avatar' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
						}
						else
						{
							$row_avatar = mysqli_fetch_assoc($result_avatar);
							$social_section .= "<div class='head'><div class='avatar' style='background-image: url(../images/avatars/" . $row_avatar['file_name'] . ");'></div>";
						}

						$social_section .= "<b>" . $row_user_data['noob_tag'] . "</b><div class='event-type'>" . ucfirst($event_types[$row_friend_events['type']]) .  "</div></div>";

						//ADDING THE CONTENT AND DATE OF POST
						$social_section .= "<div class='content'>" . str_replace('\\', '', $row_friend_events['content']) . "</div>";
						$social_section .= "<div class='date'>Posted on " . date("M d Y H:i", strtotime($row_friend_events['dates'])) . "</div>";
						
						//COMMENT SECTION
						$result_comments = mysqli_query($conn, "SELECT * FROM event_comments WHERE id_event='" . $row_friend_events['id'] ."' ORDER BY id DESC");

						$social_section .= "<div class='comment-row'><a href='social.php?comment=" . $row_friend_events['id'] . "'><i class='fa fa-commenting' aria-hidden='true'></i> Comment</a> | " . mysqli_num_rows($result_comments);
						mysqli_num_rows($result_comments) == 1 ? $social_section .= " comment</div>" : $social_section .= " comments</div>";

						$comment_counter = 0;
						while ($row_comments = mysqli_fetch_assoc($result_comments))
						{
							//SELECTING THE AVATAR
							$result_comment_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_comments['author_id'] . "' AND type='1' ORDER BY id DESC LIMIT 1");
							$row_comment_avatar = mysqli_fetch_assoc($result_comment_avatar);

							//SELECTING THE NOOB TAG
							$result_noob_tag = mysqli_query($conn, "SELECT noob_tag FROM accounts WHERE id='" .  $row_comments['author_id'] . "'");
							$row_noob_tag = mysqli_fetch_assoc($result_noob_tag);

							
							if ($comment_counter < 3)
							{	
								if (!mysqli_num_rows($result_comment_avatar))
								{
									$social_section .= "<div class='comment-row'><div class='avatar' style='background-image: url(../images/avatars/avatar_thumb.png);'></div>";
								}
								else
								{
									$social_section .= "<div class='comment-row'><div class='avatar' style='background-image: url(../images/avatars/" . $row_comment_avatar['file_name'] .");'></div>";
								}

								$social_section .= "<b>" . $row_noob_tag['noob_tag'] . "</b><div class='date-of-post'>Commented on " . date("M d Y H:i", strtotime($row_comments['dates'])) . "</div><br/>" . str_replace('\\', '', $row_comments['content']) . "</div>";
							}

							$comment_counter++;
						}
						$social_section .= "</div>";
						//SHOULD ADD THE PAGINATION MECHANISM AND THE TYPE OF THE EVENT IN THOSE BOXES TOMORROW, BUT NOW IT'S TIME FOR Zzz...
					}
				}
			}

			//IF THE COUNTER IS STILL A ZERO THEN TELL THE USER THAT THERE IS NO SOCIAL ACTIVITY IN THE SITE AMONG HIS FRIENDS
			$i == 0 ? $social_section .= "No new events" : $social_section .= "";
			$social_section .= "</div>";

		}
		
		//FINALLY ECHOING OUT THE SOCIAL SECTION
		echo $social_section;

		//CLOSING THE LEFT FRAGMENT
		echo "</div>";

		//HERE BEGINS THE RIGHT PART
		echo "<div class='fragment-social-parent'>";
		$url_friends = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$buddy_name = str_replace("/php/social.php?search_people=", "", $_SERVER['REQUEST_URI']);

		//Just for some user convenience
		if (!strpos($url_friends, "search_people"))
		{
			echo "<h2>Your buddies</h2><form method='GET'><input id='search_bar' class='typeWBGin' type='text' name='search_people' placeholder='Find friends' style='width:80%;'><br/><br/><button id='search_button'><i class='fa fa-search'></i>Search</button></form><br/>";
		}
		else
		{
			echo "<h2>Your buddies</h2><form method='GET'><input id='search_bar' class='typeWBGin' type='text' name='search_people' value='" . $buddy_name ."' placeholder='Find friends' style='width:80%;'><br/><br/><button id='search_button'><i class='fa fa-search'></i>Search</button> <button style='background-color:rgb(252,55,55);border-color:rgb(216,36,36);' type='submit' name='end_search' value='end_search'><i class='fa fa-times'></i>Close</button></form><br/>";
		}
		
		
		//If the user has searched already
		if (!strpos($url_friends, "search_people="))
		{
			$sql_select_friend = "SELECT * FROM friendships WHERE user_one_id='" . $id_user . "' OR user_two_id='" . $id_user . "'";
			$result_select_friend = $conn->query($sql_select_friend);
			//Whenever there is a match in the table get the accounts data
			$friends_list = "";

			if (!mysqli_num_rows($result_select_friend))
			{
				echo "You currently have no friends!";
			}
			else
			{
				while ($row_select_friend = $result_select_friend->fetch_assoc())
				{
					if ($row_select_friend['status'] == 1)
					{
						if ($row_select_friend['user_one_id'] != $id_user)
						{
							$query_buddy = "SELECT * FROM accounts WHERE id='" . $row_select_friend['user_one_id'] . "'";
						}
						else
						{
							$query_buddy = "SELECT * FROM accounts WHERE id='" . $row_select_friend['user_two_id'] . "'";
						}
						$result_buddy = $conn->query($query_buddy);
						$row_buddy = $result_buddy->fetch_assoc();

						$result_profile_image = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_buddy['id'] . "' AND type='1' ORDER by id DESC LIMIT 1");

						if (!mysqli_num_rows($result_profile_image))
						{
							$friends_list .= "<div class='friend-tab'><div class='avatar-thumb'></div>" . ucfirst(strtolower($row_buddy['first_name'])) . " " . ucfirst(strtolower($row_buddy['last_name'])) . "</div>";
						}
						else
						{	
							$row_profile_image = mysqli_fetch_assoc($result_profile_image);
							$friends_list .= "<div class='friend-tab'><div class='avatar-thumb' style='background-image: url(../images/avatars/" . $row_profile_image['file_name'] . ");'></div>" . ucfirst(strtolower($row_buddy['first_name'])) . " " . ucfirst(strtolower($row_buddy['last_name'])) . "</div>";
						}
					}
				}
				echo "<div class='friend-section'>" . $friends_list . "</div>";
			}
			


		}
		else
		{	
			
			$sql_buddy = "SELECT * FROM accounts WHERE noob_tag='" . $buddy_name ."'";
			$result_buddy = $conn->query($sql_buddy);

			if (!mysqli_num_rows($result_buddy)) {
				echo "<div class='error' style='width:80%;'><i class='fa fa-frown-o fa-big' aria-hidden='true'></i> Sorry but no matches!</div>";
			}
			else
			{	
				while ($row_buddy = $result_buddy->fetch_assoc())
				{	
					$sql_friendship_one = "SELECT * FROM friendships WHERE user_one_id='" . $id_user . "' AND user_two_id='" . $row_buddy['id'] . "'";
					$result_friendship_one = $conn->query($sql_friendship_one);
					$sql_friendship_two = "SELECT * FROM friendships WHERE user_two_id='" . $id_user . "' AND user_one_id='" . $row_buddy['id'] . "'";
					$result_friendship_two = $conn->query($sql_friendship_two);
					//Should go for the second case as well
					
					if (!mysqli_num_rows($result_friendship_one) && !mysqli_num_rows($result_friendship_two) && $buddy_name != $username)
					{
						$friend_output = "<div class='friend-tab'>" . ucfirst(strtolower($row_buddy['first_name'])) . " " . ucfirst(strtolower($row_buddy['last_name'])) . "</div>";
					}
					else
					{	
						//It's ordered in such a way so always your friends in the database will be on top :)
						$friend_output = "<div class='friend-tab'>" . ucfirst(strtolower($row_buddy['first_name'])) . " " . ucfirst(strtolower($row_buddy['last_name'])) . "</div>" . $friend_output;
					}
				}

				echo "<div class='friend-section'>" . $friend_output . "</div>";
			}

			if (isset($_GET['end_search']))
			{
				echo "<meta http-equiv='refresh' content='0; url=social.php'>";
				exit();
			}

			
			/*$vowels = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U");
			$onlyconsonants = str_replace($vowels, "", "Hello World of PHP");
			echo $onlyconsonants;*/
		}
		
		echo "</div></body></html>";
		?>
		<script type="text/javascript">
			

			function IntervalButton() {
				var input = document.getElementById('search_bar');
				var searchButton = document.getElementById('search_button');

				setInterval(function(){if (!input.value)
				{	
					searchButton.disabled = true;
					searchButton.style.backgroundColor = 'grey';
					searchButton.style.borderColor = 'rgb(120,120,120)';
					searchButton.style.cursor = 'default';
				}
				else
				{
					document.getElementById('search_button').disabled = false;
					searchButton.style.backgroundColor = '#49a7ff';
					searchButton.style.borderColor = '#0084ff';
					searchButton.style.cursor = 'pointer';
				}}, 10)
			}
			

			IntervalButton();
			
		</script>
	<?php
	}
	
