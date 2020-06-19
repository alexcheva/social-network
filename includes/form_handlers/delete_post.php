<?php 
require '../../config/config.php';
include('includes/classes/User.php');
$user_obj = new User($con, $userLoggedIn);
$num_posts = $user_obj->getNumPosts();

if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')
		//set post in posts table deleted field to yes
		// $query = mysqli_query($con, "UPDATE posts SET deleted='yes' WHERE id='$post_id'");
		
		//actually delete post from the table
		$query = mysqli_query($con, "DELETE FROM posts WHERE id='$post_id'");
		//remove from number of user posts:
		
			$new_posts = $num_posts - 1;
			$update_query = mysqli_query($con, "UPDATE users SET num_posts='$new_posts' WHERE username='$userLoggedin'");
}

 ?>