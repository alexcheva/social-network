<?php 
require '../../config/config.php';
include('includes/classes/User.php');


if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')
		//set post in posts table deleted field to yes
		// $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
		
		//actually delete post from the table
		$query = mysqli_query($con, "DELETE FROM posts WHERE id='$post_id'");
		
}

 ?>