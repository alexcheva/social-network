<?php
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Post.php");

$limit = 10; //number of posts loaded per call

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->loadProfilePosts($_REQUEST, $limit);

//select textarea
if(isset($_POST['comment_body'])){
	//create post valiable
	$post = new Post($con, $_POST['user_from']);
	//pass varibale into submitPost function
	$post->submitPost($_POST['comment_body'], $_POST['user_to']);

}

?>