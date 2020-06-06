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
		if(isset($_POST['message_body'])){
			//turn into string
			$body = mysqli_real_escape_string($con,$_POST['message_body']);
			$date = date("Y-m-d H:i:s");
			$message_obj->sendMessage($user_to, $body, $date);

		}
	}

 ?>
<div class="user_details_message column profile_left">
	<a href="<?php echo $userLoggedIn ?>">
		<img id="message_profile_pic" src="<?php echo $user['profile_pic']; ?>">
	</a>
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
	 	<form action="" method="POST">
	 		<?php 
	 		if($user_to == "new"){
	 			echo "<p>Select the friend you would like to message: </p>";
	 			?>

	 			To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>
	 			<?php
	 			echo "<div class='results'></div>";

	 		}
	 		else{
	 			echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message ...'></textarea>";
	 			echo "<input type='submit' name='post_message' class='info' id='message_submit' value='Send'>";
	 		}


	 		 ?>
	 		
	 	</form>
	 </div>
		
</div>
<script>
    var div = document.getElementById("scroll_messages");
 
    if(div != null) {
        div.scrollTop = div.scrollHeight;
    }
</script>
	<div class="message_details column profile_left" id="conversations">
		<h4>Conversations:</h4>
		<div class="loaded-conversations">
			<!-- go to Message classes and run getConvos function -->
			<?php echo $message_obj->getConvos(); ?>
		</div>
		<br>
		<a href="messages.php?u=new">New Message</a>
	</div>
</div>
