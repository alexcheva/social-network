<?php
	include("includes/header.php");

	if(isset($_GET['id'])){
		$id = $_GET['id'];
	} else {
		$id = 0;
	}

	$post_query = mysqli_query($con, "SELECT * FROM posts WHERE id='$id'");
	$row = mysqli_fetch_array($post_query);
	$post_body = $row['body'];
	$post_image_src = $row['image'];
	$post_author = $row['added_by'];
	$global = $row['global'];

	// if($global=='yes'){
	// 	$global_check = 'checked="checked"';
	// 	$friends_only = '';
	// }else{
	// 	$friends_only = 'checked="checked"';
	// 	$global_check = '';
	// }

	if($post_image_src != "")
		$post_image = "<div><a href='$post_image_src' target='_blank'><img class='postedImages' src='$post_image_src'></a></div>";
	else
		$post_image = "";
	


	$post_body = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $post_body);
	//youtube div
	$youtube_div_open = "<div class='embed-container youtube' data-embed='";
	//image div
	$image_div_open = "<div class='embed-images' data-embed='";
	//link div
	$link_div_open = "<div class='embed-link' data-embed='";
	$div_close = "'></div>";

	$post_body = str_replace($youtube_div_open, "https://www.youtube.com/watch?v=", $post_body);
	$post_body = str_replace($image_div_open, "", $post_body);
	$post_body = str_replace($link_div_open, "", $post_body);
	$post_body = str_replace($div_close, "", $post_body);

	$error = "";
	$message = "";
	$errorImage = "";

	if(isset($_POST['save_post'])){

		$new_post_body = strip_tags($_POST['new_post']);

		if($new_post_body == "")
			$error = "<p class='error'>Post cannot be empty or have HTML tags.</p>";
		else{
			$new_post_body = str_replace(array("\r\n", "\r", "\n"), " <br/> ", $new_post_body);
			$check_empty = preg_replace('/\s+/', '', $new_post_body); //deletes all spaces
		
		if($check_empty != "") {

			$body_array = preg_split("/\s+/", $new_post_body);
 
			foreach($body_array as $key => $value) {

				$regex_images = '~https?://\S+?(?:png|gif|jpe?g)~';
				$regex_links = '~(?<!src=\')https?://\S+\b~x';

				if(strpos($value, "www.youtube.com/watch?v=") !== false){

					$link = preg_split("!&!", $value);

					$value = str_replace("https://www.youtube.com/watch?v=", "", $link[0]);

					$value = "<div class='embed-container youtube' data-embed='". $value ."'></div>";
					
					//$key refers to position of the link
					$body_array[$key] = $value;
				}
				if(strpos($value, "https://youtu.be/") !== false){

					$link = preg_split("!\?!", $value);
					
					$value = str_replace("https://youtu.be/", "", $link[0]);

					$value = "<div class='embed-container youtube' data-embed='". $value ."'></div>";
				
					$body_array[$key] = $value;
				}
				if(preg_match($regex_images, $value)) {
				 	$link = preg_split("!\?!", $value);
				 	$value = preg_replace($regex_images, "<div class='embed-images' data-embed='\\0'></div>", $link[0]);
					$body_array[$key] = $value;
				}
				else if(preg_match($regex_links, $value)) {
				 	$value = preg_replace($regex_links, "<div class='embed-link' data-embed='\\0'></div>", $value);
				 	//<i class='fa fa-external-link-square'></i>
					$body_array[$key] = $value;
				}		
			}

			$new_post_body = implode(" ", $body_array);
			$new_post_body = mysqli_real_escape_string($con, $new_post_body);
			$visibility = $_POST['visibility'];
			if($visibility == 'global')
				$global = 'yes';
			else
				$global = 'no';

			$query = mysqli_query($con, "UPDATE posts SET body='$new_post_body' AND global='$global' WHERE id='$id'");

			//remake newpost body into post body:

			$youtube_div_open = "<div class=\'embed-container youtube\' data-embed=\'";
			$image_div_open = "<div class=\'embed-images\' data-embed=\'";
			//link div
			$link_div_open = "<div class=\'embed-link\' data-embed=\'";
			$div_close = "\'></div>";

			$post_body = $new_post_body;
			$post_body = str_replace($youtube_div_open, "https://www.youtube.com/watch?v=", $post_body);
			$post_body = str_replace($image_div_open, "", $post_body);
			$post_body = str_replace($link_div_open, "", $post_body);
			$post_body = str_replace($div_close, "", $post_body);
			$post_body = preg_replace('/\<br(\s*)?\/?\>/i', "\n", $post_body);

			$message = "<p class='success'>Post have been successfully updated! <a href='post.php?id=".$id."'>View post</a></p>";
		}

		if(!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE){
			$post_image = "";
		}else{
			$uploadOk = 1;
			$imageName = $_FILES['fileToUpload']['name'];

			if($imageName != ""){
				$targetDir = "assets/images/posts/";
				$imageName = $targetDir . uniqid() . basename($imageName);
				$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

				if($_FILES['fileToUpload']['size'] > 10000000){
					$error = "<p class='error'>Sorry, your file is too large.</p>";
					$uploadOk = 0;
				}
				if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
				$error = "<p class='error'>Sorry, only jpeg/jpg, png and gif files are allowed!</p>";
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
				$update_image = mysqli_query($con, "UPDATE posts SET image='$imageName' WHERE id='$id'");
				$post_image = "<div><a href='$post_image_src' target='_blank'><img class='postedImages' src='$imageName'></a></div>";



			}else{
				$error = "<p class='error'>Something went wrong. Please, try again.</p>";
			}
		}
	}
}
	if(isset($_POST['delete_post'])){ 
		if ($post_image_src != "")
			unlink($post_image_src);
		
		//update number of posts:
		$get_number_posts = mysqli_query($con, "SELECT * FROM users WHERE username='$post_author'");
		$row = mysqli_fetch_array($get_number_posts);
		$total_user_posts = $row['num_posts'];
		$total_user_posts--;
		$user_posts = mysqli_query($con, "UPDATE users SET num_posts='$total_user_posts' WHERE username='$post_author'");

		$delete_post = mysqli_query($con, "DELETE FROM posts WHERE id='$id' AND added_by='$userLoggedIn'");
		header("Location: index.php");

	}

	if(isset($_POST['cancel'])){
		header("Location: index.php");
	}

	if(isset($_POST['update_image'])){
		if(!isset($_FILES['fileToUpload']) || $_FILES['fileToUpload']['error'] == UPLOAD_ERR_NO_FILE){
			$errorImage = "<p class='error'>No file chosen to update the image.</p>";
		} else{
			$uploadOk = 1;
			$imageName = $_FILES['fileToUpload']['name'];

			if($imageName != ""){
				$targetDir = "assets/images/posts/";
				$imageName = $targetDir . uniqid() . basename($imageName);
				$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

				if($_FILES['fileToUpload']['size'] > 10000000){
					$error = "<p class='error'>Sorry, your file is too large.</p>";
					$uploadOk = 0;
				}
				if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
				$error = "<p class='error'>Sorry, only jpeg/jpg, png and gif files are allowed!</p>";
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
				$update_image = mysqli_query($con, "UPDATE posts SET image='$imageName' WHERE id='$id'");
				$message = "<p class='success'>Image have been successfully updated! <a href='post.php?id=".$id."'>View post</a></p>";
				$post_image = "<div><a href='$post_image_src' target='_blank'><img class='postedImages' src='$imageName'></a></div>";

			}else{
				$error = "<p class='error'>Something went wrong. Please, try again.</p>";
			}
		}
	}
	if(isset($_POST['delete_image'])){ 

		$delete_image = mysqli_query($con, "UPDATE posts SET image='' WHERE id='$id'");
		$message = "<p class='success'>Image have been successfully removed! <a href='post.php?id=".$id."'>View post</a></p>";
		unlink($post_image_src);
		$post_image = "";

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
		
	        
		<div class="post_area">
			<h4>Edit post:</h4>
			<?php echo $error; ?>
			<?php echo $message; ?>
			<form action="edit_post.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
				<textarea name="new_post" cols="30" rows="10" placeholder="Write Something"><?php echo $post_body; ?></textarea>
				<div class="visibility">
				<input type="radio" name="visibility" value="global" <?php echo $global_check; ?>>
				<i class="fas fa-globe radio"></i>
				<input type="radio" name="visibility" value="friends_only" <?php echo $friends_only; ?>>
				<i class="fas fa-user-friends radio"></i>
				</div>
				<?php if ($post_image == ""){ ?>
					<input type="file" name="fileToUpload" id="fileToUpload">
				<?php } ?>
				<input type="submit" name="save_post" class="warning inline" id="save_post" value="Save">
				<input type="submit" name="delete_post" class="danger inline" value="Delete Post">
				<input type="submit" name="cancel" class="default inline" value="Cancel">
			</form>
			<?php if ($post_image != ""){ ?>
				<h4>Edit attachment:</h4>
				<?php echo $errorImage; ?>
				<?php echo $post_image; ?>
				<form action="edit_post.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
					<input type="file" name="fileToUpload" id="fileToUpload">
				 	<input type="submit" name="update_image" class="warning inline" value="Change Image">
				 	<input type="submit" name="delete_image" class="danger inline" value="Delete Image">
				</form>
			<?php } ?>
		</div>
	</div>


</div>
<script>
$(document).ready(function(){
	$("textarea").emojioneArea({
		pickerPosition: "bottom"
	});
});
</script>
<?php include('footer.php'); ?>
