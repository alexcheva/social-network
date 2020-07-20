<?php 
require '../../config/config.php';

if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {	

	if($_POST['result'] == 'true'){
		$query_image = mysqli_query($con, "SELECT * FROM posts WHERE id='$post_id'");

		$row = mysqli_fetch_array($query_image);
		$post_image_src = $row['image'];
		$post_author = $row['added_by'];

		//if image, unlink:
		$image_src = "../../" .$post_image_src;
		unlink($image_src);

		//update number of posts:
		$get_number_posts = mysqli_query($con, "SELECT * FROM users WHERE username='$post_author'");
		$row = mysqli_fetch_array($get_number_posts);
		$total_user_posts = $row['num_posts'];
		$total_user_posts--;
		$user_posts = mysqli_query($con, "UPDATE users SET num_posts='$total_user_posts' WHERE username='$post_author'");

		$delete_post = mysqli_query($con, "DELETE FROM posts WHERE id='$post_id'");
		$delete_post_comments =  mysqli_query($con, "DELETE FROM comments WHERE post_id='$post_id'");

	}
}

 ?>