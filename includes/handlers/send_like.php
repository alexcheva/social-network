<?php
 
require_once("../../config/config.php");
 
$userLoggedIn = $_POST['userLoggedIn'];
$id = $_POST['id'];

$is_liked = mysqli_query($con, "SELECT * from likes WHERE username='$userLoggedIn' AND post_id='$id'");
$num_rows = mysqli_num_rows($is_liked);

if ($num_rows > 0) {
    $delete_like = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$id'");
} else {
    $insert_like = mysqli_query($con, "INSERT INTO likes VALUES(NULL, '$userLoggedIn', '$id')");
}

?>