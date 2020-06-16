<?php 

if(isset($_POST['save_about'])){
	$about = strip_tags($_POST['about']);
	$about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$userLoggedIn'");
	$row = mysqli_fetch_array($about_query);
	if($row > 0)
		$query = mysqli_query($con, "UPDATE details SET about='$about' WHERE username='$userLoggedIn'");
	else{
		if($about = ""){
			$message = "<p class='error'>The field cannot be empty.</p>";
		}else{
			$query = mysqli_query($con, "INSERT INTO details VALUES(NULL, '$userLoggedIn', '$about', '$interests', '$bands', 'yes')");
			$message = "<p class='success'>About was successfully updated!</p>";
		}
	}

} else
$message = "";
// ********************************

// ********************************

if(isset($_POST['close_account'])){
	$close_query = mysqli_query($con, "UPDATE users SET user_closed='yes' WHERE username='$userLoggedIn'");
		//log them out
		session_destroy();
		header("Location: register.php");
$query = mysqli_query($con, "UPDATE details SET about='$about', interests='$interests', bands='$bands' WHERE username='$userLoggedIn'");
}
?>
