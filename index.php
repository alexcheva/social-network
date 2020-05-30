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
			<p>
				<?php 
				echo "Posts: " . $user['num_posts'] . "<br>";
				echo "Likes: " . $user['num_likes'];
				?>
			</p>
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
	 -->	<input type="submit" name="post" id="post_button" value="Post">
		</form>
		<hr>
		<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">
	</div>

	<script>
	$(function(){
	 
		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
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
	<script>function sendComment(id) {
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
};</script>
	<!-- end wrapper from header -->
	</div>
	</body>
</html>