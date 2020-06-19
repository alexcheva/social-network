<?php
ob_start(); //turns in output buffering
session_start();

$timezone = date_default_timezone_set("Europe/Moscow");

$con = mysqli_connect("localhost", "root", "", "social");
$con->query("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");


if(mysqli_connect_errno()){
	echo "Failed to connect:" . mysqli_connect_errno();

}

?>