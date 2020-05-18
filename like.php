<?php
	require 'config/config.php';
	include('includes/classes/User.php');
	include('includes/classes/Post.php');

	if (isset($_SESSION['username'])){
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}
	//Get id of post
	if(isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
	}
	//get information from database
	$get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
	//fetch number of likes
	$row = mysqli_fetch_array($get_likes);
	//store numbers in variables
	$total_likes = $row['likes'];
	$user_liked = $row['added_by'];
	//find all the information about the user that posted it
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
	//GET the information from the database
	$row = mysqli_fetch_array($user_details_query);
	$total_user_likes = $row['num_likes'];

	//Like button
	if(isset($_POST['like_button'])){
		$total_likes++;
		//update the number of likes
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		//update total user likes number
		$total_user_likes++;
		//undate total likes for user
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		//add user to likes
		$insert_user = mysqli_query($con, "INSERT INTO likes VALUES(NULL, '$userLoggedIn', '$post_id')");
		
		//Insert notification
	}

	//Unlike button
	//post when submit input name = unlike_button
	if(isset($_POST['unlike_button'])){
		$total_likes--;
		//update the number of likes
		$query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
		//take away one from user likes
		$total_user_likes--;
		//undate total likes for user
		$user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
		//delete this post from likes
		$insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");

	}

	//Check for previous likes
	//find out if logged in user liked the post or not:
	$check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
	$num_rows = mysqli_num_rows($check_query);

	if($num_rows > 0){
		echo '<form class="likes" action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_like" name="unlike_button" value="Unlike">
				<span class="like_value">' . $total_likes . ' Likes
				</span>
			</form>'
		; //unlike
	} else {
		echo '<form class="likes" action="like.php?post_id=' . $post_id . '" method="POST">
				<input type="submit" class="comment_like" name="like_button" value="Like">
				<span class="like_value">' . $total_likes . ' Likes
				</span>
			</form>'; //like
	}

?>
<html>
	<head>
		<title></title>
		<script
		  src="https://code.jquery.com/jquery-2.2.4.min.js"
		  integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44="
		  crossorigin="anonymous"></script>

		  <link rel="stylesheet" href="assets/css/comments.css">
	</head>
	<body>
		
	</body>
	</html>