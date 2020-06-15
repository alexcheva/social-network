<?php 
require '../../config/config.php';

if(isset($_GET['comment_id']))
	$comment_id = $_GET['comment_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')
		//set post in posts table deleted field to yes
		// $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
		
		//actually delete post from the table
		$query = mysqli_query($con, "DELETE FROM comments WHERE id='$comment_id'");
}

 ?>