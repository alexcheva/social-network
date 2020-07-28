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

		echo "<div class='liveSearchResult'>
			<a href='" . $row['username'] ."'>
			<div>
			<img class='lifeSearchProfilePic' src='" . $row['profile_pic'] ."'>
			</div>
			<div class='liveSearchText'>" . $row['first_name'] . " " . $row['last_name'] ."</a>
			<p>" . $row['username'] . "</p>
			<p class='mutural_friends'>" . $friend_count .
			"</div>
			
			
			</div>";
	}
	if(mysqli_num_rows($usersReturnedQuery) == 0)
		echo "<div class='liveSearchResult' id='no_results'>No results found!</div>";
}

 ?>