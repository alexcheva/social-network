<?php
 
require_once("../../config/config.php");
 
$userLoggedIn = $_POST['userLoggedIn'];
$id = $_POST['id'];
 
$insert_like = mysqli_query($con, "INSERT INTO likes VALUES(NULL, '$userLoggedIn', '$id')");
?>