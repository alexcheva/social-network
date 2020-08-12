<?php
	require 'config/config.php';
	require 'includes/form_handlers/register_handler.php';
	require 'includes/form_handlers/login_handler.php';
?>
<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Welcome to VM2007!</title>
		<link rel="stylesheet" href="assets/css/register_style.css">
		<script src="assets/js/jquery-2.2.4.min.js"></script>
		<script src="assets/js/register.js"></script>
	</head>
	<body>

<?php
if(isset($_POST['register_button'])){
	echo '<script>
	$(document).ready(function(){
		$("#first").hide();
		$("#second").show();
		});
	</script>';
}
?>		<div class="wrapper">
			<div class="login-box">
				
				<div class="login_header">
					<h1>Verni moj 2007	&#128148;</h1>
					<h3>Login or Sign in below:</h3>
				</div>
				<div id="first">
					<form action="register.php" method="POST">
						<?php if(isset($_GET['reg'])) echo "<span class='success'>You are all set! Go ahead and login!</span>"; ?>
						<?php if(in_array("<span class='error'>Email or password is incorrect.</span>", $error_array)) echo "<span class='error'>Email or password is incorrect.</span>";?>
						<input type="email" name="log_email" placeholder="Email Address" value="<?php if(isset($_SESSION['log_email'])){ echo $_SESSION['log_email']; } ?>" required><br>
						<input type="password" name="log_password" placeholder="Password"><br>
						<input type="submit" name="login_button" value="Login">
						<p><a href="#" id="signup">Need an account? Sign up here!</a></p>
					</form>
				</div>
				<div id="second">
					<form action="register.php" method="POST">
						<?php if(in_array("<span class='error'>Your First name must be between 2 and 25 characters.</span>", $error_array)) echo "<span class='error'>Your First name must be between 2 and 25 characters.</span>";?>
						<input type="text" name="req_fname" placeholder="First Name" value="<?php if(isset($_SESSION['req_fname'])) echo $_SESSION['req_fname']; ?>"
						required>
						<br>
						<?php if(in_array("<span class='error'>Your Last name must be between 2 and 50 characters.</span>", $error_array)) echo "<span class='error'>Your Last name must be between 2 and 50 characters.</span>";?>
						<input type="text" name="req_lname" placeholder="Last Name" value="<?php if(isset($_SESSION['req_lname'])){ echo $_SESSION['req_lname']; } ?>" required>
						<br>
						<?php if(in_array("<span class='error'>Username can only contain english letters, numbers and underscores. And it must be between 5 and 25 characters.</span>", $error_array)) echo "<span class='error'>Username can only contain english letters, numbers and underscores. And it must be between 5 and 25 characters.</span>";?>
						<?php if(in_array("<span class='error'>This username is already in use.</span>", $error_array)) echo "<span class='error'>This username is already in use.</span>";?>

						<input type="text" name="req_username" placeholder="Username" value="<?php if(isset($_SESSION['req_username'])){ echo $_SESSION['req_username']; } ?>" required>
			<br>
			<?php if(in_array("<span class='error'>This email is already in use.</span>", $error_array)) echo "<span class='error'>This email is already in use.</span>";?>
			<?php if(in_array("<span class='error'>Invalid email format.</span>", $error_array)) echo "<span class='error'>Invalid email format.</span>";?>
			<?php if(in_array("<span class='error'>Emails don't match.</span>", $error_array)) echo "<span class='error'>Emails don't match.</span>";?>
			<input type="email" name="req_email" placeholder="Email" value="<?php if(isset($_SESSION['req_email'])){ echo $_SESSION['req_email']; } ?>" required>
			<br><input type="email" name="req_email2" placeholder="Confirm Email" value="<?php if(isset($_SESSION['req_email2'])){
			echo $_SESSION['req_email2']; } ?>" required><br>
			<?php if(in_array("<span class='error'>Your password can only contain english characters and numbers.</span>", $error_array)) echo "<span class='error'>Your password can only contain english characters and numbers.</span>";?>
			<?php if(in_array("<span class='error'>Your password must be between 5 and 30 characters.</span>", $error_array)) echo "<span class='error'>Your password must be between 5 and 30 characters.</span>";?>
			<?php if(in_array("<span class='error'>Passwords do not match.</span>", $error_array)) echo "<span class='error'>Passwords do not match.</span>";?>
			<input type="password" name="req_password" placeholder="Password" required><br>
			<input type="password" name="req_password2" placeholder="Confirm Password" required><br>
			<?php if(in_array("<span class='success'>You're all set! Go ahead and <a href='#' class='login'>log in!</a></span>", $error_array)) echo "<span class='success'>You're all set! Go ahead and <a href='#' class='login'>log in!</a></span>";?>
			<input type="submit" name="register_button" value="Register">
			<p><a href="#" class="login">Already have an account? Login here!</a></p>
		</form>
		
		</div>
	</div>
</div>
	</body>
</html>