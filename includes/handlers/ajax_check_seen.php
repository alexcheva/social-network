<?php
 
include '../../config/config.php';
 
$userLoggedIn = $_POST['me'];
$user_to = $_POST['friend'];
 
$get_last_messages_query = mysqli_query($con, "SELECT id FROM messages WHERE (user_to='$userLoggedIn' AND user_from='$user_to') OR (user_from='$userLoggedIn' AND user_to='$user_to') ORDER BY id DESC LIMIT 1");
 
$my_last_message = mysqli_query($con, "SELECT id, opened FROM messages WHERE user_from='$userLoggedIn' AND user_to='$user_to' ORDER BY id DESC LIMIT 1");
 
$friend_last_message = mysqli_query($con, "SELECT id FROM messages WHERE user_to='$userLoggedIn' AND user_from='$user_to' ORDER BY id DESC LIMIT 1");
 
$row1 = mysqli_fetch_array($my_last_message);
$row2 = mysqli_fetch_array($friend_last_message);
 
$row3 = mysqli_fetch_array($get_last_messages_query);
 
 
if($row1['id'] > $row2['id'] && $row1['opened'] === "yes" && $row1['id'] === $row3['id']) 
	echo "<div class='checkSeen'>Seen</div>";
  
  ?>
