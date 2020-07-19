<?php 
require '../../config/config.php';

if(isset($_GET['message_id']))
	$message_id = $_GET['message_id'];

if(isset($_POST['result'])) {
	
		// $query_image = mysqli_query($con, "SELECT image FROM messages WHERE id='$message_id'");
		// $row = mysqli_fetch_array($query_image);
		// $post_image_src = $row['image'];
		// $image_src = "../../" .$post_image_src;
		// unlink($image_src);

	if($_POST['result'] == 'true'){

		$delete_post = mysqli_query($con, "DELETE FROM messages WHERE id='$message_id'");

	}
}

 ?>