<?php
	include("includes/header.php");
?>
<div class="main_column column" id="main_colum">
	<h4>404: Page not found.</h4>
	<img style='width: 175px; margin-bottom: 10px;' src='assets/images/icons/why.png'>
	
	<?php echo "<p>".$_SERVER['REQUEST_URI']. " does not exist, sorry.</p>"; ?> 
	<p>If you clicked a link, it might be broken. You should email admin and let them know there is a broken link on the site.</p>
	<p>If you got here by typing randomly in the address bar, stop doing that. You're filling my error logs with unnecessary junk. :)</p>
	<p><a href="index.php">Go back to the main page.</a></p>
</div>