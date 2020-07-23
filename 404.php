<?php
	include("includes/header.php");
?>
<div class="main_column column" id="main_colum">
	<h2>404: Page not found.</h2>
	<img class='why' src='assets/images/icons/why.png'>
	
	<?php echo "<h4>".$_SERVER['REQUEST_URI']. " does not exist, sorry.</h4>"; ?> 
	<p>If you clicked a link, it might be broken. You should email admin and let them know there is a broken link on the site.</p>
	<p>If you got here by typing randomly in the address bar, stop doing that. You're filling my error logs with unnecessary junk. :)</p>
	<p><a href="index.php">Go back to the main page.</a></p>
</div>