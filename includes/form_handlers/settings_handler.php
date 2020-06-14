<?php 

if(isset($_POST['update_detais'])){
	$first_name = $_POST['first_name'];
	$last_name = $_POST['last_name'];
	$username = $_post['username'];
	$email = $_POST['email'];

	$email_check = mysqli_query($con, "SELECT * FROM users WHERE email='$email'");
	$row = mysqli_fetch_array($email_check);


}

?>
