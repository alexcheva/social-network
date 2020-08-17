<?php
	include('includes/header.php');

	//handle posting
	//redirect back to index
	if(isset($_POST['post'])){
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];
		$errorMessage = "";
		$visibility = $_POST['visibility'];
		if($visibility == 'global')
			$global = 'yes';
		else
			$global = 'no';

		if($imageName != ""){
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . basename($imageName);
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 10000000){
				$errorMessage = "Sorry, your file is too large!";
				$uploadOk = 0;
			}
			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
			$errorMessage = "Sorry, only jpeg/jpg, png and gif files are allowed!";
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
			$post->submitPost($_POST['post_text'], 'none', $global, $imageName);
			echo "<script>showUpdate('Sucessfully posted!');</script>";
			header("Location: index.php");
		}else{
			echo "<script>showUpdate('$errorMessage');</script>";
		}
	}
?>

	<aside class="user_details column">
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
					echo "Posts: " . $user['num_posts'] . "<br>";
					echo "Likes: " . $user['num_likes'] . "<br>";

				if($user['friend_array'] !== ","){
					$friend_array = preg_split("/[\s,]+/", $user['friend_array']);
					foreach($friend_array as $key => $value) {
						$value = "<a href='". $value ."'>". $value ."</a>";
						$friend_array[$key] = $value;
					}
					$friends = implode("<br>", $friend_array);
					echo "Friends: " . $friends;
				}
				?>
			</p>
		</div>
	</aside>

	<section class="main_column column">
	<?php $logged_in_user_obj = new User($con, $userLoggedIn); 
		if($logged_in_user_obj->isBlocked($userLoggedIn))

		echo '<p class="error">You have been BANNED by Admin. You are no longer allowed to post or comment.</p>';
	else{
		 ?>
		
		<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
			<textarea name="post_text" id="post_text"  placeholder="Write a post"></textarea>
			<div class="visibility"><input type="radio" id="global" name="visibility" value="global" checked="checked">
			<i class="fas fa-globe radio"></i>
			<input type="radio" id="friends_only" name="visibility" value="friends_only">
			<i class="fas fa-user-friends radio"></i>
			</div>
			<a href="javaScript:void(0)" onClick="showFileUpload()">
				<i class="fas fa-file-image" id="toggle_file_upload"></i>
			</a>
			<input type="file" name="fileToUpload" id="fileToUpload" class="hide">
		 	<input type="submit" name="post" id="post_button" value="Post">

		</form>
	<?php } ?>
		<hr>
		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">
	</section>

	<script>

		$(function(){
		 
			var inProgress = false;

			loadPosts(); //Load first posts
		    $(window).scroll(function() {
		    	var bottomElement = $(".bottom").last();
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
		            rect.top >= Math.min((window.innerHeight || document.documentElement.clientHeight) - rect.height, 0) &&
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

					$(".emojionearea-editor").text("");
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
	<hr class="bottom"/>
	<?php include('footer.php'); ?>
	<!-- end wrapper from header -->
