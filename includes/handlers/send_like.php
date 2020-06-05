<?php
	 
	require_once("../../config/config.php");

	$userLoggedIn = $_POST['userLoggedIn'];
	$id = $_POST['id'];

	//find information about the post:
	$get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$id'");
	$row = mysqli_fetch_array($get_likes);
	//declare total likes for the post:
	$total_likes = $row['likes'];
	//declare the author of the post:
	$user_liked = $row['added_by'];

	//find author in users:
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
	$row = mysqli_fetch_array($user_details_query);
	$total_user_likes = $row['num_likes'];

	//query likes:
	$is_liked = mysqli_query($con, "SELECT * from likes WHERE username='$userLoggedIn' AND post_id='$id'");
	$num_rows = mysqli_num_rows($is_liked);

	if ($num_rows > 0) {
		//remove like in posts:
		$total_likes--;
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$id'");
		//remove like in users:
		$total_user_likes--;
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		//delete like in likes:
	    $delete_like = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");

	} else {
		//add 1 like to posts:
		$total_likes++;
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$id'");
		//add 1 like to users:
		$total_user_likes++;
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		//add like to likes:
	    $insert_like = mysqli_query($con, "INSERT INTO likes VALUES(NULL, '$userLoggedIn', '$id')");
	}

?>