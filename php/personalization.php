<?php
	require 'dbconnect.php';
	include 'header.php';
	require 'functions.php';
?>

				<div class="fragment-left">
					<h1 style="display:inline;">Sign up </h1><h3 style="display:inline;">* account personalization</h3><hr/>
					<p algin="center">
						<div class="registerStep done">1</div>
						<div class="registerStep done">2</div>
						<div class="registerStep done">3</div>
					</p>



					<form action="check_personalization.php" method="POST">
						
						<?php
						$noob_tag = strip_tags($_POST['noob_tag']);
						echo "<input type='hidden' name='noob_tag' value='" . $noob_tag . "'>";
						?>
						<label><b>Experience with our platform</b></label><br/>
						<select class="white-select" name="experience">
							<option disabled="disabled" selected="selected">Select your skill</option>
							<option>Newbie</option>
							<option>Semi experienced</option>
							<option>Smurf</option>
						</select>

						<br/>
						<label><b>Description</b></label>
						<textarea class="typeWBGin" type="text" placeholder="Tell us something interesting about yourself.../max 300 symbols/" name="description"></textarea><br/>
						<label><b>Phone Number</b></label>
						<input class="typeWBGin orange" type="text" placeholder="/between 8 and 16 symbols/" name="phone_number"><br/><br/>
						
						<button type="submit" name="submit" value="submit">Personalize</button>



						<!--<h1 align="center">Let's make this a bit more personal!</h1><hr/>
						<br/>
						
						<div id="canvasEdit">
						<a class="button orangeButton" type="submit" name="selectImage">Gallery</a>
						<button id="snapButton" onclick="Snap();">Snap</button>
						<button id="resetButton" onclick="Reset();">Reset</button><br/>
						</div>-->
						<!--<button onclick="grayscale();">Grayscale</button>-->
						
						<!--<video id="video" onclick="Snap();" class="videoCircle" height="250" autoplay muted></video>
						<canvas id="canvas" onclick="Reset();"></canvas><br/>-->

					</form>



					<!--<button onload="PageShifter();">Click me</button>-->

					

					<!--<div id="page">
						
					</div>-->
				</div>
				<br/>
				<?php
					include 'footer.php';
				?>
				<script src="../js/SnapPhoto.js" type="text/javascript"></script>
		</div>
	</body>
</html>