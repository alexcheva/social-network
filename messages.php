<?php 
	include("includes/header.php");

	$message_obj = new Message($con, $userLoggedIn);

	if(isset($_GET['u']))
		$user_to = $_GET['u'];
	else {
		//show the most recent person you talked to
		$user_to = $message_obj->getMostRecentUser();
		if($user_to == false)
			$user_to = 'new';
		}

	if($user_to != "new")
		$user_to_obj = new User ($con, $user_to);

	if(isset($_POST['post_message'])){
		$uploadOk = 1;
		$imageName = $_FILES['fileToUpload']['name'];
		$errorMessage = "";

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
			$body = mysqli_real_escape_string($con,$_POST['message_body']);
			$date = date("Y-m-d H:i:s");
			$message_obj->sendMessage($user_to, $body, $date, $imageName);
			header("Location: messages.php");
		}else{
			echo "<script>bootbox.alert('$errorMessage');</script>";
		}

		// if(isset($_POST['message_body'])){
		// 	//turn into string
		// 	$body = mysqli_real_escape_string($con,$_POST['message_body']);
		// 	$date = date("Y-m-d H:i:s");
		// 	$message_obj->sendMessage($user_to, $body, $date, $imageName);
		// 	header("Location: messages.php");

		// }
	}

 ?>

<div class="message_details column profile_left" id="conversations">
		<h4>Conversations:</h4>
		<div class="loaded-conversations">
			<!-- go to Message classes and run getConvos function -->
			<?php echo $message_obj->getConvos(); ?>
		</div>
		<a href="messages.php?u=new">New Message</a>
</div>

<div class="message_column column profile"  id="main_column">
	<?php 
		if($user_to != "new"){
			echo "<h4>Messages between you and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr>";	
			echo "<div class='loaded_messages' id='scroll_messages'>";
			echo $message_obj->getMessages($user_to);
			echo "</div><hr>";
		}
		else {
			echo "<h4>New Message:</h4>";
		}
	 ?>

	 <div class="message_post">
	 	<form action="" method="POST" enctype="multipart/form-data">
	 		<?php 
	 		if($user_to == "new"){
	 			echo "<p>Select the friend you would like to message: </p>";
	 			?>

	 			To: <input type='text' onkeyup='getUserFriends(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>
	 			<?php
	 			echo "<div class='results'></div>";

	 		}
	 		else{
	 			echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
	 			echo '<a href="javaScript:void(0)" onClick="showFileUpload()">
				<i class="fas fa-file-image" id="toggle_file_upload"></i>
			</a>
			<input type="file" name="fileToUpload" id="fileToUpload" class="hide">';
	 			echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
	 		}


	 		 ?>
	 		
	 	</form>
	 </div>
		
</div>
<!-- Medium Quality: https://img.youtube.com/vi/{video-id}/mqdefault.jpg (320×180 pixels)
High Quality: http://img.youtube.com/vi/G0wGs3useV8/hqdefault.jpg (480×360 pixels)
Standard Definition (SD): http://img.youtube.com/vi/G0wGs3useV8/sddefault.jpg (640×480 pixels)
Maximum Resolution: http://img.youtube.com/vi/G0wGs3useV8/maxresdefault.jpg (1920×1080 pixels) -->
<!-- 
(480×360 pixels)
	width: 20%;
    height: auto;
    position: relative;
    top: 140px;
    left: 190px; -->
<script>
	$(document).ready(function(){
    	$("#message_textarea").emojioneArea({
			pickerPosition: "top" });
    	
		var youtube = document.querySelectorAll( ".youtube-embed" );

		if(window.matchMedia( "(max-width: 500px)" ).matches) {
			for (var i = 0; i < youtube.length; i++) {

				youtube[i].innerHTML = "<div class='youtube-play' style='width: 320px; height: 180px; background-image: url(https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/mqdefault.jpg);'><img class='youtube-logo'  style='width: 20%; height: auto; position: relative; top: 66px; left: 128px;' src='assets/images/icons/youtube.png'></div>";
				youtube_img = "<img src='https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/sddefault.jpg' async class='play-youtube-video'>";
		    
		    	youtube[i].addEventListener( "click", function() {

		            this.innerHTML = '<iframe style="width: 320px; height: 180px;" allowfullscreen frameborder="0" class="embed-responsive-item" src="https://www.youtube.com/embed/' + this.dataset.embed + '"></iframe>';
		       
		    	});
			};
		} else{
				for (var i = 0; i < youtube.length; i++) {

				youtube[i].innerHTML = "<div class='youtube-play' style='width: 480px; height: 360px; background-image: url(https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/hqdefault.jpg);'><img class='youtube-logo'  style='width: 20%; height: auto; position: relative; top: 140px; left: 190px;' src='assets/images/icons/youtube.png'></div>";
				youtube_img = "<img src='https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/sddefault.jpg' async class='play-youtube-video'>";
		    
		    	youtube[i].addEventListener( "click", function() {

		            this.innerHTML = '<iframe style="width: 480px; height: 360px;" allowfullscreen frameborder="0" class="embed-responsive-item" src="https://www.youtube.com/embed/' + this.dataset.embed + '"></iframe>';
		       
		    	});
			};
		}
		

		var embeded_images = document.querySelectorAll( ".embed-images" );

		for (var i = 0; i < embeded_images.length; i++) {

			embeded_images[i].innerHTML = "<a target='_blank' title='Open image in a new window' class='external_link' href='" + embeded_images[i].dataset.embed + "'><img class='postedImages' src='" + embeded_images[i].dataset.embed + "'></a>";
		};

		var embeded_link = document.querySelectorAll( ".embed-link" );

		for (var i = 0; i < embeded_link.length; i++) {

			embeded_link[i].innerHTML = "<a target='_blank' title='Open link in a new window' class='external_link' href='" + embeded_link[i].dataset.embed + "''>" + embeded_link[i].dataset.embed + "</a>";
		};
    });
    var div = document.getElementById("scroll_messages");
 
    if(div != null) {
        div.scrollTop = div.scrollHeight;
    }
    
</script>
