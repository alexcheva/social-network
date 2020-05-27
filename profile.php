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
			<p><?php echo "Friends: " . $num_friends; 
			 ?></p>
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
		<h4>This is the profile page of <?php echo $username; ?>:</h4>
		<div class="posts_area"></div>
			<img id="loading" src="assets/images/icons/loading.gif">
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
		//convert php variables into javaScript variables
		var userLoggedIn = '<?php echo $userLoggedIn; ?>'
		var profileUsername = '<?php echo $username; ?>'

		//when no posts loaded yet

		$(document).ready(function(){
			$('#loading').show();
			//original ajax request for loading first posts
			$.ajax({
				url: "includes/handlers/ajax_load_profile_posts.php",
				type: "POST",
				data: "page=1&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
				cache: false,

				success: function(data) {
					$('#loading').hide();//when posts returned, don't show icon anymore
					$('.posts_area').html(data);//put data into the div
				}
			});

			//when some posts are loaded

			$(window).scroll(function(){
				var height = $('.posts_area').height(); // height of div containing posts
				var scroll_top = $(this).scrollTop();
				var page = $('.posts_area').find('.nextPage').val();//find the value of nextPage div
				var noMorePosts = $('.posts_area').find('.noMorePosts').val();//find value of noMorePosts div

				if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
					$('#loading').show();//show loading icon

					var ajaxReq = $.ajax({
						url: "includes/handlers/ajax_load_profile_posts.php",
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
						cache: false,

						success: function(response) {
							$('.posts_area').find('.nextPage').remove();//removes current nextPage div
							$('.posts_area').find('.noMorePosts').remove();

							$('#loading').hide();
							$('.posts_area').append(response);
						}
					});
				}//end if statement
				return false;
			});//end (window).scroll(function())

		});//end document.ready
	</script>
		<!-- end wrapper from header -->
	</div>
	</body>
</html>