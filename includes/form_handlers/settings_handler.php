<?php 

if(isset($_POST['update_details'])){
	// $id = $_POST['id'];
	$first_name = strip_tags($_POST['first_name']);
	$last_name = strip_tags($_POST['last_name']);
	// $username = $_POST['username'];
	$email = $_POST['email'];
	//check if the email is in use:
	$email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn){
		$message = "<p class='success'>Details have been sucessfully updated!</p>";

		$query = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");

	}else
	$message = "<p class='error'>That email is already in use!</p>";

} else
$message = "";
// ********************************
if(isset($_POST['update_password'])){
	$old_password = strip_tags($_POST['old_password']);
	$new_password1 = strip_tags($_POST['new_password1']);
	$new_password2 = strip_tags($_POST['new_password2']);

	$password_query = mysqli_query($con, "SELECT password FROM users WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($password_query);
	$db_password = $row['password'];
	//if old password match the one in database:
	if(md5($old_password) == $db_password){

		if($new_password1 == $new_password2){
			//check if password longer then 4 characters
			if(strlen($new_password1) <= 4){
				$password_message = "<p id='password_message' class='error'>Your new password must to be greater than 4 characters!</p>";
			}else{
				$new_password_md5 = md5($new_password1);
				$password_query = mysqli_query($con, "UPDATE users SET password='$new_password_md5' WHERE username='$userLoggedIn'");
				$password_message = "<p id='password_message' class='success'>Success! Password has been updated!</p>";
			}

		}else{
			$password_message = "<p id='password_message' class='error'>Your two new password doesn't match!</p>";
		}
	}else
	$password_message = "<p id='password_message' class='error'>Your old password doesn't match!</p>";

} else
$password_message = "";

?>
