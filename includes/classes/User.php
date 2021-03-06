<?php
class User{
	private $user;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user'");
		$this->user = mysqli_fetch_array($user_details_query);

		// if($this->user == null) {
  //       exit("user is null. Username passed into class: $user");
  //   	}
	}
	public function getUsername(){
		return $this->user['username'];

	}
	public function getNumberFriendRequests(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$username'");
		return mysqli_num_rows($query);
	}
	public function getNumPosts() {
		$username = $this->user['username'];
		//$get_number_posts = mysqli_query($this->con, "SELECT * FROM posts WHERE added_by='$username'");
		//$num_posts = mysqli_num_rows($get_number_posts);
		//$query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$username'");
		$query = mysqli_query($this->con, "SELECT num_posts FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		return $row['num_posts'];
	}
	//get first and last name of the user
	public function getFirstAndLastName(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT first_name, last_name FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
	return $row['first_name'] . " " . $row['last_name']; 
	}

	public function getProfilePic(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT profile_pic FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
	return $row['profile_pic']; 
	}
	
	public function getFriendArray(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
	return $row['friend_array']; 
	}

	public function isClosed(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_closed FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		if($row['user_closed'] == 'yes')
			return true;
		else
			return false;
	}
	public function isAdmin(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT admin FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		if($row['admin'] == 'yes')
			return true;
		else
			return false;
	}
	public function isBlocked(){
		$username = $this->user['username'];
		$query = mysqli_query($this->con, "SELECT user_blocked FROM users WHERE username='$username'");
		$row = mysqli_fetch_array($query);
		if($row['user_blocked'] == 'yes')
			return true;
		else
			return false;
	}

	public function banUser($user_to_ban){
		$logged_in_user = $this->user['username'];

		$block_user = mysqli_query($this->con, "UPDATE users SET user_blocked='yes' WHERE username='$user_to_ban'");
		$block_user_posts = mysqli_query($this->con, "UPDATE posts SET user_closed='yes' WHERE added_by='$user_to_ban'");
	}

	public function isFriend($username_to_check){
		$usernameComma = "," . $username_to_check . ",";

		if(strstr($this->user['friend_array'], $usernameComma) || $username_to_check == $this->user['username']){
			return true;
		}
		else {
			return false;
		}
	}

	public function didReceiveRequest($user_from){
		$user_to = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	public function didSendRequest($user_to){
		$user_from = $this->user['username'];
		$check_request_query = mysqli_query($this->con, "SELECT * FROM friend_requests WHERE user_to='$user_to' AND user_from='$user_from'");
		if(mysqli_num_rows($check_request_query) > 0 ) {
			return true;
		} else {
			return false;
		}
	}
	public function removeFriend($user_to_remove){
		$logged_in_user = $this->user['username'];

		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_remove'");
		$row = mysqli_fetch_array($query);
		$friend_array_username = $row['friend_array'];

		//update friend array for user that logged in
		$new_friend_array = str_replace($user_to_remove . ",", "", $this->user['friend_array']);
		//update table:
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$logged_in_user'");

		$new_friend_array = str_replace($this->user['username'] . ",", "", $friend_array_username);
		$remove_friend = mysqli_query($this->con, "UPDATE users SET friend_array='$new_friend_array' WHERE username='$user_to_remove'");

	}
	public function sendRequest($user_to){
		$user_from = $this->user['username'];
		$query = mysqli_query($this->con, "INSERT INTO friend_requests VALUES (NULL, '$user_to', '$user_from')");
	}

	public function getFriends($user){
		$user_array = $this->user['friend_array'];
		$user_array_explode = explode(",", $user_array);
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user'");
		foreach($user_array_explode as $i){
			echo "<a href='".$i."'>".$i."</a>";
		}
	}
	public function getMutualFriends($user_to_check){
		$mutualFriends = 0;
		$user_array = $this->user['friend_array'];
		//split string into an array for userLoggedIn
		$user_array_explode = explode(",", $user_array);

		//for username to check
		$query = mysqli_query($this->con, "SELECT friend_array FROM users WHERE username='$user_to_check'");
		$row = mysqli_fetch_array($query);
		$user_to_check_array = $row['friend_array'];
		$user_to_check_array_explode = explode(",", $user_to_check_array);

		foreach($user_array_explode as $i){
			foreach($user_to_check_array_explode as $j){
				if($i == $j &&$i != ""){
					$mutualFriends++;
				}
			}
		}
		return $mutualFriends;

	}


}


?>