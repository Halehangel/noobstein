<?php
    require '../../session_start.php';
    require '../../dbconnect.php';
    include '../../functions.php';
    date_default_timezone_set("Europe/Sofia");

    $id_game = 2;
	  $_SESSION['id_game'] = $id_game;

    $url_array = explode(".", $_SERVER['REQUEST_URI']);
    $another_split = explode("/", $url_array[0]);
    $actual_name = end($another_split);
    $nav = '<div class="pageWrap"><header><div class="nav-bar"><div class="nav-box-1"><ul class="nav">';
?>


<!doctype html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
    <title>Noobstein &bull; Mathrix</title>
    <link rel="stylesheet" href="../TemplateData/style.css">
    <script src="https://use.fontawesome.com/0b97bf7972.js"></script>

    <link rel="shortcut icon" href="../../../images/favicon.png" />
    <script src="TemplateData/UnityProgress.js"></script>
    <script src="../../../js/functions.js"></script>
  </head>
  <body class="template">
    <?php
      include '../game_header.php';
    ?>
    <div class='fragment-left'>
      <h1>Mathrix</h1><hr />
            <h3>Play fullscreen 
                <div class="fullscreen" onclick="SetFullscreen(1);">
                    <i class="fa fa-arrows-alt" aria-hidden="true"></i>
                </div>
            </h3><br/>
      <div class="template-wrap clear">
        <canvas class="emscripten" id="canvas" oncontextmenu="event.preventDefault()" height="600px" width="960px"></canvas>
      </div>

      <h3 style='display:inline-block;'>Rating 
      <?php
          $result_rating = mysqli_query($conn, "SELECT * FROM game_rating WHERE id_game='" . $id_game . "'");
           $rating = 0;

          if (!mysqli_num_rows($result_rating))
          {
            $i = 0;

            while ($i < 5)
            {
              echo "<i class='fa fa-star' aria-hidden='true'></i>";
              $i++;
            }


          }
          else
          {

            while ($row_rating = mysqli_fetch_assoc($result_rating)) {
              $rating += $row_rating['rate'];
            }

            $exact_rating = round($rating/mysqli_num_rows($result_rating), 0, PHP_ROUND_HALF_UP);

            $i = 0;
            while ($i < $exact_rating)
            {
                echo "<i class='fa fa-star good-star' aria-hidden='true'></i>";
                $i++;
            }
            $bad = 5 - $i;

            while ($bad > 0)
            {
                echo "<i class='fa fa-star' aria-hidden='true'></i>";
                $bad--;
            }
          }
          
      ?>
      </h3>

      <?php
      echo Favourites($conn, $id_user, $id_game);    
      ?>

    <?php
        GameRating($conn, $id_game, $id_user, $status);
    ?>

      <br/><br/>
      <h2>Game lore and rules</h2><hr/>
      <p style="text-align: justify;">Dodge the constantly incoming numbers by jumping around while gaining points. Points are earned over time and so the longer you'll survive the higher score you'll have in a game of Mathrix.<br/> NOTE OUT that the inclined platforms on the right and the left are bouncy!</p>
      <h2>Game controls</h2><br/>
      <div class='controls-table'>
          <div class="head">
              <div class='left-part'>Controls</div>
              <div class='right-part'>Action</div>
          </div>
          <div class="row">
              <div class='left-part'>A, D</div>
              <div class='right-part'>Movement</div>
          </div>
          <div class="row">
              <div class='left-part'>Ctrl</div>
              <div class='right-part'>Crawling</div>
          </div>
          <div class="row">
              <div class='left-part'>Mouse click</div>
              <div class='right-part'>Slow down objects</div>
          </div>
      </div>
      <br/>
        <?php
            echo GameComments($conn, $id_user, $id_game, $status);
        ?>
    </div>
    <?php
            echo "<div class='fragment-right' style='width:25% !important;'><div class='fragmentRightPart'>";
            News($conn, $id_user);
            echo Socials();
            echo "</div></div>";
        ?>
    <script type='text/javascript'>
  var Module = {
    TOTAL_MEMORY: 268435456,
    errorhandler: null,			// arguments: err, url, line. This function must return 'true' if the error is handled, otherwise 'false'
    compatibilitycheck: null,
    backgroundColor: "#222C36",
    splashStyle: "Light",
    dataUrl: "Release/Desktop.data",
    codeUrl: "Release/Desktop.js",
    asmUrl: "Release/Desktop.asm.js",
    memUrl: "Release/Desktop.mem",
  };
</script>
<script src="Release/UnityLoader.js"></script>

  </body>
</html>
