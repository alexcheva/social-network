<?php
 
include '../../config/config.php';
include '../classes/User.php';
 
		$userLoggedIn = $_POST['me'];
		$otherUser = $_POST['friend'];
		
		
		$data = "";
 
		$query = mysqli_query($con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");
 
		$get_messages_query = mysqli_query($con, "SELECT messages.*
							  FROM messages
							  JOIN (  
							        SELECT MAX(id) id
							        FROM messages
							        WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')
							        ) x ON messages.id = x.id");
 
		
		while($row = mysqli_fetch_array($get_messages_query)) {
 
			$id = $row['id'];
			$user_to = $row['user_to'];
			$body = $row['body'];
			$date = $row['date'];
			$image = $row['image'];
			$friend = new User($con, $otherUser);
			$friend_name = $friend->getFirstName();
			$pic = $friend->getProfilePic();
 
			if($image != "") {
 
				$imageDiv = "<div class='postedImage'>
								<img src='$image'>
							</div>";
			}
 
			else {
 
				$imageDiv = "";
			}
 
			
			
			$info = ($user_to === $userLoggedIn) ? $friend_name . " on " . date("M d Y H:i", strtotime($date)) : 
													"You" .  " on " . date("M d Y H:i", strtotime($date));
 
 
			$div_top = ($user_to === $userLoggedIn) ? "<img src='" . $pic . "' height='70' width='70' style='margin: 0 7px; 												   border-radius: 50%;'><div class='message_g' id='green'>" :
													     "<div class='message_b' id='blue'>";
 
			$body_array = preg_split("/\s+/", $body);
 
 
			foreach($body_array as $key => $value) {
 
				if(strpos($value, "www.youtube.com/watch?v=") !== false) {
 
					$link = preg_split("!&!", $value);
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<p><iframe width='400' height='300' src='" . $value . "'></iframe></p>";
					$body_array[$key] = $value;
 
					$body = implode(" ", $body_array);
 
				}
				
			} 										
 
			
			$data = $data . $div_top . "<span>" . $info . "</span>" . nl2br($body) . $imageDiv . "</div><br><br>";
			
		}
	
			if($data !== "")
				echo "<div id='$id'>" . $data . "</div><div class='checkSeen'></div>";
  ?>
