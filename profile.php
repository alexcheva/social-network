<?php
	include('includes/header.php');

	if(isset($_GET['profile_username'])){
		$username = $_GET['profile_username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
		$user_array = mysqli_fetch_array($user_details_query);
		//find friends, look for commans
		$num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
		$about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$username'");
		$get_number_posts = mysqli_query($con, "SELECT * FROM posts WHERE added_by='$username'");
	}else{
		$username = $userLoggedIn;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user_array = mysqli_fetch_array($user_details_query);
		//find friends, look for commans
		$num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
		$about_query = mysqli_query($con, "SELECT about, interests, bands FROM details WHERE username='$userLoggedIn'");
		$get_number_posts = mysqli_query($con, "SELECT * FROM posts WHERE added_by='$userLoggedIn'");
	}
	$message = "";
	$error = "";
	$row = mysqli_fetch_array($about_query);
	$num_posts = mysqli_num_rows($get_number_posts);
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
			$error = "<p class='message error'>Nothing to update. The fields are empty.</p>";
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
	if(isset($_POST['delete_about'])){
	$delete_about = mysqli_query($con, "DELETE FROM details WHERE username='$userLoggedIn'");
	header("Location: profile.php");
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

?>
	<div class="user_details column profile_left">
		<p id="profile_name"><a href="<?php echo $username; ?>">
				<?php 
				echo $user_array['first_name'] . " " . $user_array['last_name'];
				?>
			</a></p>
		<div class="profile_info">
			<a href="<?php echo $username; ?>"><img id="profile_pic" src="<?php echo $user_array['profile_pic']; ?>"></a>
			<form action="<?php echo $username; ?>" method="POST">
				<?php 
					$profile_user_obj = new User($con, $username); 
					if($profile_user_obj->isClosed()){
						header("Location: user_closed.php");
					}
					$logged_in_user_obj = new User($con, $userLoggedIn); 
					//if they are friends
					if($userLoggedIn != $username) {
						if($logged_in_user_obj ->isFriend($username)){
							echo '<input id="danger" type="submit" name="remove_friend" value="Remove Friend">';
						}
						else if($logged_in_user_obj ->didReceiveRequest($username)){
							echo '<input id="warning" type="submit" name="respond_request" value="Respond to Request">';
						}
						else if($logged_in_user_obj ->didSendRequest($username)){
							echo '<input id="default" type="submit" name="" value="Request Sent">';
						}
						else {
							echo '<input id="success" type="submit" name="add_friend" value="Add Friend">';
						}
					}
				?>
			</form>
			<p><?php echo "Posts: " . $num_posts; ?></p>
			<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
			<p><?php echo "Friends: " . $num_friends; ?></p>
			<?php 
				if($userLoggedIn != $username){
					echo '<p class="profile_info_bottom">Mutual Friends: ';
					echo $logged_in_user_obj->getMutualFriends($username);
					echo '</p>';
				}
				if($logged_in_user_obj ->isFriend($username))
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
					<label>About me:</label>
					
					<textarea name="about" id="" cols="30" rows="10" placeholder="Write Something"><?php echo $about; ?></textarea>
					
					
					<label>My Interests:</label>
					<textarea name="interests" id="" cols="30" rows="10" value="Write Something" placeholder="Write Something"><?php echo $interests; ?></textarea>
					
					<label>My Favourite Bands:</label>
					<textarea name="bands" id="" cols="30" rows="10" placeholder="Write Something"><?php echo $bands; ?></textarea>
					<input type="submit" name="save_about" class="warning" id="save_about" value="Save">
					<input type="submit" name="delete" class="danger close_edit" href="#about_div" aria-control="about_div" role="tab" data-toggle="tab" id="close_edit" value="Cancel">
				</form>
			</div>
			<?php
			}
			?>
			
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
	        <button type="button" id="submit_profile_post" class="btn btn-primary" name="post_button">Post</button>
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
	        <input type="button" id="dont_close" data-dismiss="modal" value="No! Leave it.">
	        </form>
	      </div>
	    </div>
	  </div>
	</div>

	<script>
	$(function(){
	 
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
		var profileUsername = '<?php echo $username; ?>'
		var inProgress = false;
	 
		loadPosts(); //Load first posts
	 
	    $(window).scroll(function() {
	    	var bottomElement = $(".status_post").last();
	    	var noMorePosts = $('.posts_area').find('.noMorePosts').val();
	 
	        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
	        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
	            loadPosts();
	        }
	    });
	 
	    function loadPosts() {
	        if(inProgress) { //If it is already in the process of loading some posts, just return
				return;
			}
			
			inProgress = true;
			$('#loading').show();
	 
			var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
	 
			$.ajax({
				url: "includes/handlers/ajax_load_profile_posts.php",
				type: "POST",
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
				cache: false,
	 
				success: function(response) {
					$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
					$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
					$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 
	 
					$('#loading').hide();
					$(".posts_area").append(response);
	 
					inProgress = false;
				}
			});
	    }
	 
	    //Check if the element is in view
	    function isElementInView (el) {
	        var rect = el.getBoundingClientRect();
	 
	        return (
	            rect.top >= 0 &&
	            rect.left >= 0 &&
	            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
	            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
	        );
	    }
	});
	//tabs functionality
	$("#about").on('click', function(){
		$("#newsfeed").removeClass("active_tab");
		$("#about").addClass("active_tab");
		$("#newsfeed_div").removeClass("active").addClass("fade");
		$("#edit_about").removeClass("active").addClass("fade");
		$("#about_div").removeClass("fade").addClass("active");
		
	});
	$('#newsfeed').on('click', function(){
		$("#about").removeClass("active_tab");
		$("#newsfeed").addClass("active_tab");
		$("#edit_about").removeClass("active").addClass("fade");
		$("#about_div").removeClass("active").addClass("fade");
		$("#newsfeed_div").removeClass("fade").addClass("active");
		$(".message").html("");
	});

	$('.edit_button').on('click', function(){
		$("#about_div").removeClass("active").addClass("fade");
		$("#edit_about").removeClass("fade").addClass("active");
	});
	$('.close_edit').on('click', function(){
		$("#edit_about").removeClass("active").addClass("fade");
		$("#about_div").removeClass("fade").addClass("active");
		$(".message").html("");
	});

	if($(".message").length){
		$("#newsfeed").removeClass("active_tab");
		$("#about").addClass("active_tab");
		$("#newsfeed_div").removeClass("active").addClass("fade");
		$("#about_div").removeClass("fade").addClass("active");
	};

	if($(".error").length){
		$("#about_div").removeClass("active").addClass("fade");
		$("#edit_about").removeClass("fade").addClass("active");
	}

	//emoji one plugIn
	$("textarea").emojioneArea({
		pickerPosition: "bottom"
	});



	function sendComment(id) {
	 	const userLoggedIn = '<?php echo $userLoggedIn; ?>';
		const commentText = $("#comment" + id).val();
		
		if(commentText === "") {
	 
			bootbox.alert("Please enter some text first!");
			return;
	}
 
	const sendComment = $.post("includes/handlers/send_comment.php", {
			userLoggedIn: userLoggedIn, 
			commentText: commentText, 
			id: id
		}, 
		function(response){
 
			if(response !== "No text") {
	 
				const loadComment = $.post("includes/handlers/load_comment.php", 
					{
						id: id, 
						userLoggedIn: userLoggedIn
					}, 
					function(newComment) {
	 
					$("#comment" + id).val("");
					const noComment = $("#toggleComment" + id).find("#noComment" + id);
					
					if(noComment.length !== 0) {
						noComment.remove();
					}
	 
					$("#toggleComment" + id).append(newComment);
	 
				});
			}
	 
			else {
	 
				bootbox.alert("Something went wrong. Please try again");
			} 
 
		});
	};
	</script>
	<!-- end wrapper from header -->
	</div>
	</body>
</html>