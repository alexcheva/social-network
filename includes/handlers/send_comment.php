<?php
 
	require_once("../../config/config.php");
	require_once("../classes/User.php");
	require_once("../classes/Post.php");
	require_once("../classes/Notification.php");
	require_once("../classes/Emojis.php");
	 
	$userLoggedIn = $_POST['userLoggedIn'];
	$commentText = $_POST['commentText'];
	$id = $_POST['id'];
	 
	$get_added_by = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$id'");
	$find_users = mysqli_fetch_array($get_added_by);
	$post_author = $find_users['added_by'];
	$user_to = $find_users['user_to'];
	 
	$post = new Post($con, $userLoggedIn);
	$post->sendComment($post_author, $commentText, $id, $user_to);

?>