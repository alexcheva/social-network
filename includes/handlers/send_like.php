<?php
 
require_once("../../config/config.php");
require_once("../classes/User.php");
require_once("../classes/Post.php");
 
$userLoggedIn = $_POST['userLoggedIn'];
$id = $_POST['id'];
 
$post = new Post($con, $userLoggedIn);
$post->sendLike($userLoggedIn, $id);
?>