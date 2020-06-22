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
	public function submitPost($body, $user_to, $imageName){
		$body = strip_tags($body);//removes html tags
		//$body = mysqli_real_escape_string($this->con, $body);
		// $body = str_replace('\r\n', '\n', $body);
		//$body = nl2br($body); //replace new line with line break

		$check_empty = preg_replace('/\s+/', '', $body); //deletes all spaces
		
		//https://www.youtube.com/?app=desktop
		if($check_empty != "") {
			//redex split at spaces
			$body_array = preg_split("/\s+/", $body);

			foreach($body_array as $key => $value){
				//string position, look for the following string:
				if(strpos($value, "www.youtube.com/watch?v=") !== false){
					//replace a string inside a string:
					$link = preg_split("!&!", $value);
					//find !that!
					$value = preg_replace("!watch\?v=!", "embed/", $link[0]);
					$value = "<div class=\'embed-container\'><iframe src=\'" . $value ."\' frameborder=\'0\' allowfullscreen></iframe></div>";
					//save newly modified $value into post:
					//$key refers to position of the link
					$body_array[$key] = $value;
				}
			}
			//separate array elements with a space
			$body = implode(" ", $body_array);

			//Current date and time
			$date_added = date("Y-m-d H:i:s");
			//Get username
			$added_by = $this->user_obj->getUsername();

			//if user is on own profile, user_to is 'none'
			if($user_to == $added_by){
				$user_to = "none";
			}
			//insert post into a database
			$query = mysqli_query($this->con, "INSERT INTO posts VALUES(NULL, '$body', '$added_by', '$user_to', '$date_added', 'no','no','0', '$imageName')");
			//find out the id of the last post
			$returned_id = mysqli_insert_id($this->con);

			//Insert notification when someone posts on user profile
			if($user_to != 'none'){
				$notification = new Notification($this->con, $added_by);
				$notification->insertNotification($returned_id, $user_to, "profile_post");
			}

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
				$time_message = $interval->m . " month " . $days;
			} else{
				$time_message = $interval->m . " months " . $days;
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
				$imagePath = $row['image'];

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
				//edit post functionality
				if($userLoggedIn == $added_by)
					$edit_button = "<a class='edit_button' id='edit_post$id' data-toggle='modal' data-target='#edit_post$id'><i class='fas fa-edit'></i></a>";
				else
					$edit_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment -->
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

				//EMOJI
				// $body_array = preg_split("/[ ]+|\n/", $body);
				// foreach($body_array as $key => $value) {
				//      $body = implode(" ", Emojis::createEmojis($body_array, $key, $value));
				// }

				//image
				if($imagePath != ""){
					$imageDiv = "<div><a href='$imagePath' target='_black'><img class='postedImages' src='$imagePath'></a></div>";
				}else{
					$imageDiv = "";
				}
				
				//Get number of likes for the post:
				$get_likes = mysqli_query($this->con, "SELECT likes FROM posts WHERE id='$id'");
				$row = mysqli_fetch_array($get_likes);
				$total_likes = $row['likes'];

				//Check for previous likes
				$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$num_rows = mysqli_num_rows($check_query);
                $like_button = '';

                $liked = $num_rows > 0 ? 'liked' : 'unliked';

                //if you already liked:
				$like_button .= "<a id='like_button_$id' class='$liked' name='like_button' value='Unlike' onclick='sendLike($id)'>
    			    <i class='fas fa-heart-broken broken-heart hover'></i>
					<i class='fas fa-heart full hover'></i>
                    <i class='far fa-heart hollow active'></i>
				</a>
				";

				$str .= "<div class='status_post'>
							<div class='post_profile_pic'>
								<img class='post_profile_img' src='$profile_pic'>
							</div>
							<div class='post_main'>
							<div class='posted_by'>
								<a href='$added_by'>$first_name $last_name</a> $user_to 
							$delete_button
							$edit_button
							</div>
							<div class='post_body'>
								$body
								$imageDiv
							</div>
							<div class='post_time'>
								$time_message
							</div>
							</div>
							<hr>
							<div class='newsfeedPostOptions'>
								<span class='num_comments' onClick='javaScript:toggle$id()'>";
								if($comments_check_num == 0)
									$str.= "<i class='fas fa-comment-alt'></i> Comment";
								else if($comments_check_num == 1)
									$str.= "<i class='fas fa-comment-alt'></i> $comments_check_num Comment";
								else
									$str.= "<i class='fas fa-comment-alt'></i> $comments_check_num Comments";
								$str.="</span>  | 
								<span class='like_value' id='total_like_$id'>";
								if($total_likes === '1' ){
									$str .= "$total_likes Like";
								}
								else{
									$str .= "$total_likes Likes";
								}

								$str .= "</span>
								<span>$like_button</span>
							</div>					
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
						   <div class='comments_area'>
						     <textarea class='comment' id='comment$id' placeholder='Post a comment...'></textarea>
						     <input type='button' class='comment_btn' onclick='sendComment($id)' value='Send'>
						   </div>"
							.$this->getComments($id).
						"</div>
						<hr>";
				}//if friend end if statement

				?>
				<script>
				//Delete post functionality bootbox
					$(document).ready(function(){
							// $(".comment").emojioneArea({
							// 	pickerPosition: "bottom"
							// });
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
						// $('#edit_post<?php echo $id; ?>').on('click', function(){
						// 	//bootstrap
						// 	bootbox.prompt({
						// 	    title: "Edit post:",
						// 	    inputType: 'textarea',
						// 	    callback: function (result) {
						// 			$.post("includes/form_handlers/delete_post.php?post_id=<?php echo $id; ?>",{result: result});
						// 			//if there is a result = true
						// 			if(result)
						// 				location.reload();
						// 		}
						// 	});
						// });
					});
				</script>
				<?php
			}//end while loop

			if($count > $limit)
				$str .="<input type='hidden' class='nextPage' value='" . ($page + 1) ."'><input type='hidden' class='noMorePosts' value='false'>";//append to str
				else
				$str .="<input type='hidden' class='noMorePosts' value='true'><p class='no_posts_p'> No More Posts to show!</p>";
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
				//edit
				if($userLoggedIn == $added_by)
					$edit_button = "<a class='edit_button' id='edit_post$id'><i class='fas fa-edit'></i></a>";
				else
					$edit_button = "";

				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment -->
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

				//EMOJI
				// $body_array = preg_split("/[ ]+|\n/", $body);
				// foreach($body_array as $key => $value) {
				//      $body = implode(" ", Emojis::createEmojis($body_array, $key, $value));
				// }

				//Number of likes from posts:
				$get_likes = mysqli_query($this->con, "SELECT likes FROM posts WHERE id='$id'");
				$row = mysqli_fetch_array($get_likes);
				$total_likes = $row['likes'];

				//Check if the user already liked post
				$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$num_rows = mysqli_num_rows($check_query);

				$like_button = '';

				$liked = $num_rows > 0 ? 'liked' : 'unliked';

                //if you already liked:
				$like_button .= "<a id='like_button_$id' class='$liked' name='like_button' value='Unlike' onclick='sendLike($id)'>
    			    <i class='fas fa-heart-broken broken-heart hover'></i>
					<i class='fas fa-heart full hover'></i>
                    <i class='far fa-heart hollow active'></i>
				</a>
				";

				$str .= "<div class='status_post'>
							<div class='post_profile_pic'>
								<img class='post_profile_img' src='$profile_pic'>
							</div>
							<div class='post_main'>
								<div class='posted_by'>
									<a href='$added_by'>$first_name $last_name</a>$delete_button $edit_button
								</div>
								<div class='post_body'>
									$body
								</div>
								<div class='post_time'>
									$time_message
								</div>
							</div>
						</div>
							
						<hr>
						<div class='newsfeedPostOptions'>
							<span class='num_comments' onClick='javaScript:toggle$id()'>";
								if($comments_check_num == 0)
									$str.= "<i class='fas fa-comment-alt'></i> Comment";
								else if($comments_check_num == 1)
									$str.= "<i class='fas fa-comment-alt'></i> $comments_check_num Comment";
								else
									$str.= "<i class='fas fa-comment-alt'></i> $comments_check_num Comments";
								$str.="</span>  | 
								<span class='like_value' id='total_like_$id'>";

								if($total_likes === '1' ){
								    $str .= "$total_likes Like";
								}
								else{
								    $str .= "$total_likes Likes";
								}

								$str .= "</span>
								<span>$like_button</span>
							</div>					
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
						   <div class='comments_area'>
						     <textarea id='comment$id' placeholder='Post a comment...'></textarea>
						     <input class='comment_btn' type='button' onclick='sendComment($id)' value='Send'>
						   </div>"
							.$this->getComments($id).
						"</div>
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
				$str .="<input type='hidden' class='noMorePosts' value='true'><p class='no_posts_p'> No More Posts to show!</p>";
		}//end if statement
		echo $str;	

	}
	//send comments
	public function sendComment($post_author, $commentText, $id, $user_to) {
 
		$userLoggedIn = $this->user_obj->getUsername();
	 
		$body = strip_tags($commentText);
		$body = mysqli_real_escape_string($this->con, $body);
		$body = str_replace('\r\n', '\n', $body);
		$body = nl2br($body);
	 
		if($body === "") {
			echo "No text";
			return;
		};
		$date_added = date("Y-m-d H:i:s");
	 
		$insert_comment = mysqli_query($this->con, "INSERT INTO comments VALUES(NULL, '$body', '$userLoggedIn', '$post_author', '$date_added', 'no', '$id')");

	 	//notifications
		if($post_author !== $userLoggedIn) {
			$notification = new Notification($this->con, $userLoggedIn);
			$notification->insertNotification($id, $post_author, "comment");
		}
	 
		if($user_to !== 'none' && $user_to !== $userLoggedIn) {
			$notification = new Notification($this->con, $userLoggedIn);
			$notification->insertNotification($id, $user_to, "profile_comment");
		}
	 
		$get_commenters = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id'");
		$notified_users = array();
		while($row = mysqli_fetch_array($get_commenters)) {
	 
			if($row['posted_by'] !== $post_author && $row['posted_by'] !== $user_to 
				&& $row['posted_by'] !== $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {
	 
				$notification = new Notification($this->con, $userLoggedIn);
				$notification->insertNotification($id, $row['posted_by'], "comment_non_owner");
	 
				array_push($notified_users, $row['posted_by']);
			}
	 
		}
	 
	}
	//loading comments
	public function getComments($id, $get_only_last_comment = false) {
		$userLoggedIn = $this->user_obj->getUsername();
	 
		if($get_only_last_comment) {
	 
			$get_comments = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' ORDER BY id DESC LIMIT 1");
		}
	 
		else {
			
			$get_comments = mysqli_query($this->con, "SELECT * FROM comments WHERE post_id='$id' ORDER BY id ASC");
		}
	 
		
		$count = mysqli_num_rows($get_comments);
	 
		$commment_from_db = "";
	 
		if($count !== 0) {
	 
			while($comment = mysqli_fetch_array($get_comments)) {
	 
				$comment_body = $comment['body'];
				$posted_to = $comment['posted_to'];
				$posted_by = $comment['posted_by'];
				$date_added = $comment['date_added'];
				$removed = $comment['removed'];
				$comment_id = $comment['id'];
        
	    		$time_message = $this->getTime($date_added);
	    		//EMOJI
				$body_array = preg_split("/[ ]+|\n/", $comment_body);
				foreach($body_array as $key => $value) {
				     $comment_body = implode(" ", Emojis::createEmojis($body_array, $key, $value));
				}
	 
				$user_obj = new User($this->con, $posted_by);
	 
				$profile_pic = $user_obj->getProfilePic();
	 
				$name = $user_obj->getFirstAndLastName();
	 			
	 			//delete post if logged in user is the one posted
				if($userLoggedIn == $posted_by)
					$delete_button = "<a class='delete_button' id='comment$comment_id'><i class='fas fa-trash-alt'></i></a>";
				else
					$delete_button = "";

						
				$commment_from_db .= "
				<hr>
				<div class='comment_section'>
					<a href='$posted_by' target='_parent'><img src='$profile_pic' title='$posted_by' class='comment_profile_img'></a>
					<div class='post_main'>
						<a href='$posted_by'>$name</a>
						$delete_button
						<div class='post_body'>
							$comment_body
						</div>
						<div class='post_time'>
							$time_message
						</div> 
					</div>
					
				</div>";				
			}
			?>
				<script>
				//Delete comment functionality bootbox
					$(document).ready(function(){
						$('#comment<?php echo $comment_id; ?>').on('click', function(){
							//bootstrap
							bootbox.confirm({
								message: "Are you sure you want to delete this comment?", buttons: {
	        					confirm: {
						            label: 'Yes'
						        },

						        cancel: {
						            label: 'No'						        }
						    	},
						    	
						        callback:
						    	function
								(result){
									$.post("includes/form_handlers/delete_comment.php?comment_id=<?php echo $comment_id; ?>",{result: result});
									//if there is a result = true
									if(result)
										location.reload();

								}
							});
						});
					});
				</script>
				<?php
		}
	 
		else {
	 
			$commment_from_db = "<div class='no_comments' id='noComment$id'>No comments to show!</div>";
		}
	 
		return $commment_from_db;
	}
	public function getSinglePost($id){
		$userLoggedIn = $this->user_obj->getUsername();
		//changed notification to opened, when clicked
		$opened_query = mysqli_query($this->con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn' AND link LIKE '%=$id'");
		
		$str = ""; //string to return
		$data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND id='$id'");
		//if there is a posts:
		if(mysqli_num_rows($data_query) > 0){

			$row = mysqli_fetch_array($data_query);
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
				return;
			}

			$user_logged_obj = new User($this->con, $userLoggedIn);

			//If friend statement
			if($user_logged_obj->isFriend($added_by)){

				//add delete post button if logged in user is the one posted
				if($userLoggedIn == $added_by)
					$delete_button = "<a class='delete_button' id='post$id'><i class='fas fa-trash-alt'></i></a>";
				else
					$delete_button = "";

				//get user details and store them in variables:
				$user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
				$user_row = mysqli_fetch_array($user_details_query);
				$first_name = $user_row['first_name'];
				$last_name = $user_row['last_name'];
				$profile_pic = $user_row['profile_pic'];

				?>
				<!-- Show the comment -->
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
				//EMOJI
				// $body_array = preg_split("/[ ]+|\n/", $body);
				// foreach($body_array as $key => $value) {
				//      $body = implode(" ", Emojis::createEmojis($body_array, $key, $value));
				// }
				
				//Get number of likes for the post:
				$get_likes = mysqli_query($this->con, "SELECT likes FROM posts WHERE id='$id'");
				$row = mysqli_fetch_array($get_likes);
				$total_likes = $row['likes'];

				//Check for previous likes
				$check_query = mysqli_query($this->con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
				$num_rows = mysqli_num_rows($check_query);
				$like_button = '';

	            $liked = $num_rows > 0 ? 'liked' : 'unliked';

                //if you already liked:
				$like_button .= "<a id='like_button_$id' class='$liked' name='like_button' value='Unlike' onclick='sendLike($id)'>
    			    <i class='fas fa-heart-broken broken-heart hover'></i>
					<i class='fas fa-heart full hover'></i>
                    <i class='far fa-heart hollow active'></i>
				</a>
				";

				$str .= "<div class='status_post'>
							<div class='post_profile_pic'>
								<img class='post_profile_img' src='$profile_pic'>
							</div>
							<div class='post_main'>
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
							</div>
							<hr>
							<div class='newsfeedPostOptions'>
								<span class='num_comments' onClick='javaScript:toggle$id()'>
									Comments ($comments_check_num)
								</span>
								<span class='like_value' id='total_like_$id'>";
								if($total_likes === '1' ){
									$str .= "$total_likes Like";
								}
								else{
									$str .= "$total_likes Likes";
								}

								$str .= "</span>
								<span>$like_button</span>
							</div>					
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none;'>
						   <div class='comments_area'>
						     <textarea id='comment$id' placeholder='Post a comment...'></textarea>
						     <input type='button' class='comment_btn' onclick='sendComment($id)' value='Send'>
						   </div>"
							.$this->getComments($id).
						"</div>
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
			}//end if friend statement
				else{
					echo "<p>You cannot see this post, because you are not friends with this user.</p>";
					return;
				}

		}//end if there is post statement
		else{
			echo "<p>No post found. If you clicked a link, it may be broken.</p>";
					return;
		}
		echo $str;	

	}
}



?>