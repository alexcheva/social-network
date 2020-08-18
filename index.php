<?php
	include('includes/header.php');
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
	<input type='hidden' class="index"/>
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

	<input type='hidden' class="bottom"/>
	<?php include('footer.php'); ?>
