<?php
	include('includes/header.php');

	//handle posting
	//redirect back to index
	if(isset($_POST['post'])){
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];
		$errorMessage = "";

		if($imageName != ""){
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . basename($imageName);
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 10000000){
				$errorMessage = "Sorry, your file is too large";
				$uploadOk = 0;
			}
			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
			$errorMessage = "Sorry, only jpeg/jpg, png and gif files are allowed";
				$uploadOk = 0;
			}

			if($uploadOk){
				if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)){
					//image uploaded sucessfully

				}else{
					//image did not upload
					$uploadOk = 0;
				}
			}
		}

		if($uploadOk){
			$post = new Post($con, $userLoggedIn);
			$post->submitPost($_POST['post_text'], 'none', $imageName);
			header("Location: index.php");
		}else{
			echo "<div style='text-align:center;' class='error'> 
			$errorMessage</div>";
		}
	}
?>
	<div class="user_details column">
		<a href="<?php echo $userLoggedIn ?>">
			<img id="profile_pic" src="<?php echo $user['profile_pic']; ?>">
		</a>
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
	<div class="main_column column">
		
		<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">

			<textarea name="post_text" id="post_text"  placeholder="Write a post"></textarea>
			
			
	<!-- 		<br>
			<input type="radio" id="global" name="visibility" value="global" checked="checked">
			<i class="fas fa-globe radio"></i>
			<input type="radio" id="friends_only" name="visibility" value="friends_only">
			<i class="fas fa-user-friends radio"></i>

	 -->	
			<a href="javaScript:void(0)" onClick="showFileUpload()">
				<i class="fas fa-file-image" id="toggle_file_upload"></i>
			</a>
			<input type="file" name="fileToUpload" id="fileToUpload" class="hide">
		 	<input type="submit" name="post" id="post_button" value="Post">

		</form>
		<hr>
		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">
	</div>

	<!-- Post on wall Edit post -->
	<?php 
		if(isset($_GET['post_id'])){
		$post_id = $_GET['post_id'];
		$get_post = mysqli_query($con, "SELECT body FROM posts WHERE post_id='$post_id'");
		$old_post = $row['body'];
	?>
	<div class="modal fade" id="edit_post<?php echo $post_id;?>" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">

	      <div class="modal-header">
	        <h4 class="modal-title" id="postModalLabel">Edit post:</h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      </div>

	      <div class="modal-body">
	        <form class="profile_post" action="" method="POST">
	        	<div class="form-group">
	        		<textarea class="form-control" name="new_post" rows="5"><?php echo $old_post; ?></textarea>
	        		<!-- <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>"> -->
	        		<!-- user whos page we are on -->
	        		<!-- <input type="hidden" name="user_to" value="<?php echo $username; ?>"> -->
	        	</div>
	        </form>
	      </div>

	      <div class="modal-footer">
	        <button type="button" id="ignore" class="btn btn-default" data-dismiss="modal">Close</button>
	        <button type="button" id="submit_profile_post" class="btn btn-primary" name="post_button">Save</button>
	      </div>
	    </div>
	  </div>
	</div>
	<?php
		}

		if(isset($_POST['post_button'])){
		
			$new_post = strip_tags($_POST['new_post']);
				
			//set post in posts table deleted field to yes
			$query = mysqli_query($con, "UPDATE posts SET body='$new_post' WHERE id='$post_id'");
		}
	?>
		
	<script>

		$(function(){
		 
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
					url: "includes/handlers/ajax_load_posts.php",
					type: "POST",
					data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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
	</script>
	<script>
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
	 
				bootbox.alert("Something went wrong. Please try again.");
			} 
	 
		});
	};
	</script>
	<script>
		$(document).ready(function(){
			$(".comment").emojioneArea({
				pickerPosition: "bottom"
			});
		});
	</script>

	<!-- end wrapper from header -->
	</div>
	</body>
</html>