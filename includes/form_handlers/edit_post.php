<?php 
require '../../config/config.php';

if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')
		$body =
		//set post in posts table deleted field to yes
		$query = mysqli_query($con, "UPDATE posts SET body='$body' WHERE id='$post_id'");
}

 ?>