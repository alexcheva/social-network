<?php 
require '../../config/config.php';

if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')
		$get_post = mysqli_query($con, "SELECT body FROM posts WHERE post_id='$post_id'");
		
		//set post in posts table deleted field to yes
		$query = mysqli_query($con, "UPDATE posts SET body='$body' WHERE id='$post_id'");
}

 ?>