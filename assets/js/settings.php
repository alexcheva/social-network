<?php 
include("includes/header.php");
// include("includes/settings_handler.php");

 ?>

 <div class="main_column column">
 	
 	<h4>Account Settings</h4>
 	<?php 
 	echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pic'>";
 	 ?>
 	 <div>
 	 	<a href="upload.php">Upload new profile picture</a>
 	 </div>

 </div>