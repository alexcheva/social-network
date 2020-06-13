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
 	 	<p>Modify the values and click "Update Details":</p>
 	 	<form action="settings.php" method="POST">
 	 		<label>First Name:</label>
 	 		<input type="text" name="first_name" value="<?php echo $user['first_name']; ?>">
 	 		<label>Last Name:</label>
 	 		<input type="text" name="last_name" value="<?php echo $user['last_name']; ?>">
 	 		<label>Username:</label>
 	 		<input type="text" name="username" value="<?php echo $user['username']; ?>">

 	 	</form>
 	 </div>

 </div>