<?php
include('includes/header.php');

//handle posting of posts
//redirect back to index
if(isset($_POST['post'])){
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
	// header("Location: index.php");
}

?>
	<div class="user_details column">
		<a href="<?php echo $userLoggedIn ?>"><img id="profile_pic" src="<?php echo $user['profile_pic']; ?>"></a>
		<div class="user_details_left_right">
			<a href="<?php echo $userLoggedIn ?>" id="name">
				<?php 
				echo $user['first_name'] . " " . $user['last_name'];
				?>
			</a>
			<p>			<?php 
				echo "Posts: " . $user['num_posts'] . "<br>";
				echo "Likes: " . $user['num_likes'];

			?></p>
			

		</div>
		



	</div>
	<div class="main_column column">
	<form class="post_form" action="index.php" method="POST">
		<textarea name="post_text" id="post_text"  placeholder="Write a post"></textarea>
<!-- 		<br>
		<input type="radio" id="global" name="visibility" value="global" checked="checked">
		<i class="fas fa-globe radio"></i>
		<input type="radio" id="friends_only" name="visibility" value="friends_only">
		<i class="fas fa-user-friends radio"></i>
 -->		<input type="submit" name="post" id="post_button" value="Post">
	</form>
	<hr>

	<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">
	</div>

	<script>
	var userLoggedIn = '<?php echo $userLoggedIn; ?>';
	</script>
    <script type="text/javascript" src="assets/js/post_loader.js"></script>

	</div>
	</body>
</html>