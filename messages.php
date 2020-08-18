<?php   
 
require ("includes/header.php");
 
 
$message_obj = new Message($con, $userLoggedIn);
 
if(isset($_GET['u'])) 
	$user_to = $_GET['u'];
 
else {
 
	$user_to = $message_obj->getMostRecentUser();
 
	if($user_to == false)
		$user_to = 'new';
}
 
if($user_to != "new") {
	$user_to_obj = new User($con, $user_to);
	$friend_name = $user_to_obj->getFirstAndLastName();
	$friend_username = $user_to_obj->getUsername();
 
	$query_friend = mysqli_query($con, "SELECT profile_pic FROM users WHERE username='$friend_username'");
 
	$row = mysqli_fetch_array($query_friend);
 
?>
 
<div class="user_details column">
 
		<img src="<?php echo $row['profile_pic']; ?>" id="mess_pic">
</div>
 
<?php
 
}
 
?>
 
<div class="main_column column" id="main_column">
			
			<?php  
 
				if($user_to != "new"){
 
					echo "<h4>&nbsp;You and <a href='$user_to'>" . $friend_name . "</a></h4><hr><br>";
					echo "<div class='loaded_messages' id='scroll_messages'>";
					
					echo $message_obj->getMessages($user_to);
					
					echo "</div>";
				}
 
				else
					echo "<h4>New Message</h4>";
			?>
 
 
		<div class="message_post">
 
			<form action="" method="POST" enctype="multipart/form-data" name="imgForm">
			
				<?php 
 
				if($user_to == "new") {
					echo "Select the friend you would like to message <br><br>";
					?> 
					
					To: <input type='text' onkeyup='getUser(this.value, "<?php echo $userLoggedIn;?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'>
 
					<?php
					echo "<div class='results'></div>";
				}
 
				else {
					echo "<textarea name='message_body' id='message_textarea' placeholder='Write your message, click Send or hit Enter&#10;Press Shift+Enter for new line'></textarea>";
					?>
					<input type='button' onclick="sendMessage()" name='post_message' class='info' id='message_submit' value='Send'>
					<?php
					echo "<input type='file' name='fileToUpload' id='fileToUpload'>";
				}
 
				?>	
 
 
			</form>	
 
 
		</div>
 
</div>
 
<div class="user_details column" id="conversations">
			
	<h4>Conversations</h4>
 
	<div class="loaded_conversations">
 
		<?php echo $message_obj->getConvos(); ?>
		
	</div>
	<br>
 
	<a href="messages.php?u=new">New Message</a>
 
</div>
 
 
 
<script>
 
	var div = document.getElementById("scroll_messages");
 
	if(div != null) {
		div.scrollTop = div.scrollHeight;
	}
 
	var userTo = "<?php echo $user_to; ?>";
 
	const sendMessage = () => {
 
		var body = $("textarea").val();
 
                var form = document.forms.namedItem("imgForm");
 
		var formData = new FormData(form);
 
		var otherData = [];
 
		otherData.push({"me":userLoggedIn, "body":body, "friend":userTo});
 
		formData.append('otherData', JSON.stringify(otherData));
 
		$.ajax({
	        type: "POST",
	        url: "includes/handlers/send_message.php",
	        data: formData,
	        contentType: false,
	        processData: false,   
    		success:(function(data) {
        	
        		$("textarea").val("");
 
			$(".checkSeen").remove();
    		})
		});
 
		const scrollDown = () => {
 
			div.scrollTop = div.scrollHeight;
		}
 
		setTimeout(scrollDown, 800);
 
		var file = document.getElementById("fileToUpload");	
		file.value = file.defaultValue;
	}
 
	const getMessages = () => {
 
		$.post("includes/handlers/get_messages.php", {me:userLoggedIn, friend:userTo}, function(result){
 
			$(".loaded_messages").append(result);
 
			  var all_elements = $(".loaded_messages").children();
 
			  all_elements.each(function(){
			    var el_id = this.id;
			    
			    // data("verified") prevents the removal triggered by its duplicate, if any.
			    $(this).data("verified",true);
 
			    all_elements.each(function(){
			      if(el_id==this.id && !$(this).data("verified")){
			        $(this).remove();
			      }
			    });
			  });
			  
			  // Turn all "surviving" element's data("verified") to false for future "clean".
			  $(".loaded_messages").children().each(function(){
			    $(this).data("verified",false);
			  });
		
		});
	}
 
	setInterval(getMessages, 500);
 
 
	const getConvos = () => {
 
		$.post("includes/handlers/get_convos.php", {me:userLoggedIn}, function(data){
 
			$(".loaded_conversations").html(data);
 
		});
	}
 
	setInterval(getConvos, 2000);
 
	const checkSeen = () => {
 
		$.post("includes/handlers/check_seen.php", {me:userLoggedIn, friend:userTo}, function(data){
 
			$(".checkSeen").html(data);
			
		});
	}
 
	setInterval(checkSeen, 4000);
	
 
	$(function(){
	
		$(document).keypress(function(e){
 
			if(e.keyCode === 13 && e.shiftKey === false && $("#message_textarea").is(":focus")) {
 
				e.preventDefault();
 
				$("#message_submit").click();
 
				const scrollDown = () => {
 
					div.scrollTop = div.scrollHeight;
				}
 
				setTimeout(scrollDown, 800);	
			}
 
		});
 
	});
 
</script>
<?php include('footer.php'); ?>
