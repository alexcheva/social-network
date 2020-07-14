<?php
	include("includes/header.php");

	if(isset($_GET['id'])){
		$id = $_GET['id'];
	} else {
		$id = 0;
	}

	$post_query = mysqli_query($con, "SELECT body, image FROM posts WHERE id='$id'");
	$row = mysqli_fetch_array($post_query);
	$post_body = $row['body'];
	$post_image_src = $row['image'];

	if($post_image_src != "")
		$post_image = "<div><a href='$post_image_src' target='_black'><img class='postedImages' src='$post_image_src'></a></div>";
	else
		$post_image = "";
	
	$youtube_div = ["<div class='embed-container'><iframe src='","' frameborder='0' allowfullscreen></iframe></div>"];
	$post_body = str_replace($youtube_div, '', $post_body);
	$error = "";
	$message = "";

	if(isset($_POST['save_post'])){ 

		$new_post_body = strip_tags($_POST['new_post']);

		if($new_post_body == "")
			$error = "<p class='message error'>Post cannot be empty.</p>";
		else{
			$body_array = preg_split("/\s+/", $new_post_body);
			
			$check_empty = preg_replace('/\s+/', '', $new_post_body); //deletes all spaces
		
			//https://www.youtube.com/?app=desktop
			if($check_empty != "") {
				foreach($body_array as $key => $value){
					//string position, look for the following string:
					if(strpos($value, "www.youtube.com/watch?v=")|| strpos($value, "www.youtube.com/embed/") !== false){
						//replace a string inside a string:
						$link = preg_split("!&!", $value);
						//find !that!
						$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
						$value = "<div class=\'embed-container\'><iframe src=\'" . $value ."\' frameborder=\'0\' allowfullscreen></iframe></div>";
						//save newly modified $value into post:
						//$key refers to position of the link
						$body_array[$key] = $value;
					}
				}
			}
			$new_post_body = implode(" ", $body_array);
			$new_post_body = nl2br($new_post_body);

			$query = mysqli_query($con, "UPDATE posts SET body='$new_post_body' WHERE id='$id'");

			$new_post_youtube_div = ["<div class=\'embed-container\'><iframe src=\'","\' frameborder=\'0\' allowfullscreen></iframe></div>"];
			$post_body = str_replace($new_post_youtube_div, '', $new_post_body);
			$message = "<p class='message success'>Post have been successfully updated!</p>";
		}
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];

		if($imageName != ""){
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . basename($imageName);
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 10000000){
				$error = "<p class='message error'>Sorry, your file is too large.</p>";
				$uploadOk = 0;
			}
			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
			$error = "<p class='message error'>Sorry, only jpeg/jpg, png and gif files are allowed!</p>";
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
			$post_image = "<div><a href='$post_image_src' target='_black'><img class='postedImages' src='$imageName'></a></div>";


		}else{
			$error = "<p class='message error'>Something went wrong. Please, try again.</p>";
		}
	}
	if(isset($_POST['delete_post'])){ 

		$delete_post = mysqli_query($con, "DELETE FROM posts WHERE id='$id' AND added_by='$userLoggedIn'");
		header("Location: index.php");

	}

	if(isset($_POST['cancel'])){
		header("Location: index.php");
	}

	if(isset($_POST['update_image'])){
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];

		if($imageName != ""){
			$targetDir = "assets/images/posts/";
			$imageName = $targetDir . uniqid() . basename($imageName);
			$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

			if($_FILES['fileToUpload']['size'] > 10000000){
				$error = "<p class='message error'>Sorry, your file is too large.</p>";
				$uploadOk = 0;
			}
			if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg" && strtolower($imageFileType) != "gif"){
			$error = "<p class='message error'>Sorry, only jpeg/jpg, png and gif files are allowed!</p>";
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
			$message = "<p class='message success'>Image have been successfully updated!</p>";
			$post_image = "<div><a href='$post_image_src' target='_black'><img class='postedImages' src='$imageName'></a></div>";


		}else{
			$error = "<p class='message error'>Something went wrong. Please, try again.</p>";
		}
	}
	if(isset($_POST['delete_image'])){ 

		$delete_image = mysqli_query($con, "UPDATE posts SET image='' WHERE id='$id'");
		$message = "<p class='message success'>Image have been successfully removed!</p>";
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
				<?php if ($post_image == ""){ ?>
					<input type="file" name="fileToUpload" id="fileToUpload">
				<?php } ?>
				<input type="submit" name="save_post" class="warning inline" id="save_post" value="Save">
				<input type="submit" name="delete_post" class="danger inline" value="Delete Post">
				<input type="submit" name="cancel" class="default inline" value="Cancel">
			</form>
			<?php if ($post_image != ""){ ?>
				<h4>Edit attachment:</h4>
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
