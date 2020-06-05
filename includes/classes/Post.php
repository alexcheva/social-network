<?php
class Post{
	//define class properfies/attributes:
	private $user_obj;
	private $con;
	//constuct an automatic behavior for every new post/object:
	public function __construct($con, $user){
		$this->con = $con;
		$this->user_obj = new User($con, $user);
	}
	//handle post submission
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
	//Time frame
	public function getTime($date_time){
			
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
		return $time_message;
	}

	//Posts Loading function
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
					$user_to_obj = new User($this->con, $row['user_to']);
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
				//delete post if logged in user is the one posted
				if($userLoggedIn == $added_by)
					$delete_button = "<a class='delete_button' id='post$id'><i class='fas fa-trash-alt'></i></a>";
				else
					$delete_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment iframe/embed -->
				<script>
					function toggle<?php echo $id; ?>(){
						var target = $(event.target);
						if(!target.is("a")){
							var element = document.getElementById("toggleComment<?php echo $id; ?>");
							if(element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";
						}
					}

				</script>
				<?php

				//Check if there are comments:
				//find all comments for post:
				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				//find number of results:
				$comments_check_num = mysqli_num_rows($comments_check);
				//add time frame
				$time_message = $this->getTime($date_time);
				//Number of likes:
				$get_likes = mysqli_query($this->con, "SELECT * FROM likes WHERE post_id='$id'");
				$total_likes = mysqli_num_rows($get_likes);

				//Check for previous likes
				$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$num_rows = mysqli_num_rows($check_query);
                $like_button = '';
				if($num_rows > 0) {
					$like_button .= "<input id='like_button_$id' type='button' class='comment_like' name='like_button' value='Unlike' onclick='sendLike($id)'>
					";
				}
				else {
					$like_button .= "
							<input type='button' id='like_button_$id' class='comment_like' name='like_button' value='Like' onclick='sendLike($id)'>
					";
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
							$delete_button
							<hr>
							<div class='newsfeedPostOptions'>
								<span class='num_comments' onClick='javaScript:toggle$id()'>
									Comments ($comments_check_num)
								</span>
								<span class='like_value' id='total_like_$id'>$total_likes Likes </span>
								<span>$like_button</span>
							</div>					
						</div>
						<div class='post_comment' id='toggleComment$id' style='display: none;'>
						<embed src='comment_frame.php?post_id=$id' class='comment_frame' style='color: white;' frameborder='0'></embed></div>
						<hr>";
				}//if friend end if statement

				?>
				<script>
				//Delete post functionality bootbox
					$(document).ready(function(){
						$('#post<?php echo $id; ?>').on('click', function(){
							//bootstrap
							bootbox.confirm({
								message: "Are you sure you want to delete this post?", buttons: {
	        					confirm: {
						            label: 'Yes'
						        },

						        cancel: {
						            label: 'No'						        }
						    	},
						    	
						        callback:
						    	function
								(result){
									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result: result});
									//if there is a result = true
									if(result)
										location.reload();
								}
							});
						});
					});
				</script>
				<?php
			}//end while loop

			if($count > $limit)
				$str .="<input type='hidden' class='nextPage' value='" . ($page + 1) ."'><input type='hidden' class='noMorePosts' value='false'>";//append to str
				else
				$str .="<input type='hidden' class='noMorePosts' value='true'><p style='text-align: center; color: white;'> No More Posts to show!</p>";
		}//end if statement
		echo $str;	

	}

	//Profile posts loading function
	public function loadProfilePosts($data, $limit){
		$page = $data['page'];
		//comes from ajax_load_profile_posts.php $_REQUEST and ajaxReq data
		$profileUsername = $data['profileUsername'];
		$userLoggedIn = $this->user_obj->getUsername();

		if($page == 1)
			$start = 0;
		else
			$start = ($page - 1) * $limit;
		
		$str = ""; //string to return
		//show only posts that are not addressed to anybody or addressed to this user
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by='$profileUsername' AND user_to='none') OR user_to='$profileUsername') ORDER BY id DESC");
		//if there are posts:
		if(mysqli_num_rows($data_query) > 0){

			$num_iterations = 0; //Number of results checked (not nessasery posted)
			$count = 1; //how many results will be loaded


			while($row = mysqli_fetch_array($data_query)){
				$id = $row['id'];
				$body = $row['body'];
				$added_by = $row['added_by'];
				$date_time = $row['date_added'];

				if($num_iterations++ < $start)
					continue;

				//once 10 posts have been loaded, break
				if($count > $limit) {
					break;
				} else {
					$count++;
				}
				//delete post if logged in user is the one posted
				if($userLoggedIn == $added_by)
					$delete_button = "<a class='delete_button' id='post$id'><i class='fas fa-trash-alt'></i></a>";
				else
					$delete_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment iframe/embed -->
				<script>
					function toggle<?php echo $id; ?>(){
						var target = $(event.target);
						if(!target.is("a")){
							var element = document.getElementById("toggleComment<?php echo $id; ?>");
							if(element.style.display == "block")
								element.style.display = "none";
							else
								element.style.display = "block";
						}
					}

				</script>
				<?php

				//Check if there are comments:
				//find all comments for post:
				$comments_check = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
				//find number of results:
				$comments_check_num = mysqli_num_rows($comments_check);


				//Time frame
				$time_message = $this->getTime($date_time);

				$str .= "<div class='status_post' onClick='javaScript:toggle$id()'>
							<div class='post_profile_pic'>
								<img class='post_profile_img' src='$profile_pic'>
							</div>
							<div class='posted_by'>
								<a href='$added_by'>$first_name $last_name</a>
							</div>
							<div class='post_body'>
								$body
							</div>
							<div class='post_time'>
								$time_message
							</div>
							$delete_button
							
							<hr>
							<div class='newsfeedPostOptions'>
								<span class='num_comments' onClick='javaScript:toggle$id()'>
									Comments ($comments_check_num)
								</span>
								<embed src='like.php?post_id=$id'></embed>
							</div>					
						</div>
						<div class='post_comment' id='toggleComment$id' style='display: none;'>
						<embed src='comment_frame.php?post_id=$id' class='comment_frame' style='color: white;' frameborder='0'></embed></div>
						<hr>";

				?>
				<script>
				//Delete post functionality bootbox
					$(document).ready(function(){
						$('#post<?php echo $id; ?>').on('click', function(){
							//bootstrap
							bootbox.confirm({
								message: "Are you sure you want to delete this post?", buttons: {
	        					confirm: {
						            label: 'Yes'
						        },

						        cancel: {
						            label: 'No'						        }
						    	},
						    	
						        callback:
						    	function
								(result){
									$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result: result});
									//if there is a result = true
									if(result)
										location.reload();
								}
							});
						});
					});
				</script>
				<?php
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