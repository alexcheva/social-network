<?php 
	include("includes/header.php");
	require_once("includes/classes/Emojis.php");

	if(isset($_GET['id'])){
		$id = $_GET['id'];
	} else {
		$id = 0;
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
			<p>
				<?php
				echo "Posts: " . $user['num_posts']; . "<br>";
				echo "Likes: " . $user['num_likes'];
				?>
			</p>
		</div>		
	</div>

	<div class="main_column column" id="main_column">
		<div class="post_area">
			<?php 
			$post = new Post($con, $userLoggedIn);
			$post->getSinglePost($id);
			 ?>
		</div>
	</div>


</div>
