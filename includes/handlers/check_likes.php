<?php
 
include '../../config/config.php';
 
$post_id = $_POST['like'];
 
$query_likes = mysqli_query($con, "SELECT username FROM likes WHERE post_id='$post_id' ORDER BY id DESC");
 
$num = mysqli_num_rows($query_likes);
 
if($num > 0) {
 
	while($row = mysqli_fetch_array($query_likes)) {
 
		$username = $row['username'];
 
		$query_users = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");
 
		$data = mysqli_fetch_array($query_users);
 
 
		echo "<span>
		<a href='" . $data['username'] . "' class='liked_name'>
		<img src='" . $data['profile_pic'] . "' class='liked_img'>" . $data['first_name'] . " " . $data['last_name'] . "</a></span>"; 
 
	}
 
}
?>
