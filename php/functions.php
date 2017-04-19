<?php

    function RandomString($length = 10)
	{
        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $random_string = '';
        for ($i = 0; $i < $length; $i++) {
            $random_string .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $random_string;
    }

	function ImageUpload($conn, $id_user, $size_limit)
	{
		if (isset($_POST['upload_image']))
		{
			//For function should a type var, size limit and I think that's it for now
			$file = $_FILES['image'];
			$file_db_type = $_POST['image_type'];

			$file_name = $file['name'];
			$file_type = $file['type'];
			$file_tmp_name = $file['tmp_name'];
			$file_error = $file['error'];
			$file_size = $file['size'];
			
			$file_ext = explode('.', $file_name);
			$file_actual_ext = strtolower(end($file_ext));

			$allowed = array('jpg', 'jpeg', 'png');

			if (in_array($file_actual_ext, $allowed)) 
			{
				if ($file_error === 0)
				{
					if ($file_size < $size_limit)
					{
						$file_new_name = $id_user . "_" .  uniqid('', true) . '.' . $file_actual_ext;

						$type_array = array('background', 'avatar');

						$result_upload = mysqli_query($conn, "INSERT INTO profile_images (id_user, file_name, type) VALUES ('" . $id_user . "', '" . $file_new_name . "', '" . $file_db_type . "')");
						$file_destination = "../images/" . $type_array[$file_db_type] . "s/" . $file_new_name;

						move_uploaded_file($file_tmp_name, $file_destination);

						echo "<meta http-equiv='refresh' content='0; url=http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "'>";

					}
					else
					{
						echo "Your file is too big!";
					}
				}
				else
				{
					echo "There was an error uploading your file!";
				}
			}
			else
			{
				echo "The file format you have submitted isn't supported!";
			}
			
		}
	}

	//A function used to paginate numerous data rows taken from a db
	function Pagination($current_page, $num_pages, $url, $first_cubes = 5, $last_cubes = 2)
	{
		//Instancing a variable for the cubes
		$cubes = "<div class='cube-wrapper'>";
		
		//This is probably just for the aesthetics -> Taking the number of pages
		if ($num_pages > ($first_cubes + $last_cubes))
		{	
			//If the current page is over the first and the last cubes and if the it is less than the total number of pages then generate an offset of the last page :)
			if ($current_page > ($first_cubes + $last_cubes) && ($current_page < $num_pages))
			{	
				$page_offset = $num_pages - $current_page;
			}

			//For the first pages
			if ($current_page > ($first_cubes + $last_cubes))
			{	
				//This is for the appearance of the first pages so that would make everyone's life easier
				$first_pages = 0;
				do {
					//Incrementing the first pages before applying them to the cubes variable
					$first_pages++;
					$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $first_pages . "'>" . $first_pages . "</a>";
				} while ( $first_pages < $first_cubes);

				//Just some marking
				$cubes .= " ...";

				//If the current page is equal to number of pages
				if ($current_page == $num_pages)
				{	
					//Changing the start counter from which starts the generating of cubes
					$i = $current_page - 2;
				}
				else
				{	
					//Changing the start counter if it isn't so
					$i = $current_page - 1;
				}

				//If we are close to the last page
				$last_page = $current_page + 2;

				//If the last page number get's over the number of pages then make it equal to that so there won't be any glitches
				if ($last_page >= $num_pages)
				{	
					$last_page = $num_pages;
				}

				//Echoing the final cubes
				do {
					$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $i . "'>" . $i . "</a>";
					$i++;
				} while ($i <= $last_page);
			}
			else
			{
				$i = 0;
				if ($current_page > ($first_cubes/2))
				{
					$i = $current_page - round(($first_cubes/2), 0, PHP_ROUND_HALF_DOWN);
					$first_cubes = $current_page + ($first_cubes/2);

					do {
						//Just a simple incrementation
						
						$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $i . "'>" . $i . "</a>";
						$i++;
					} while ( $i < $first_cubes);
				}
				else
				{
					do {
						//Just a simple incrementation
						$i++;
						$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $i . "'>" . $i . "</a>";
						
					} while ( $i < $first_cubes);
				}

				

				//Just a small gap between them
				$cubes .= " ...";

				//Changing the value of the counter to the desired one trough the use of the last_cubes variable
				$i = $num_pages - $last_cubes;
				//Echoing the final cubes
				do {
					$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $i . "'>" . $i . "</a>";
					$i++;
				} while ($i <= $num_pages);
			}
			

		}
		elseif ($num_pages == 1 || $num_pages <= 1)
		{}
		//If the number of pages is less
		else
		{
			do {
				$i++;
				$cubes .= "<a class='page-cube' href='" . $url . ".php?page=" . $i . "'>" . $i . "</a>";
			} while ( $i < $num_pages);
		}

		$cubes .= "</div>";

		return $cubes;
	}

	function News($conn, $id_user)
	{
		echo "<h2>Latest news</h2>";
		$query_select_news = "SELECT * FROM news ORDER BY id DESC LIMIT 3";
		$result_select_news = $conn->query($query_select_news);

		while ($row_select_news = $result_select_news->fetch_assoc()) 
		{
			$news_data = "<div class='note news'>";
			//Checking if news has been seen
			$result_views_client = mysqli_query($conn, "SELECT * FROM news_views WHERE id_user='" . $id_user . "' AND id_news='" . $row_select_news['id'] . "'");
			$result_views = mysqli_query($conn, "SELECT * FROM news_views WHERE id_news='" . $row_select_news['id'] . "'");

			if (!mysqli_num_rows($result_views_client))
			{
				$news_data .= "<h4>" . $row_select_news['title'] . "<div class='views'><i class='fa fa-eye' aria-hidden='true'></i>" . (mysqli_num_rows($result_views) + 1) . "</div></h4>";
				mysqli_query($conn, "INSERT INTO news_views (id_news, id_user) VALUES ('" . $row_select_news['id'] . "', '" . $id_user . "')");
			}
			else
			{
				$news_data .= "<h4>" . $row_select_news['title'] . "<div class='views'><i class='fa fa-eye' aria-hidden='true'></i>" . mysqli_num_rows($result_views) . "</div></h4>";
			}
			
			$news_data .= str_replace('\\', '', $row_select_news['content']);
			$news_data .= "</div>";
			$news_data .= "<div class='date'>" . date("M d Y H:i", strtotime($row_select_news['date'])) . "</div>";
			echo $news_data;
		}
	}

	function Feedback($conn, $id_user)
	{	
		echo "<br/><h2>Feedback</h2>";
		$form_feedback = "<form method='POST'>";
		$form_feedback .= "<input type='hidden' name='feedback_date' value='" . date("Y-m-d H:i:s") . "'>";
		//$form_feedback .= "<input class='typeWBGin' type='email' name='feedback_email' placeholder='E-mail'><br/><br/>";
		$form_feedback .= "<input type='hidden' name='id' value='" . $id_user . "'>";
		$form_feedback .= "<textarea class='typeWBGin' name='feedback_content' placeholder='Say something about our performance...'></textarea><br/><br/>";
		$form_feedback .= "<button type='submit' name='feedback_submit' value='feedback_submit'><i class='fa fa-paper-plane' aria-hidden='true'></i> Submit</button>";
		$form_feedback .= "</form>";
		echo $form_feedback;

		$url = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		if (strpos($url, "?")) 
		{
			$basic_url = explode('?', $url, 5);
			$url = $basic_url[0];
			$error = $basic_url[1];
		}


		if (isset($_POST['feedback_submit'])) 
		{	
			$result_email = mysqli_query($conn, "SELECT email FROM accounts WHERE id='" . $id_user . "'");
			$row_email = mysqli_fetch_assoc($result_email);
			$feedback_email = $row_email['email'];
			$feedback_content = strip_tags($_POST['feedback_content']);
			$feedback_date = $_POST['feedback_date'];

			$feedback_email_length = strlen($feedback_email);
			$feedback_content_length = strlen($feedback_content);

			$sql_feedback = "INSERT INTO feedback (email, content, date) VALUES ('" . $feedback_email . "', '" . $feedback_content . "', '" . $feedback_date . "')";

			if (empty($feedback_content))
			{
				echo "<meta http-equiv='refresh' content='0; url=" . $url . "?error=empty'>";
			}
			else
			{
				if ($feedback_email_length > 40) 
				{
					echo "<meta http-equiv='refresh' content='0; url=" . $url . "?error=email'>";
				}
				else 
				{
					if ($feedback_content_length > 300) 
					{
						echo "<meta http-equiv='refresh' content='0; url=" . $url . "?error=length'>";
					} 
					else 
					{
						$result_feedback = mysqli_query($conn, $sql_feedback);
						
						//should add something from the sort of dont't bother me You have already given me the same content and should add a thing that limits the number of feedbacks daily!
						echo "<meta http-equiv='refresh' content='0; url=" . $url . "?error=success'>";
					}
				}
			}
		}

		if ($error == 'error=success')
		{
			echo "<div class='note'><div class='error-title success-title'><i class='fa fa-exclamation-circle'></i>Success</div><div class='note-content'>Thank you for letting us know what you think about our services! Have a nice day! :)</div></div>";

		}
		elseif ($error == 'error=length')
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Data in the content box should not be bigger than 300 symbols!</div></div>";
		}
		elseif ($error == 'error=email')
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>The email you have entered is way too long!</div></div>";
		}
		elseif ($error == 'error=empty')
		{
			echo "<div class='note'><div class='error-title'><i class='fa fa-exclamation-circle'></i>Error</div><div class='note-content'>Empty fields</div></div>";
		}
	}

	function Socials()
	{
		$socials = "<br/><h2>Follow us on social media</h2>";
		$socials .= "<a href='https://www.facebook.com/Noobstein-935172936585710/' target='_blank'><i class='fa fa-facebook-square socials' aria-hidden='true'></i></a>";
		$socials .= "<a href='twitter.com' target='_blank'><i class='fa fa-twitter-square socials' aria-hidden='true'></i></a>";
		$socials .= "<a href='google.com' target='_blank'><i class='fa fa-google-plus-square socials' aria-hidden='true'></i></a>";
		$socials .= "<a href='reddit.com' target='_blank'><i class='fa fa-reddit-square socials' aria-hidden='true'></i></a>";

		return $socials;
	}

	function ProgressBar($total_money_earned, $title, $round_str=' km')
	{
		$string = "<b>" . $title . "</b><br/>";

		$round_str = ' km';

		$i = 0;
		while ($total_money_earned >= 1000) 
		{
			$total_money_earned -= 1000;
			$i++;
		}

		$i_value = $i;
		if ($i >= 1)
		{
			$round_index = 1;
			while ($i > 1000)
			{
				$round_index++;
				$i -= 1000;
				$i_value++;
			}
		}
		//SO IT WON'T CHANGE
		$i = $i_value;

		do {
			$filling = round(($total_money_earned/10), 2, PHP_ROUND_HALF_DOWN);
		} while ($filling > 100);
		
		$current_value_output = number_format(($total_money_earned + $i * 1000), "0", "", " ");
		$achiev_value_output = number_format((1000 * ($i+1)), "0", ",", " ");


		$string .= "<div class='progress-bar-wrapper'><div class='filling' style='width: " . $filling . "%;'></div><div class='text'>" . $current_value_output . " / " . $achiev_value_output . "</div></div>";

		return $string;
	}

	function GameComments($conn, $id_user, $game_id, $status = 0)
	{	
		date_default_timezone_set("Europe/Sofia");
		$comments = "<h2>Latest comments</h2>";

		if ($status == 1)
		{
			$comments .= "<a class='button' href='../../game_feedback.php?game=" . $game_id . "'>Leave a comment</a>";
		}

		$sql_comments = "SELECT * FROM game_comments WHERE id_game='" . $game_id . "' ORDER BY id DESC LIMIT 5";
		$result_comments = mysqli_query($conn, $sql_comments);

		while ($row_comments = mysqli_fetch_assoc($result_comments))
		{
			$comments .= "<div class='comment' style='top:-10px;'>";

			$result_id = mysqli_query($conn, "SELECT email FROM accounts WHERE id='" . $row_comments['id_user'] . "'");
			$row_id = mysqli_fetch_assoc($result_id);

			$result_search_avatar = mysqli_query($conn, "SELECT file_name FROM profile_images WHERE id_user='" . $row_comments['id_user'] . "' AND type='1' ORDER BY id DESC LIMIT 1");

			if (!mysqli_num_rows($result_search_avatar))
			{
				$comments .= "<div class='head'><div class='avatar-thumb'></div>";
			}
			else
			{
				$row_search_avatar = mysqli_fetch_assoc($result_search_avatar);
				$comments .= "<div class='head'><div class='avatar-thumb' style='background-image: url(../../../images/avatars/" . $row_search_avatar['file_name'] . ");'></div>";
			}
			
			$comments .= $row_id['email'] . "</div>";
			$comments .= "<div class='content'>\"" . $row_comments['content'] . "\"</div>";
			$comments .= "<div class='date'>Sent on " . date("M d Y H:i", strtotime($row_comments['date'])) . "</div>";
			$comments .= "</div>";
		}

		return $comments;
	}

	function GameRating($conn, $id_game, $id_user, $status = 0)
	{	
		if ($status == 1)
		{
			//Rating form
			$form_rating = "<form method='POST'>";
	        $form_rating .= "<select class='white-select select-small' name='stars'>";
	        $form_rating .= "<option>5</option>";
	        $form_rating .= "<option>4</option>";
	        $form_rating .= "<option>3</option>";
	        $form_rating .= "<option>2</option>";
	        $form_rating .= "<option>1</option>";
	        $form_rating .= "</select>&nbsp;";
	        $form_rating .= "<button type='submit' name='submit_rating' value='submit_rating' style='top:14px; vertical-align: bottom;'>Rate</button>";
	        $form_rating .= "</form>";

	        echo $form_rating;#Echoing it out

	        if (isset($_POST['submit_rating']))
			{
			  $stars = mysqli_real_escape_string($conn, $_POST['stars']);

			  $result_rating = mysqli_query($conn, "SELECT * FROM game_rating WHERE id_game='" . $id_game . "' AND id_user='" . $id_user . "'");

			  if (!mysqli_num_rows($result_rating))
			  {
			      mysqli_query($conn, "INSERT INTO game_rating (id_user, id_game, rate) VALUES ('" . $id_user . "', '" . $id_game . "', '" . $stars . "')");
			  }
			  else
			  {
			      mysqli_query($conn, "UPDATE game_rating SET rate='" . $stars . "' WHERE id_game='" . $id_game . "' AND id_user='" . $id_user . "'");
			  }

			  echo "<meta http-equiv='refresh' content='0; url=index.php'>";
			}
		}
		
	}

	function Favourites($conn, $id_user, $id_game)
	{	
		//Selecting the favourites
		$result_favourite = mysqli_query($conn, "SELECT id_user FROM favourite_games WHERE id_game='" . $id_game . "'");


        $favs = "<h3 style='display:inline-block; float:right'>" . mysqli_num_rows($result_favourite) . " ";

        while ($row_favourite = mysqli_fetch_assoc($result_favourite))
        {
            if ($row_favourite['id_user'] == $id_user)
            {
                $found = true;
            }
        }

        if ($found == true)
        {
            $favs .= "<i class='fa fa-heart good-heart' aria-hidden='true'></i>";
        }
        else
        {
        	$favs .= "<form method='POST' style='display:inline-block;'>";
        	$favs .= "<input type='hidden' name='id' value='" . $id_user . "'/>";
        	$favs .= "<input type='hidden' name='game' value='" . $id_game . "' />";
       		$favs .= "<button class='no-styling' name='fav_submit' value='fav_submit'><i class='fa fa-heart bad-heart' aria-hidden='true'></i></button>"; 
           	$favs .= "</form>";
        }

        $favs .= " Favourite</h3>";

        if (isset($_POST['fav_submit']))
        {
        	$id_user = mysqli_real_escape_string($conn, $_POST['id']);
        	$game_id = mysqli_real_escape_string($conn, $_POST['game']);

        	$sql = "INSERT INTO favourite_games (id_user, id_game) VALUES ('" . $id_user . "', '" . $game_id . "')";
        	$result = $conn->query($sql);

        	echo "<meta http-equiv='refresh' content='0; url=" . $_SERVER['REQUEST_URI'] . "' >";
        }

        return $favs;
	}