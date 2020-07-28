<?php 
include("../../config/config.php");
include("../../includes/classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

// reese kenney = reece[0] kenney[1]
//search for first and last name:
$names =  explode(" ", $query);
// if query contains an underscore, assume its username
if(strpos($query, '_') !== false)
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
//if there are two words, assume it's first and last names respactevely
else if(count($names) == 2)
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
//search first or last name
else
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");

if($query != ""){
	$button = "";

	while($row = mysqli_fetch_array($usersReturnedQuery)){

		$user = new User($con, $userLoggedIn);

		$mutural_friends = $user->getMutualFriends($row['username']);

		if($row['username'] == $userLoggedIn)
			$friend_count = "";
		else if($mutural_friends == 0) 
			$friend_count = "No friends in common";
		else if ($mutural_friends == 1)
			$friend_count = $mutural_friends . " friend in common";
		else
			$friend_count = $mutural_friends . " friends in common";

		if($userLoggedIn != $row['username']){
			//generate button depending on relationship status
			if($user->isFriend($row['username']))
				$button = "<input type='submit' name='" . $row['username'] . "' id='danger' value='Remove Friend'>";
			else if($user->didReceiveRequest($row['username']))
				$button = "<input type='submit' name='" . $row['username'] . "' id='warning' value='Respond to Request'>";
			else if($user->didSendRequest($row['username']))
				$button = "<input type='submit' id='default' value='Request Sent'>";
			else
				$button = "<input type='submit' name='" . $row['username'] . "' id='success' value='Add Friend'>";
			
			if(isset($_POST[$row['username']])){

				if($user->isFriend($row['username'])){
					$user->removeFriend($row['username']);
					header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				}
				else if($user->didReceiveRequest($row['username'])){
					header("Location: requests.php");
				}
				else if($user->didSendRequest($row['username'])){

				}
				else{
					$user->sendRequest($row['username']);
					header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
				}

			}
		}

		echo "<div class='liveSearchResult'>
			<a href='" . $row['username'] ."'>
			<div>
			<img class='lifeSearchProfilePic' src='" . $row['profile_pic'] ."'>
			</div>
			<div class='liveSearchText'>" . $row['first_name'] . " " . $row['last_name'] ."</a>
			<p>" . $row['username'] . "</p>
			<p class='mutural_friends'>" . $friend_count .
			"</div>
			<div class='searchPageFriend Buttons'>
				<form action='' method='POST'>
				" . $button . "
				</form>
				</div>
			
			</div>";
	}
	if(mysqli_num_rows($usersReturnedQuery) == 0)
		echo "<div class='liveSearchResult' id='no_results'>No results found!</div>";
}

 ?>