<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<title>NoobStein</title>
		<link rel="shortcut icon"/>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="stylesheet" href="../css/style.css" type="text/css" />
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

		<!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>-->
	</head>
	<body>
		<div class="pageWrap">
			<header>
				<div class="navBar">
					<div class="logo">
					<div class="navBox1">
					<ul class="nav">
						<li class="nav"><a class="nav navPrimary" href="index.php">Home</a></li>
						<li class="nav"><a class="nav navPrimary" href="#">Auction House</a></li>
						<li class="nav"><a class="nav navPrimary" href="games.html">Games</a></li>
					</ul>
					</div>
					</div>
					<div class="navBox2">
						<ul class="nav">
							<li class="nav"><a class="nav navPrimary" href="#">Log in</a></li>
							<li class="nav"><a class="nav navPrimary" href="#">Sign up</a></li>
						</ul>
					</div>
				</div>
			</header>

			<div class="fragmentsWrapperDefault">
				<div class="fragmentFill">
					<h1 align="center">Let's make this a bit more personal!</h1>
					<br/>
					<video id="video" onclick="Snap();" class="videoCircle" height="250" autoplay muted></video>
					<canvas id="canvas" onclick="Reset();"><!--<div id="resetCanvas">reset</div>--></canvas><br/>
					<p align="center">
					<a class="button orangeButton" type="submit" name="selectImage">Gallery</a>
					<button id="snapButton" onclick="Snap();">Snap</button>
					<button id="resetButton" onclick="Reset();">Reset</button>
					</p>
				</div>
			</div>

			

		</div><!--Here ends pageWrap-->
		<footer class="footer">
			<div class="footBox">
				<ul class="foot">
					<li class="foot"><a class="foot" href="#">Contacts</a></li>
					<li class="foot"><a class="foot" href="#">Donate</a></li>
					<li class="foot"><a class="foot" href="#">About</a></li>
					<li class="foot"><a class="foot" href="#">License agreement</a></li>
				</ul>
			</div>
		</footer>
		<script src="../js/SnapPhoto.js" type="text/javascript"></script>

	</body>
</html>