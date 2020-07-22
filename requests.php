<?php
include('includes/header.php');

?>
	<div class="main_column column profile" id="main_column">
	<h4>Friend Requests:</h4>
	<?php 
		$query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to='$userLoggedIn'");
		//check if there are friend requests
		if(mysqli_num_rows($query) == 0)
			echo "You have no friend requests at this time!";
		else{
			while($row = mysqli_fetch_array($query)){
				$user_from = $row['user_from'];
				$user_from_obj = new User($con, $user_from);
				echo "<b><a href='" . $user_from_obj->getUsername() ."'>" .
				$user_from_obj->getFirstAndLastName() . "</a></b> sent you a friend request!";

				$user_from_friend_array = $user_from_obj->getFriendArray();
				//create a unique name for accept request
				if(isset($_POST['accept_request' . $user_from ])){
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$user_from,') WHERE username='$userLoggedIn'");
					$add_friend_query = mysqli_query($con, "UPDATE users SET friend_array=CONCAT(friend_array, '$userLoggedIn,') WHERE username='$user_from'");

					//delete the request from the friend request table
					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					$notification = new Notification($con, $userLoggedIn);
					$notification->friendNotification($user_from);
					echo "You are now friends!";
					header("Location: requests.php");

				}
				//create a unique name for ignore request
				if(isset($_POST['ignore_request' . $user_from ])){
					$delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to='$userLoggedIn' AND user_from='$user_from'");
					echo "Request Ignored!";
					header("Location: requests.php");
				}
			?> 
				 <form action="requests.php" method="POST" class="friend_requests">
				 	<!-- pass in uniques name for accept request -->
				 	<input type="submit" name="accept_request<?php echo $user_from; ?>" value="Accept Request" class="success">
				 	<!-- pass in uniques name for ignore request -->
				 	<input type="submit" name="ignore_request<?php echo $user_from; ?>" value="Ignore Request" class="danger">
				 </form>
			
			<?php

			}
		}
	 ?>

	</div>
	<div class="user_details column profile_left">
		<a href="<?php echo $userLoggedIn ?>"><img id="profile_pic" src="<?php echo $user['profile_pic']; ?>"></a>
		<div class="profile_info">
			<p><a href="<?php echo $userLoggedIn ?>" id="name">
				<?php 
				echo $user['first_name'] . " " . $user['last_name'];
				?>
			</a></p>

			<p><?php
					echo "Posts: " . $user['num_posts']; ?></p>
			<p><?php echo "Likes: " . $user['num_likes']; ?></p>
			<?php 
			if($user['friend_array'] !== ",")
			echo "Friends: " . str_replace(",", "<br>", $user['friend_array']); ?>
			
		</div>
	</div>


	</div>
	</body>
</html>