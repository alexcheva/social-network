<?php
include('includes/header.php');

if(isset($_GET['profile_username'])){
	$username = $_GET['profile_username'];
	$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
	$user_array = mysqli_fetch_array($user_details_query);
}
?>
<div class="user_details column profile_left">
		<a href="<?php echo $userLoggedIn ?>"><img id="profile_pic" src="<?php echo $user_array['profile_pic']; ?>"></a>
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
	This is the profile page of <?php echo $username; ?>
	<hr>
	</div>



	</div>
	</body>
</html>