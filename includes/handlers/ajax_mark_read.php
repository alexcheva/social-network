<?php
 
include '../../config/config.php';
 
$userLoggedIn = $_SESSION['username'];
 
$read = mysqli_query($con, "UPDATE notifications SET opened='yes' WHERE user_to='$userLoggedIn'");
?>