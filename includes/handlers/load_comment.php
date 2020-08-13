<?php
 
	require_once("../../config/config.php");
	require_once("../classes/User.php");
	require_once("../classes/Post.php");
	 
	$id = $_POST['id'];
	 
	$userLoggedIn = $_POST['userLoggedIn'];
	 
	$post = new Post($con, $userLoggedIn);
	echo $post->getComments($id, true);
?>