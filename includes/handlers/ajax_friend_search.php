<?php 
	include("../../config/config.php");
	include("../classes/User.php");
	//come from post request
	$query = $_POST['query'];
	$userLoggedIn = $_POST['userLoggedIn'];

	$names = explode(" ", $query);
	//string position al% = alex... and such
	//if there is an undescore assume it's a username
	if(strpos($query, "_") !== false){
		$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
	}
	//if looking for full name
	else if (count($names) == 2){
		$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[1]%') AND user_closed='no' LIMIT 8");

	}
	else {
		$usersReturned = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '%$names[0]%' AND last_name LIKE '%$names[0]%') AND user_closed='no' LIMIT 8");
	}
	if($query != "") {
		while($row= mysqli_fetch_array($usersReturned)){

			$user = new User($con, $userLoggedIn);

			if($row['username'] != $userLoggedIn) {
				$mutural_friends = $user->getMuturalFriends($row['username']) ." friends in common";
			} else{
				$mutural_friends = "";
			}
		}
	}

 ?>