<?php 
include("includes/header.php");

if(isset($_GET['q'])){
	$query = $_GET['q'];

} else{
	$query = "";
}
if(isset($_GET['type'])){
	$type = $_GET['type'];

} else{
	$type = "name";
}

?>
<div class="main_column column" id="main_colum">
	<?php 

	if($query == "")
		echo "You must enter something in the search bar.";
	else {
		if($type == "username")
			$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no'");
		else{
			$names =  explode(" ", $query);

			if(count($names) == 3)
			$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
			//search first or last name
			else if(count($names) == 2)
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
			else
				$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
		
		}
		if(mysqli_num_rows($usersReturnedQuery) == 0)
		echo "<p>We can't find anyone with a" . $type . " like: " . $query ."</p>";
	else
		echo "<p>" . mysqli_num_rows($usersReturnedQuery) . " results found:<p>";

	echo "<p id='grey'>Try searching for: <p>";
	echo "<a href='search.php?q=" . $query . "&type=name'>Names</a>/<a href='search.php?q=" . $query . "&type=username'>Usernames</a><hr>";
	while($row= mysqli_fetch_array($usersReturnedQuery)) {
		$user_obj = new User($con, $user['username']);
		$button = "";
		$mutual_friends = "";
		$friend_count = "";

		if($user['username'] != $row['username']){
			//generate button depending on relationship status
			if($user_obj->isFriend($row['username']))
				$button = "<input type='submit' name='" . $row['username'] . "' class='danger' value='Remove Friend'>";
			else if($user_obj->didRecieveRequest($row['username']))
				$button = "<input type='submit' name='" . $row['username'] . "' class='warning' value='Respond to Request'>";
			else if($user_obj->didSendRequest($row['username']))
				$button = "<input class='default' value='Request Sent'>";
			else
				$button = "<input type='submit' name='" . $row['username'] . "' class='success' value='Add Friend'>";

			$mutural_friends = $user->getMutualFriends($row['username']);

			else if($mutural_friends == 0) 
				$friend_count = "No friends in common";
			else if ($mutural_friends == 1)
				$friend_count = $mutural_friends . " friend in common";
			else
				$friend_count = $mutural_friends . " friends in common";

			//button forms

		}

	}
		
	
	}

	 ?>
</div>