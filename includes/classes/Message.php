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

	public function sendMessage($user_to, $body, $date){
		//if not empty
		if($body != ""){
			$userLoggedIn = $this->user_obj->getUsername();
			$query = mysqli_query($this->con, "INSERT INTO messages VALUES(NULL, '$user_to', '$userLoggedIn', '$body', '$date', 'no', 'no', 'no')");
		}
	}

	public function getMessages($otherUser){
		$userLoggedIn = $this->user_obj->getUsername();
		$data = "";

		$query = mysqli_query($this->con, "UPDATE messages SET opened='yes' WHERE user_to='$userLoggedIn' AND user_from='$otherUser'");
		//retrive the messages between two users:
		$get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$otherUser') OR (user_from='$userLoggedIn' AND user_to='$otherUser')");
		//while we have results populate the data
		while($row = mysqli_fetch_array($get_messages_query)) {
			$user_to = $row['user_to'];
			$user_from = $row['user_from'];
			$body = $row['body'];
			//if logged in user send it make puple, else make bright purple
			$div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='received_message'>" : "<div class='message' id='sent_message'>";
			$data = $data . $div_top . $body . "</div><br><br>";

		}
		return $data;
	}
	public function getLatestMessage($userLoggedIn, $user2){
		$details_array = array();

		$query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user2') OR (user_to='$user2' AND user_from='$userLoggedIn') ORDER BY id DESC LIMIT 1");

		$row = mysqli_fetch_array($query);
		$sent_by = ($row['user_to'] == $userLoggedIn) ? "They said: " : "You said: ";

		//pass date from db to post class into get time method
		$post_obj = new Post($this->con, $userLoggedIn);
		$time_message = $post_obj->getTime($row['date']);

		array_push($details_array, $sent_by);
		array_push($details_array, $row['body']);
		array_push($details_array, $time_message);

		return $details_array;
	}

	public function getConvos(){
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";
		$convos = array();

		$query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to='$userLoggedIn' OR user_from='$userLoggedIn' ORDER BY id DESC");

		while($row = mysqli_fetch_array($query)) {
			//push user into an array
			$user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];
			//check if username is not in the array yet
			if(!in_array($user_to_push, $convos)) {
				array_push($convos, $user_to_push);
			}
		}

		foreach($convos as $username){
			$user_found_obj = new User($this->con, $username);
			//get the latest message between two users
			$latest_message_details = $this->getLatestMessage($userLoggedIn, $username);
			
			$dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
			$split = str_split($latest_message_details[1], 12);
			$split = $split[0] . $dots;

			$return_string .= "<a href='messages.php?u=$username'>
			<div class='user_found_messages'>
				<div class='user_image'>
					<img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50%; border: 2px solid purple; width: 50px; margin-right: 15px;'>
				</div>
		    	<div class='found_message'>
		    	<p>" . $user_found_obj->getFirstAndLastName() . "</p>
		    	<p id='grey' style='margin: 0;'>" . $latest_message_details[0] . $split . "</p>
		    	<p class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</p>
				</div>
		    </div> </a>";


		}
		return $return_string;
	}
	public function getUnreadNumber(){
		$userLoggedIn = $this->user_obj->getUsername();
		$query = mysqli_query($this->con, "SELECT * FROM messages WHERE opened='no' AND user_to='$userLoggedIn'");
		return mysqli_num_rows($query);
	}

}
?>