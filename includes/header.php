<?php
	require 'config/config.php';
	include('includes/classes/User.php');
	include('includes/classes/Post.php');
	include('includes/classes/Message.php');
	include('includes/classes/Notification.php');

	if (isset($_SESSION['username'])){
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	}
	else {
		header("Location: register.php");
	}
?>

<html>
	<head>
		<title>Verni Moj 2007 &#128148;</title>
			<script src="assets/js/jquery-2.2.4.min.js"></script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
			<script src="assets/js/bootbox.min.js"></script>
			<script src="assets/js/main.js"></script>
			<script src="assets/js/all.min.js"></script>
			<script src="assets/js/jquery.jcrop.js"></script>
			<script src="assets/js/jcrop_bits.js"></script>
			<script>
    var userLoggedIn = "<?php echo $userLoggedIn; ?>";
    </script>

			<link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.6.3/css/font-awesome.min.css'>
			<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
			<link rel="stylesheet" href="assets/css/style.css">
			<link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />

	</head>
	<body>
		<div class="top_bar">
			<div class="logo">
				<h1><a href="index.php">Verni Moj 2007 &#128148;</a></h1>
			</div>

			<nav>
				<?php 
				//Unread messages 
				$messages = new Message($con, $userLoggedIn);
				$num_messages = $messages->getUnreadNumber();
				//Unread notifications 
				$notifications = new Notification($con, $userLoggedIn);
				$num_notifications = $notifications->getUnreadNumber();
				//Unread friend_requests 
				$user_obj = new User($con, $userLoggedIn);
				$num_requests = $user_obj->getNumberFriendRequests();
				 ?>
				<a href="<?php echo $userLoggedIn ?>">
					<?php echo $user['first_name']?>
				</a>
				<a href="index.php"><i class="fas fa-home"></i></a>
				<a href="<?php echo $userLoggedIn ?>"><i class="fas fa-user-circle"></i></a>
				<a href="requests.php"><i class="fas fa-user-friends"></i>
					<?php if($num_requests > 0){
							echo '<span class="notification_badge" id="unread_request"></span>';
						} ?>
				</a>
				<a href="messages.php"><i class="fas fa-envelope"></i>
					<?php if($num_messages > 0){
						echo '<span class="notification_badge" id="unread_message"></span>';
					} ?>
					</a>
				<a href="javaScript:void(0)" onClick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
					<i class="fas fa-bell"></i></a>
					<?php if($num_notifications > 0){
						echo '<span class="notification_badge" id="unread_notification"></span>';
					} ?>
				<a href="#"><i class="fas fa-user-cog"></i></a>
				<a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt"></i></a>

				<!-- <a href="#"><i class="fas fa-house-user"></i></a> -->
			</nav>
			<div class="dropdown_data_window"></div>
			<input type="hidden" id="dropdown_data_type" value="">
		</div>
		<div class="wrapper">


