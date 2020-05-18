<?php
include('includes/header.php');
include('includes/classes/User.php');
include('includes/classes/Post.php');

//handle posting of posts
//redirect back to index
if(isset($_POST['post'])){
	$post = new Post($con, $userLoggedIn);
	$post->submitPost($_POST['post_text'], 'none');
	header("Location: index.php");
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
			<p>			<?php 
				echo "Posts: " . $user['num_posts'] . "<br>";
				echo "Likes: " . $user['num_likes'];
			?></p>

		</div>
		



	</div>
	<div class="main_column column">
	<form class="post_form" action="index.php" method="POST">
		<textarea name="post_text" id="post_text"  placeholder="Write a post"></textarea>
		<input type="submit" name="post" id="post_button" value="Post">
	</form>
	<hr>

	<div class="posts_area"></div>
		<img id="loading" src="assets/images/icons/loading.gif">
	</div>

	<script>
		var userLoggedIn = '<?php echo $userLoggedIn; ?>'

		$(document).ready(function(){
			$('#loading').show();
			//original ajax request for loading first posts
			$.ajax({
				url: "includes/handlers/ajax_load_posts.php",
				type: "POST",
				data: "page=1&userLoggedIn=" + userLoggedIn,
				cache: false,

				success: function(data) {
					$('#loading').hide();//when posts returned, don't show icon anymore
					$('.posts_area').html(data);//put data into the div
				}
			});

			$(window).scroll(function(){
				var height = $('.posts_area').height(); // height of div containing posts
				var scroll_top = $(this).scrollTop();
				var page = $('.posts_area').find('.nextPage').val();//find the value of nextPage div
				var noMorePosts = $('.posts_area').find('.noMorePosts').val();//find value of noMorePosts div

				if((document.body.scrollHeight == document.body.scrollTop + window.innerHeight) && noMorePosts == 'false') {
					$('#loading').show();//show loading icon

					var ajaxReq = $.ajax({
						url: "includes/handlers/ajax_load_posts.php",
						type: "POST",
						data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
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

	</div>
	</body>
</html>