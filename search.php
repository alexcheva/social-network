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
		echo "<p>No results found!</p>";
		
	
	}

	 ?>
</div>