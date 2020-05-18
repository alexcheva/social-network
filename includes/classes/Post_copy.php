<?php
class Post{
	private $user_obj;
	private $con;

	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}

	public function submitPost($body, $user_to){
		$body = strip_tags($body);//removes html tags
		$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace('\r\n', '\n', $body);
		$body = nl2br($body); //replace new line with line break

		$check_empty = preg_replace('/\s+/', '', $body); //deletes all spaces
		
		if($check_empty != "") {

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//if user is on own profile, user_to is 'none'
			if($user_to == $added_by){
				$user_to = "none";
			}
			//insert post into a database
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no','no','0')");
			$returned_id = mysqli_insert_id($this->con);

			//Insert notification

			//Update post count for user
			$num_posts = $this->user_obj->getNumPosts();
			$num_posts++;
			$update_query = mysqli_query($this->con, "UPDATE users SET num_posts='$num_posts' WHERE username='$added_by'");

		}
	}
	//Posts Loadign function
	public function loadPostsFriends($data, $limit){
		$page = $data['page'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
		
		$str = ""; //string to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
		//if there are posts:
		if(mysqli_num_rows($data_query) > 0){

			$num_iterations = 0; //Number of results checked (not nessasery posted)
			$count = 1; //how many results will be loaded


			while($row = mysqli_fetch_array($data_query)){
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				//prepare user_to string so it can be included even if noot posted to a user
				if($row['user_to'] == "none") {
					$user_to = "";
				}
				else {
					$user_to = new User($con, $row['user_to']);
					$user_to_name = $user_to_obj->getFirstAndLastName();
					$user_to = "to <a href='" . $row['user_to'] ."'>" . $user_to_name . "</a>";

				}
				//check is user have their account closed
				$added_by_obj = new User($this->con, $added_by);
				if($added_by_obj->isClosed()){
					continue;
				}

				$user_logged_obj = new User($this->con, $userLoggedIn);
				if($user_logged_obj->isFriend($added_by)){

				if($num_iterations++ < $start)
					continue;

				//once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				} else {
					$count++;
				}

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment iframe/embed -->
				<script>
					function toggle<?php echo $id; ?>(){
						var element = document.getElementById("toggleComment<?php echo $id; ?>");
						if(element.style.display == "block")
							element.style.display = "none";
						else
							element.style.display = "block";
					}

				</script>
				<?php

				//Time frame
				$date_time_now = date("Y-m-d H:i:s");
				$start_date = new DateTime($date_time);//time of post
				$end_date = new DateTime($date_time_now);//current time
				$interval = $start_date->diff($end_date); //difference between dates
				
				if($interval->y >= 1){
					if($interval == 1)
						$time_message = $interval->y . " year ago";
					
					else 
						$time_message = $interval->y . " years ago";
					
				}
				else if($interval-> m >= 1){
					if($interval->d == 0) {
						$days = " ago";
					}
					else if ($interval->d == 1) {
						$days = $interval->d . " day ago";
					} else {
						$days = $interval->d . " days ago";
					}
					if($interval->m == 1){
						$time_message = $interval->m . " month" . $days;
					} else{
						$time_message = $interval->m . " months" . $days;
					}
				}
				else if($interval->d >= 1){
					if ($interval->d == 1){
						$time_message = "Yesterday";
					} else{
						$time_message = $interval->d . " days ago";
					}
				}
				else if($interval->h >=1){
					if ($interval->h == 1){
						$time_message = $interval->h . " hour ago";
					} else{
						$time_message = $interval->h . " hours ago";
					}
				}
				else if($interval->i >= 1){
					if ($interval->i == 1){
						$time_message = $interval->i ." minute ago";
					} else{
						$time_message = $interval->i . " minutes ago";
	 				}
				}
				else{
					if($interval->s <30){
						$time_message = "Just now";
					} else{
						$time_message = $interval->s . " seconds ago";
					}
				}

				$str .= "<div class='status_post' onClick='javaScript:toggle$id()'>
							<div class='post_profile_pic'>
								<img class='post_profile_img' src='$profile_pic'>
							</div>
							<div class='posted_by'>
								<a href='$added_by'>$first_name $last_name</a> $user_to 
							</div>
							<div class='post_body'>
								$body
							</div>
							<div class='post_time'>
								$time_message
							</div>
							<hr>
							<p> Show comments </p>						
						</div>
						<div class='post_comment' id='toggleComment$id' style='display: none;'>
						<embed src='comment_frame.php?post_id=$id' class='comment_frame' style='color: white;' frameborder='0'></embed></div>
						<hr>";
				}//if friend end if statement
			}//end while loop

			if($count > $limit)
				$str .="<input type='hidden' class='nextPage' value='" . ($page + 1) ."'><input type='hidden' class='noMorePosts' value='false'>";//append to str
				else
				$str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center; color: white;'> No More Posts to show!</p>";
		}//end if statement
		echo $str;	

	}
}



?>