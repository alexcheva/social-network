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
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Verni Moj 2007 &#128148;</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css"/>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="assets/css/emojionearea.min.css"/>
		<link rel="stylesheet" href="assets/css/style.css"/>
		<link rel="stylesheet" href="http://jcrop-cdn.tapmodo.com/v0.9.12/css/jquery.Jcrop.min.css" />
		<script>
	    	const userLoggedIn = "<?php echo $userLoggedIn; ?>";
	    </script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.bundle.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/js/all.min.js"></script>
		<!-- Emoji -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/emojione/2.2.7/lib/js/emojione.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.1/emojionearea.min.js"></script>
		<script src="assets/js/main.js"></script>
		
	</head>
	<body>
		<header class="top_bar">
			<div class="header-wrapper">
				<div class="logo">
					<h1>
						<a href="index.php">
							Verni <span id="moj">Moj </span>2007 &#128148;
						</a>
					</h1>
				</div>
				<!-- SEARCH -->
				<div id="search" class="vm_search">
					<form action="search.php" method="GET" name="search_form">
						<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input">
						<div class="button_holder">
							<i class="fa fa-search"></i>
						</div>
					</form>
				</div>
				<!-- Navigation -->
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

					<a id="username" href="<?php echo $userLoggedIn ?>">
						<?php echo $user['first_name']?>
					</a>
					<a href="index.php">
						<i class="fas fa-home"></i>
					</a>
					<a href="<?php echo $userLoggedIn ?>">
						<i class="fas fa-user-circle"></i>
					</a>
					<a href="requests.php">
						<i class="fas fa-user-friends"></i>
						<?php if($num_requests > 0)
							echo '<span id="unread_request" class="notification_badge">'.$num_requests.'</span>';
						?>
						<!-- <span id="unread_request" class="notification_badge">5</span> -->
					</a>
					<a href="messages.php">
						<i class="fas fa-envelope"></i>
						<?php if($num_messages > 0)
							echo '<span id="unread_message" class="notification_badge">'.$num_messages.'</span>';
						?>
						<!-- <span id="unread_message" class="notification_badge">5</span> -->
					</a>
					<a href="javaScript:void(0)" onClick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
						<i class="fas fa-bell"></i>
						<?php if($num_notifications > 0)
							echo '<span id="unread_notification" class="notification_badge">'.$num_notifications.'</span>';
						?>
					</a>
					<a href="settings.php">
						<i class="fas fa-user-cog"></i>
					</a>
					<a class="search_button" href="javaScript:void(0);">
						<i class="fa fa-search"></i>
					</a>
					<a href="includes/handlers/logout.php">
						<i class="fas fa-sign-out-alt"></i>
					</a>
				</nav>
			</div> <!-- end header wrapper -->
		<div class="search-wrapper">
			<div class="search_dropdown">
				<div class="search_results"></div>
				<div class="search_results_footer_empty"></div>
			</div>
		</div>
		<div class="dropdown-wrapper">
		<div class="dropdown_data_window"></div>
		<input type="hidden" id="dropdown_data_type" value=""></div>
		
			
				
	</header><!-- end top bar -->

		<div class="wrapper">


