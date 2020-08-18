<?php
class Message {
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getMostRecentUser(){
		$userLoggedIn = $this->user_obj->getUsername();

		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC LIMIT 1");
		if(mysqli_num_rows($query) == 0)
			return false;

		$row = mysqli_fetch_array($query);
		$user_to = $row['user_to'];
		$user_from = $row['user_from'];

		if($user_to != $userLoggedIn)
			return $user_to;
		else
			return $user_from;
	}

	public function sendMessage($user_to, $body, $date, $imageName){

		$userLoggedIn = $this->user_obj->getUsername();
		$body = strip_tags($body);//removes html tags
		//$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace(array("\r\n", "\r", "\n"), " <br/> ", $body);
		$check_empty = preg_replace('/\s+/', '', $body);
		//if not empty
		if($check_empty != "") {
			$body_array = preg_split("/\s+/", $body);
 
			foreach($body_array as $key => $value) {

				$regex_images = '~https?://\S+?(?:png|gif|jpe?g)~';
				$regex_links = '~(?<!src=\')https?://\S+\b~x';

				if(strpos($value, "www.youtube.com/watch?v=") !== false){

					$link = preg_split("!&!", $value);

					$value = str_replace("https://www.youtube.com/watch?v=", "", $link[0]);

					$value = "<div class='youtube-embed' data-embed='". $value ."'></div>";
					
					//$key refers to position of the link
					$body_array[$key] = $value;
				}
				if(strpos($value, "https://youtu.be/") !== false){

					$link = preg_split("!\?!", $value);
					
					$value = str_replace("https://youtu.be/", "", $link[0]);

					$value = "<div class='embed-container youtube-embed' data-embed='". $value ."'></div>";
				
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
			 $body = implode(" ", $body_array);
			}

			$query = mysqli_query($this->con, "INSERT INTO messages VALUES(NULL, '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no', '$imageName')");
		}

	}

public function getMessages($otherUser) {
 
		$userLoggedIn = $this->user_obj->getUsername();
		$data = "";
 
		$query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");
 
		$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");
 
		while($row = mysqli_fetch_array($get_messages_query)) {
 
			$id = $row['id'];
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];
			$body = $row['body'];
			$date = $row['date'];
			$image = $row['image'];
			$friend = new User($this->con, $otherUser);
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
			return "<div id='$id'>" . $data . "</div>";
}
public function getLatestMessage($userLoggedIn, $user2) {
 
		$details_array = array();
 
		$query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");
 
		$row = mysqli_fetch_array($query);
 
		$sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";
 
 
		//Timeframe
 
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($row['date']); // Time of post
				$end_date = new DateTime($date_time_now); //Current time
				$interval = $start_date->diff($end_date); //Difference between dates
 
						if($interval->y >= 1) {
							if($interval->y == 1)
								$time_message = $interval->y . " year ago"; //1 year ago
							else 
								$time_message = $interval->y . " years ago"; //1+ year ago
						}
						else if ($interval->m >= 1) {
							if($interval->d == 0) {
								$days = " ago";
							}
							else if($interval->d == 1) {
								$days = $interval->d . " day ago";
							}
							else {
								$days = $interval->d . " days ago";
							}
 
 
							if($interval->m == 1) {
								$time_message = $interval->m . " month ". $days;
							}
							else {
								$time_message = $interval->m . " months ". $days;
							}
 
						}
						else if($interval->d >= 1) {
							if($interval->d == 1) {
								$time_message = "Yesterday";
							}
							else {
								$time_message = $interval->d . " days ago";
							}
						}
						else if($interval->h >= 1) {
							if($interval->h == 1) {
								$time_message = $interval->h . " hour ago";
							}
							else {
								$time_message = $interval->h . " hours ago";
							}
						}
						else if($interval->i >= 1) {
							if($interval->i == 1) {
								$time_message = $interval->i . " minute ago";
							}
							else {
								$time_message = $interval->i . " minutes ago";
							}
						}
						else {
							if($interval->s < 30) {
								$time_message = "Just now";
							}
							else {
								$time_message = $interval->s . " seconds ago";
							}
						}
 
		if(strpos($row['body'], "www.youtube.com") !== false) {
 
			$sent_by = ($row['user_to'] === $userLoggedIn) ? "They" : "You";
 
			$row['body'] = " sent a clip";
		}
 
		if(strpos($row['body'], "https://") !== false && strpos($row['body'], "www.youtube.com") === false) {
 
			$sent_by = ($row['user_to'] === $userLoggedIn) ? "They" : "You";
 
			$row['body'] = " sent a link";
		}					
		
		array_push($details_array, $sent_by);
		array_push($details_array, $row['body']);
		array_push($details_array, $time_message);
 
	return $details_array;
 
 
}
public function getConvos() {
 
		$userLoggedIn = $this->user_obj->getUsername();
 
		$return_string = "";
 
		$convos = array();
 
		$query = mysqli_query($this->con, "SELECT * FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
 
		while($row = mysqli_fetch_array($query)) {
 
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
 
			if(!in_array($user_to_push, $convos)) 
				array_push($convos, $user_to_push);
		}
 
		foreach($convos as $username) {
 
			$is_unread_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$username') OR (user_from='$userLoggedIn' AND user_to='$username') ORDER BY id DESC");
			$row = mysqli_fetch_array($is_unread_query);
			$style = ($row['opened'] == 'no') ? "background-color: #DDEDFF" : "";
 
			$user_found_obj = new User($this->con, $username);
 
			$details = new Message($this->con, $userLoggedIn);
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
 
			else {
				
				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>" . $user_found_obj->getFirstAndLastName() . "<br><span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
								    <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p></div></a>";
			}
			
		}
 
		echo $return_string;
}
public function getConvosDropdown($data, $limit) {
 
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();
 
		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
 
		$set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed='yes' WHERE user_to='$userLoggedIn'");
 
		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");
 
		while($row = mysqli_fetch_array($query)) {
 
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
 
			if(!in_array($user_to_push, $convos)) 
				array_push($convos, $user_to_push);
		}
 
		$num_iterations = 0; //Number of messages checked
		$count = 1; //Number of messages posted
 
		foreach($convos as $username) {
 
			if($num_iterations++ < $start)
				continue;
			
			if($count > $limit)
				break;
			else
				$count++;
 
			$is_unread_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$username') OR (user_from='$userLoggedIn' AND user_to='$username') ORDER BY id DESC");
			$row = mysqli_fetch_array($is_unread_query);
			$style = ($row['opened'] == 'no') ? "background-color: #DDEDFF" : "";
 
			$user_found_obj = new User($this->con, $username);
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
 
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
 
			if(strpos($latest_message_details[1], "http://") !== false) {
 
				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>" . $user_found_obj->getFirstAndLastName() . "<br><span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
								    <p id='grey' style='margin: 0;'></p></div></a>";
			} 
 
			else {
				$return_string .= "<a href='messages.php?u=$username'> <div class='user_found_messages' style='" . $style . "'>
								<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 5px; margin-right: 5px;'>" . $user_found_obj->getFirstAndLastName() . "<br><span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
								    <p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split  . "</p></div></a>";
			}
			
		}
 
		//If posts were loaded
 
		if($count > $limit)
			$return_string .="<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$return_string .="<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center;'>No more messages to load!</p>";
 
	return $return_string;
}
	public function getUnreadNumber(){
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT * FROM messages WHERE opened='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

}
?>
