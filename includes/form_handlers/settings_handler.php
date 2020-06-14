<?php 

if(isset($_POST['update_details'])){
	// $id = $_POST['id'];
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	// $username = $_POST['username'];
	$email = $_POST['email'];
	//check if the email is in use:
	$email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);
	$matched_user = $row['username'];

	if($matched_user == "" || $matched_user == $userLoggedIn){
		$message = "<p>Details updated!</p>";

		$query = mysqli_query($con, "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email' WHERE username='$userLoggedIn'");

	}else
	$message = "<p>That email is already in use!</p>";

} else
$message = "";


?>
