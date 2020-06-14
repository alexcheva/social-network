<?php 
	include("includes/header.php");
?>

<div class="main_colum column">
	<h4>Close Account</h4>
	<p>Are you sure you want to close your account?</p>
	<p>Closing your account will hide your profile and all your activity from other users.</p>
	<p>You can re-open your account at any time by simply logging in.</p>

	<form action="close_account.php" method="POST">
		<input type="submit" name="close_account" id="danger" value="Yes! Close it.">
		<input type="submit" name="cancel" id="default" value="No. Leave it.">
	</form>

</div>