<?php
ob_start(); //turns in output buffering
session_start();

$timezone = date_default_timezone_set("Europe/Moscow");

$con = mysqli_connect("127.0.0.1", "root", "", "social");

if(mysqli_connect_errno()){
	echo "Failed to connect:" . mysqli_connect_errno();

}

?>