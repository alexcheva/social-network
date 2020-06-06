<?php  
	require 'config/config.php';

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}

	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}

	$get_likes = mysqli_query($con, "SELECT * FROM likes WHERE post_id='$post_id'");
	$total_likes = mysqli_num_rows($get_likes);
    echo json_encode($total_likes);
?>
