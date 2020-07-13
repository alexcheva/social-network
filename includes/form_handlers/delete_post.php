<?php 
require '../../config/config.php';
// $username = $_POST['userLoggedIn'];

if(isset($_GET['post_id']))
	$post_id = $_GET['post_id'];

if(isset($_POST['result'])) {
	if($_POST['result'] == 'true')

		$query = mysqli_query($con, "DELETE FROM posts WHERE id='$post_id'");

		// $get_number_posts = mysqli_query($con, "SELECT * FROM posts WHERE added_by='$username'");
		// $num_posts = mysqli_num_rows($get_number_posts);
		// $query = mysqli_query($con, "UPDATE users SET num_posts='$num_posts' WHERE username='$username'");
		
}

 ?>