<?php
	include('includes/header.php');

	if(isset($_GET['profile_username'])){
		$username = $_GET['profile_username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
		$user_array = mysqli_fetch_array($user_details_query);
		//find friends, look for commans
		$num_friends = (substr_count($user_array['friend_array'], ',')) - 1;
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
		<a href="<?php echo $username; ?>"><img id="profile_pic" src="<?php echo $user_array['profile_pic']; ?>"></a>
		<div class="profile_info">
			<p><a href="<?php echo $username; ?>" id="name">
				<?php 
				echo $user_array['first_name'] . " " . $user_array['last_name'];
				?>
			</a></p>
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
			<p><?php echo "Posts: " . $user_array['num_posts']; ?></p>
			<p><?php echo "Likes: " . $user_array['num_likes']; ?></p>
			<p><?php echo "Friends: " . $num_friends; ?></p>
			<?php 
				if($userLoggedIn != $username){
					echo '<p class="profile_info_bottom">Mutural Friends: ';
					echo $logged_in_user_obj->getMuturalFriends($username);
					echo '</p>';
				}
			?>
			<input type="submit" id="post_button" data-toggle="modal" data-target="#post_form" value="Post Something">
		</div>
	</div>

	<div class="main_column column profile">
		<h2 class="profile-name"><?php 
				echo $user_array['first_name'] . " " . $user_array['last_name'];
				?>:</h2>

		<ul class="nav nav-tabs" id="profileTabs">
		  <li>
		    <a href="#newsfeed_div"  role="tab" data-toggle="tab">Newsfeed</a>
		  </li>
		  <li><a href="#about_div" aria-control="about_div" role="tab" data-toggle="tab">About</a></li>
		</ul>
		<div class="tab-content">
			<div role="tabpanel" class="tab-pane active" id="newsfeed_div">
				<div class="posts_area"></div>
				<img id="loading" src="assets/images/icons/loading.gif">
			</div>
			<div role="tabpanel" class="tab-pane fade in" id="about_div">

			</div>
		</div>

		
	</div>

	<!-- Modal -->
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
	function sendComment(id) {
 	const userLoggedIn = '<?php echo $userLoggedIn; ?>';
	const commentText = $("#comment" + id).val();
	
	if(commentText === "") {
 
		alert("Please enter some text first");
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
 
			alert("Something went wrong. Please try again");
		} 
 
	});
};
	</script>
	<!-- end wrapper from header -->
	</div>
	</body>
</html>