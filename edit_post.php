<?php
	// require 'config/config.php'; 
	include("includes/header.php");


	if(isset($_GET['id'])){
		$id = $_GET['id'];
	} else {
		$id = 0;
	}


	$post_query = mysqli_query($con, "SELECT body FROM posts WHERE id='$id'");
	$row = mysqli_fetch_array($post_query);
	$post_body = $row['body'];
	$error = "";
	$message = "";

	if(isset($_POST['save_post'])){ 

		$new_post_body = strip_tags($_POST['new_post']);

		if($new_post_body == "")
			$error = "<p class='message error'>Post cannot be empty.</p>";
		else{
			$query = mysqli_query($con, "UPDATE posts SET body='$new_post_body' WHERE id='$id'");
			$post_body = $new_post_body;
			$message = "<p class='message success'>Post have been successfully updated!</p>";
		}
	}
	if(isset($_POST['delete_post'])){ 

		$delete_post = mysqli_query($con, "DELETE FROM posts WHERE id='$id' AND added_by='$userLoggedIn'");
		header("Location: index.php");

	}

	if(isset($_POST['cancel'])){
	header("Location: index.php");
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
				
				echo "Posts: " . $user['num_posts'] . "<br>";
				echo "Likes: " . $user['num_likes'];
				?>
			</p>
		</div>		
	</div>

	<div class="main_column column" id="main_column">
		<button type="submit" class="close"><a href="index.php"><span aria-hidden="true">&times;</span></a></button>
		<h4>Edit post:</h4>
	        
		<div class="post_area"><?php echo $error; ?>
		<?php echo $message; ?>
		<form action="edit_post.php?id=<?php echo $id; ?>" method="POST">
			<textarea name="new_post" cols="30" rows="10" placeholder="Write Something"><?php echo $post_body; ?></textarea>
			<input type="submit" name="save_post" class="warning" id="save_post" value="Save">
			<input type="submit" name="delete_post" class="danger" value="Delete Post">
			<input type="submit" name="cancel" id="default" value="Cancel">
		</form>
		</div>
	</div>


</div>
<script>
		$("textarea").emojioneArea({
		pickerPosition: "bottom"
	});
		$('.danger').on('click', function(){
							//bootstrap
							bootbox.confirm({
								message: "Are you sure you want to delete this post?", buttons: {
	        					confirm: {
						            label: 'Yes'
						        },

						        cancel: {
						            label: 'No'						        }
						    	},
						    	
						        callback:
						    	function
								(result){
									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result: result});
									//if there is a result = true
									if(result)
										location.reload();
								}
							});
</script>
