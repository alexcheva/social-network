<?php 
require '../../config/config.php';
include('../classes/User.php');
include('../classes/Post.php');

//select textarea
if(isset($_POST['post_body'])){
	//create post valiable
	$post = new Post($con, $_POST['user_from']);
	//pass varibale into submitPost function
	$post->submitPost($_POST['post_body'], $_POST['user_to']);

}

?>
