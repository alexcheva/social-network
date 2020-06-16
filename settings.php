<?php 
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");

 ?>

 <div class="main_column column">
 	
 	<h2 id="message">Account Settings:</h2>
 	<h4>Profile Picture:</h4>
 	<?php 
 	echo "<img src='" . $user['profile_pic'] . "' id='small_profile_pic'>";
 	 ?>
 	 <div>
 	 	<p><a href="upload.php">Upload new profile picture</a></p>
 	 	<h4>Account Details:</h4>
 	 	<p>Modify the values and click "Update Details":</p>

 	 	<?php 
		$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
		$row = mysqli_fetch_array($user_data_query);
		$first_name = $row['first_name'];
		$last_name = $row['last_name'];
		// $username = $row['username'];
		$email = $row['email'];
 	 	 ?>
 	 	<form class="settings" action="settings.php#message" method="POST">
 	 		<?php echo $message; ?>
<!--  	 		<input type="hidden" name="id" value="<?php echo $user['id']; ?>">			 --> 	 		
			<label>First Name:</label>
 	 		<input type="text" name="first_name" value="<?php echo $first_name; ?>">
 	 		<label>Last Name:</label>
 	 		<input type="text" name="last_name" value="<?php echo $last_name; ?>">
 	 		<!-- <label>Username:</label>
 	 		<input type="text" name="username" value="<?php echo $username; ?>"> -->
 	 		<label>E-mail:</label>
 	 		<input type="text" name="email" value="<?php echo $email; ?>">
 	 		<input type="submit" name="update_details" class="save_details" value="Update Details">
 	 	</form>
 	 	<h4>Change Password:</h4>
 	 	<form class="settings" action="settings.php#password_message" method="POST">
 	 		<?php echo $password_message; ?>
 	 		<label>Old Password:</label>
 	 		<input type="password" name="old_password">
 	 		<label>New Password:</label>
 	 		<input type="password" name="new_password1">
 	 		<label>New Password Again:</label>
 	 		<input type="password" name="new_password2">
 	 		<input type="submit" name="update_password" class="save_details" value="Update Password">
 	 	</form>
 	 	<h4>Close Account:</h4>
 	 	
 	 		<input type="submit" data-toggle="modal" data-target="#close_account_form" id="danger" value="Close Account">
 	 	
 	 </div>

 </div>
 	<!-- Modal -->
	<div class="modal fade" id="close_account_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <h4 class="modal-title" id="postModalLabel">Are you sure you want to close your account?</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      </div>

	      <div class="modal-body">
	      	<p>Closing your account will hide your profile and all your activity from other users.</p>
	      	<p>You can re-open your account at any time by simply logging in.</p>
	      </div>

	      <div class="modal-footer">
	        <form action="settings.php" method="POST">
	        <input type="submit" id="close_account" name="close_account" value="Yes! Close it.">
	        <input type="button" id="dont_close" data-dismiss="modal" value="No way! Leave it.">
	        </form>
	      </div>
	    </div>
	  </div>
	</div>