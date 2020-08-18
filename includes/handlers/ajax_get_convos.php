<?php
 
include '../../config/config.php';
include '../classes/User.php';
include '../classes/Message.php';
 
		$userLoggedIn = $_POST['me'];
 
		$return_string = "";
		$convos = array();
 
		$query = mysqli_query($con, "SELECT * FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
 
		while($row = mysqli_fetch_array($query)) {
 
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
 
			if(!in_array($user_to_push, $convos)) 
				array_push($convos, $user_to_push);
		}
 
		foreach($convos as $username) {
 
			$is_unread_query = mysqli_query($con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$username') OR (user_from='$userLoggedIn' AND user_to='$username') ORDER BY id DESC");
			$row = mysqli_fetch_array($is_unread_query);
			$style = ($row['opened'] == 'no') ? "background-color: #DDEDFF" : "";
 
			$user_found_obj = new User($con, $username);
 
			$details = new Message($con, $userLoggedIn);
			$latest_message_details = $details->getLatestMessage($userLoggedIn, $username);
			
 
			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;
 
			if($row['opened'] === 'yes' && $row['user_from'] === $userLoggedIn && $row['user_to'] === $username) {
 
				$latest_message_details[2] .= " <b>✓</b>";
			}
 
			if ($row['opened'] === 'no' && $row['user_from'] === $userLoggedIn && $row['user_to'] === $username) {
 
				$style = "";
				$latest_message_details[2] .= " <b>←</b>";
			}
 
			if($row['opened'] === 'no' && $row['user_to'] === $userLoggedIn && $row['user_from'] === $username) {
 
				$style = "background-color: #DDEDFF";
			}
 
			if(strpos($latest_message_details[1], "http://") !== false) {
 
				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>" . $user_found_obj->getFirstAndLastName() . "<br><span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
								    <p id='grey' style='margin: 0;'></p></div></a>";
			}
 
			
			else  {
				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>" . $user_found_obj->getFirstAndLastName() . "<br><span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
								    <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p></div></a>";
			}
			
		}
 
		echo $return_string;
    ?>
