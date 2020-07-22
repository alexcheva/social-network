<?php
class Notification{
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function getUnreadNumber(){
	$userLoggedIn = $this->user_obj->getUsername();
	//might be viewed
	$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE opened='no' AND user_to='$userLoggedIn'");
	return mysqli_num_rows($query);
	}

	public function getNotifications($data, $limit){
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();
		$return_string = "";


		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;

		$set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed='yes' WHERE user_to='$userLoggedIn'");
		$query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to='$userLoggedIn' ORDER BY id DESC");

		if(mysqli_num_rows($query) == 0) {
			echo "<p>You have no notifications!</p>";
		}

		$num_iterations = 0;
		$count = 1;

		while($row = mysqli_fetch_array($query)){
			if($num_iterations++ < $start)
				continue;
			if ($count > $limit)
				break;
			else
				$count++;

			$user_from = $row['user_from'];
			//get timeframe
			$post_obj = new Post($this->con, $userLoggedIn);
			$time_message = $post_obj->getTime($row['datetime']);
			//get the data from the user who notification is from
			$user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
			$user_data = mysqli_fetch_array($user_data_query);

			$opened = $row['opened'];
			//hightlight unread messages:
			$style = (isset($row['opened']) && $row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

			$return_string .= "<a href='". $row['link'] . "'>
								<div class='notification'>
									<img class='notificationsProfilePic' src='". $user_data['profile_pic']. "'>
									<div class='notification_body'>". $row['message'] . "</div>
									<div class='notification_time' id='grey'>". $time_message ."</div>
								</div>
								</a>";

		}
		//if notifications were loaded
		if($count > $limit)
			$return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) ."'><input type='hidden' class='noMoreDropdownData' value='false'>";
		else
			$return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p>No more notifications to load!</p>";
		return $return_string;

	}

	public function insertNotification($post_id, $user_to, $type) {
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		switch($type){
			case 'comment':
				$message = $userLoggedInName . " commented on your post.";
				break;
			case 'like':
				$message = $userLoggedInName . " liked your post.";
				break;
			case 'profile_post':
				$message = $userLoggedInName . " posted on your profile.";
				break;
			case 'comment_non_owner':
				$message = $userLoggedInName . " commented on a post you commented on.";
				break;
			case 'profile_comment':
				$message = $userLoggedInName . " commented on your profile post.";
				break;
		}

		$link = "post.php?id=" . $post_id;

		$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES(NULL, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
	}

	public function friendNotification($user_to) {
		$userLoggedIn = $this->user_obj->getUsername();
		$userLoggedInName = $this->user_obj->getFirstAndLastName();

		$date_time = date("Y-m-d H:i:s");

		$message = $userLoggedInName . " accepted your friend request.";

		$link = $userLoggedIn;

		$insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES(NULL, '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
	}

}

?>