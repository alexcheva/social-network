<?php
	include('includes/header.php');

	if(isset($_GET['profile_username'])){
		$username = $_GET['profile_username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
		$user_array = mysqli_fetch_array($user_details_query);
		$opened_query = mysqli_query($con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link='$username'");

		if($user_array == 0){
			echo '<div class="main_column column" id="main_colum">
			<h4>User profile not found.</h4><img class="why" src="assets/images/icons/why.png">
			<p>Username "'.$username.'" does not exist, sorry.</p>
			<p><a href="index.php">Go back to the main page.</a></p>
			</div>';
			die();
		}else{
		//find friends, look for commans
		$num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
		$about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$username'");
		}
	}else{
		$username = $userLoggedIn;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user_array = mysqli_fetch_array($user_details_query);
		//find friends, look for commans
		$num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
		$about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$userLoggedIn'");
	}
	
	$message = "";
	$error = "";
	$row = mysqli_fetch_array($about_query);

	if($row > 0){
		$about = $row['about'];
		$interests = $row['interests'];
		$bands = $row['bands'];
	} else {
		$about = "";
		$interests = "";
		$bands = "";
	}
	
	if(isset($_POST['save_about'])){ 

		$new_about = strip_tags($_POST['about']);
		$new_interests = strip_tags($_POST['interests']);
		$new_bands = strip_tags($_POST['bands']);

		if($new_about == "" && $new_interests == "" && $new_bands == "")
			$error = "<p class='message error errorMessage'>Nothing to update. The fields are empty.</p>";
		else if($row > 0){
			$query = mysqli_query($con, "UPDATE details SET about='$new_about', interests='$new_interests', bands='$new_bands' WHERE username='$userLoggedIn'");
			$message = "<p class='message success'>Profile details have been successfully updated!</p>";
			$about = $new_about;
			$interests = $new_interests;
			$bands = $new_bands;
		}else{
			$query = mysqli_query($con, "INSERT INTO details VALUES(NULL, '$userLoggedIn', '$new_about', '$new_interests', '$new_bands', 'yes')");
			$message = "<p class='message success'>Profile details have been successfully updated!</p>";
		}

	}
	if(isset($_POST['ban_user'])){
		$user = new User($con, $userLoggedIn);
		$user->banUser($username);
	}
	
	//when remove friend button pressed
	if(isset($_POST['remove_friend'])){
		$user = new User($con, $userLoggedIn);
		//pass the parameter into removeFriend function
		$user->removeFriend($username);
	}
	//when add friend button pressed
	if(isset($_POST['add_friend'])){
		$user = new User($con, $userLoggedIn);
		//pass the parameter into sendRequest( function
		$user->sendRequest($username);
	}
	//when respond_request button pressed redirect to requests
	if(isset($_POST['respond_request'])){
		header("Location: requests.php");
	}

	if(isset($_POST['delete_about'])){
	$delete_about = mysqli_query($con, "DELETE FROM details WHERE username='$userLoggedIn'");
	header("Location: profile.php");
	}
	

?>	
	<input type='hidden' class="profile" value="<?php echo $username; ?>" />
	<div class="user_details column profile_left">
		<p id="profile_name"><a href="<?php echo $username; ?>">
				<?php 
				echo $user_array['first_name'] . " " . $user_array['last_name'];
				?>
			</a></p>
		<div class="profile_info clearfix">
			<a href="<?php echo $username; ?>"><img id="profile_pic" src="<?php echo $user_array['profile_pic']; ?>"></a>
			<form action="<?php echo $username; ?>" method="POST">
				<?php 
					$profile_user_obj = new User($con, $username); 
					if($profile_user_obj->isClosed()){
						header("Location: user_closed.php");
					}
					$logged_in_user_obj = new User($con, $userLoggedIn); 
					if($logged_in_user_obj->isAdmin($userLoggedIn)&& $userLoggedIn != $username){
						if($profile_user_obj->isBlocked()){
							echo '<input class="default" type="submit" value="USER BANNED">';
						}else
						echo '<input class="danger" type="submit" name="ban_user" onClick="showAlert(\'The user has been sucessfully blocked!\')" value="BAN USER">';
					}
					//if they are friends
					if($userLoggedIn != $username) {
						if($logged_in_user_obj ->isFriend($username)){
							echo '<input class="danger" type="submit" name="remove_friend" value="Remove Friend">';
						}
						else if($logged_in_user_obj ->didReceiveRequest($username)){
							echo '<input class="warning" type="submit" name="respond_request" value="Respond to Request">';
						}
						else if($logged_in_user_obj ->didSendRequest($username)){
							echo '<input class="default" type="submit" name="" value="Request Sent">';
						}
						else {
							echo '<input type="submit" name="add_friend" value="Add Friend">';
						}
					}
				?>
			</form>
			<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
			<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
			<p><?php echo "Friends: " . $num_friends; ?></p>
			<?php 
				if($userLoggedIn != $username){
					echo '<p class="profile_info_bottom">Mutual Friends: ';
					echo $logged_in_user_obj->getMutualFriends($username);
					echo '</p>';
				}
				if($logged_in_user_obj->isBlocked()){
					echo '<p class="error">You have been BANNED by Admin. You are no longer allowed to post or comment.</p>';
				}
				else if($profile_user_obj->isBlocked()){
					echo '<p class="error">This user has been BANNED by Admin.</p>';
				}

				else if($logged_in_user_obj ->isFriend($username))
					echo '<input type="submit" data-toggle="modal" data-target="#post_form" value="Post Something">';
			?>
			
		</div>
	</div>

	<div class="main_column column profile">
		<h2 class="profile-name">
			<?php echo $user_array['first_name'] . " " . $user_array['last_name']; ?>:
		</h2>

		<ul class="nav nav-tabs" id="profileTabs">
		  <li class="active_tab" id="newsfeed">
		    <a href="#newsfeed_div" role="tab" data-toggle="tab">Newsfeed</a>
		  </li>
		  <li id="about"><a href="#about_div" aria-control="about_div" role="tab" data-toggle="tab">About</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="newsfeed_div">
				<div class="posts_area"></div>
				<img id="loading" src="assets/images/icons/loading.gif">
			</div>
			<input type='hidden' class="bottom"/>
			<!-- *********ABOUT********* -->
			<div role="tabpanel" class="tab-pane fade" id="about_div">
				<?php 
					//show edit and delete buttons:
					if($userLoggedIn == $user_array['username'] && $row > 0 || $message != ""){
						echo "<a class='delete_button' id='delete_details' data-toggle='modal' data-target='#delete_about_modal'><i class='fas fa-trash-alt'></i></a>";
					}
					if($userLoggedIn == $user_array['username']){
						echo "
						<a class='edit_button' href='#edit_about' aria-control='edit_about' role='tab' data-toggle='tab'><i class='fas fa-edit'></i></a>";
					}
					//show heading:
					echo "<h4>About ". $user_array['first_name'] . " " . $user_array['last_name'] .":</h4>";
					
	 	 			if($row > 0){

						echo $message ."<div id='about_info'>";

						if($about != ""){
							echo "<p class='purple'>About me:</p>			
							<p>". nl2br($about) ."</p><hr>";
						}
						
						if($interests != ""){
							echo "<p class='purple'>My Interests:</p>
							<p>" . nl2br($interests) . "</p><hr>";
						}
						
						if($bands != ""){
							echo "<p class='purple'>My Favourite Bands:</p>
							<p>". nl2br($bands) . "</p><hr>";
						}
						echo "</div>";

					}else{
						if($message != ""){
							echo $message ."<div id='about_info'>";
							if($new_about != ""){
								echo "<p class='purple'>About me:</p>			
								<p>". nl2br($new_about) ."</p><hr>";
							}
							
							if($new_interests != ""){
								echo "<p class='purple'>My Interests:</p>
								<p>" . nl2br($new_interests) . "</p><hr>";
							}
							
							if($new_bands != ""){
								echo "<p class='purple'>My Favourite Bands:</p>
								<p>". nl2br($new_bands) . "</p><hr>";
							}
							echo "</div>";

						}else if($userLoggedIn == $user_array['username']) {
							echo "<div id='about_info'>
							<p><i>You have not updated your profile details yet.</i></p>
							<img style='width: 100px; margin-bottom: 20px;' src='assets/images/icons/sad.png'></div>";
						} else {
							echo "
							<p><i>This user has not updated their profile details yet.</i></p>
							<img style='width: 100px; margin-bottom: 20px;' src='assets/images/icons/sad.png'>";
							echo "</div>";//close about div
						}
					}

	 	 			if($userLoggedIn == $user_array['username']){
	 	 				echo "<a href='#edit_about' aria-control='edit_about' id='edit_button' class='edit_button' role='tab' data-toggle='tab'>Edit</a>";
	 	 				echo "</div>"; //close about div
				?>

			
			<div role="tabpanel" class="tab-pane fade" id="edit_about">

				<button type="button" class="close close_edit" href="#about_div" aria-control="about_div" role="tab" data-toggle="tab" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4>Edit your Profile Details:</h4>
				<?php $about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$userLoggedIn'");
					$row = mysqli_fetch_array($about_query);
					if($row > 0){
					$about = $row['about'];
					$interests = $row['interests'];
					$bands = $row['bands'];}
					echo $error;
				 ?>
				<form action="profile.php" method="POST">
					<label class='block'>About me:</label>
					
					<textarea name="about" cols="30" rows="10" placeholder="Write Something"><?php echo $about; ?></textarea>
					
					
					<label class='block'>My Interests:</label>
					<textarea name="interests" cols="30" rows="10" value="Write Something" placeholder="Write Something"><?php echo $interests; ?></textarea>
					
					<label class='block'>My Favourite Bands:</label>
					<textarea name="bands" cols="30" rows="10" placeholder="Write Something"><?php echo $bands; ?></textarea>
					<input type="submit" name="save_about" class="warning" id="save_about" value="Save">
					<input type="submit" name="delete" class="danger close_edit" href="#about_div" aria-control="about_div" role="tab" data-toggle="tab" id="close_edit" value="Cancel">
				</form>
			</div>
			<?php
			}
			?>
			
		</div>
	</div>
</div>
	

	<!-- Post on wall -->
	<div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <h4 class="modal-title" id="postModalLabel">Post Something!</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      </div>

	      <div class="modal-body">
	      	<p>This will appear on the user's profile page and also their newsfeed for your friends to see!</p>
	        <form class="profile_post" action="" method="POST">
	        	<div class="form-group">
	        		<textarea class="form-control" name="post_body" rows="5"></textarea>
	        		<input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
	        		<!-- user whos page we are on -->
	        		<input type="hidden" name="user_to" value="<?php echo $username; ?>">
	        	</div>
	        </form>
	      </div>

	      <div class="modal-footer">
	        <button type="button" id="ignore" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" id="submit_profile_post" class="btn btn-primary" onclick="showAlert('Sucessfully submited!')" name="post_button">Post</button>
	      </div>
	    </div>
	  </div>
	</div>

	<!-- DELETE ABOUT INFO -->

	<div class="modal fade" id="delete_about_modal" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <h4 class="modal-title" id="postModalLabel">Are you sure you want to delete your account details?</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      </div>

	      <div class="modal-body">
	      	<p>This will delete your profile details from the database.</p>
	      	<p>You can add them back at any time by editing your profile details again.</p>
	      </div>

	      <div class="modal-footer">
	        <form action="profile.php" method="POST">
	        <input type="submit" id="close_account" name="delete_about" value="Yes! Delete it.">
	        <input type="submit" id="dont_close" data-dismiss="modal" value="No! Leave it.">
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

<?php include('footer.php'); ?>
