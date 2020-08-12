<?php 
//Declaring variables
$fname = "";
$lname = "";
$username = "";
$em = "";
$em2 = "";
$pass = "";
$pass2 = "";
$date = "";
$error_array= array();

if(isset($_POST['register_button'])){

	$fname = strip_tags($_POST['req_fname']);
	$fname = str_replace(' ', '', $fname);
	$fname = ucfirst(strtolower($fname));
	$_SESSION['req_fname'] = $fname;
	
	$lname = strip_tags($_POST['req_lname']);
	$lname = str_replace(' ', '', $lname);
	$lname = ucfirst(strtolower($lname));
	$_SESSION['req_lname'] = $lname;

	$username = strip_tags($_POST['req_username']);
	$username = str_replace(' ', '', $username);
	$_SESSION['req_username'] = $username;

	$em = strip_tags($_POST['req_email']);
	$em = str_replace(' ', '', $em);
	$_SESSION['req_email'] = $em;

	$em2 = strip_tags($_POST['req_email2']);
	$em2 = str_replace(' ', '', $em2);
	$_SESSION['req_email2'] = $em2;


	$password = strip_tags($_POST['req_password']);
	$password2 = strip_tags($_POST['req_password2']);
	$date = date("Y-m-d");

	if($em == $em2){
		if(filter_var($em, FILTER_VALIDATE_EMAIL)){
			$em = filter_var($em, FILTER_VALIDATE_EMAIL);
				//check if email ecxists:
				$e_check = mysqli_query($con, "SELECT email FROM users WHERE email='$em'");
				$num_rows = mysqli_num_rows($e_check);
				if($num_rows > 0){
					array_push($error_array, "<span class='error'>This email is already in use.</span>");
				}
		} else {
			array_push($error_array, "<span class='error'>Invalid email format.</span>");
		}
	}
	else {
		array_push($error_array, "<span class='error'>Emails don't match.</span>");
	}
	// if(strlen($username) > 30 || strlen($username) < 3) {
	// 	array_push($error_array, "<span class='error'>Your username must be between 3 and 30 characters.</span>");
	// }
	if(preg_match('/^[a-z\d_]{5,25}$/i', $username)){
		$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		$num_rows = mysqli_num_rows($check_username_query);
			if($num_rows > 0){
				array_push($error_array, "<span class='error'>This username is already in use.</span>");
			}
		
	}else{
		array_push($error_array, "<span class='error'>Username can only contain english letters, numbers and underscores. And it must be between 5 and 25 characters.</span>");
		}
	
	if(strlen($fname) > 25 || strlen($fname) < 2) {
		array_push($error_array, "<span class='error'>Your First name must be between 2 and 25 characters.</span>");
	}
	if(strlen($lname) > 50 || strlen($lname) < 2) {
		array_push($error_array, "<span class='error'>Your Last name must be between 2 and 50 characters.</span>");
	}
	else {
		if(preg_match('/[^A-Za-z0-9]/', $password)) {
			array_push($error_array, "<span class='error'>Your password can only contain english letters and numbers.</span>");
		}
	}
	if(strlen($password) > 30 || strlen($password) < 5) {
		array_push($error_array, "<span class='error'>Your password must be between 5 and 30 characters.</span>");
	}
	if($password !== $password2){
		array_push($error_array, "<span class='error'>Passwords do not match.</span>");
	}
	if(empty($error_array)){
		$password = md5($password); //ecrypt password

		//Genarate username by concatentaing first name and last name
		// $username = strtolower($fname . "_" . $lname);
		// $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		//if username exists add number to username
		// $i = 0;
		// while(mysqli_num_rows($check_username_query) != 0) {
		// 	$i ++; 
		// 	$username = $username . "_" . $i;
		// 	$check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
		// }

		//Profile picture assignment
		$rand = rand(1, 2); //random number between 1 and 2
		
		if($rand == 1)
			$profile_pic = "assets/images/profile_pics/defaults/pic_1.png";
		else if($rand == 2)
			$profile_pic = "assets/images/profile_pics/defaults/pic_2.png";
		
		//Insert values into the database
		$query = mysqli_query($con, "INSERT INTO users VALUES (NULL, '$fname', '$lname', '$username', '$em', '$password', '$date', '$profile_pic', '0', '0', 'no', 'no', 'no', ',')");
		array_push($error_array, "<span class='success'>You are all set! Go ahead and login!</span>");

		//Clear session variables
		$_SESSION['req_fname'] = "";
		$_SESSION['req_lname'] = "";
		$_SESSION['req_username'] = "";
		$_SESSION['req_email'] = "";
		$_SESSION['req_email2'] = "";

		header("Location: register.php?reg=reg_successful");

	}

}
?>