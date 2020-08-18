<?php
 
include '../../config/config.php';
include '../classes/User.php';
include '../classes/Message.php';
 
$params = json_decode($_POST['otherData'], true);
 
$userLoggedIn = $params[0]['me'];
$body = $params[0]['body'];
$user_to = $params[0]['friend'];
 
 
		$body_array = preg_split("/\s+/", $body);
 
		foreach($body_array as $key => $value) {
 
	 		if(strpos($value, "www.youtube.com/watch?v=") === false && (strpos($value, "www") !== false || strpos($value, "https") !== false)) {
 
	 			if(strpos($value, "http://") === false && strpos($value, "https://") === false) {
 
	 				$value = "<p><a target='_blank' href='https://" . strip_tags($body_array[$key]) . "' style='color: white; font-weight: bold; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;' title='Click to go to link'>" . strip_tags($body_array[$key]) . "</a></p>";
	 				$body_array[$key] = $value;
	 			}
 
	 			else if(strpos($value, "https://") !== false) {
 
					$value = "<p><a target='_blank' href='" . strip_tags($body_array[$key]) . "' style='color: white; font-weight: bold; text-shadow: -1px 0 black, 0 1px black, 1px 0 black, 0 -1px black;' title='Click to go to link'>" . strip_tags($body_array[$key]) . "</a></p>";
					$body_array[$key] = $value;
				}
 
				$body = implode(" ", $body_array);
			}
 
		}
 
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $errorMessage = "";
 
    if($imageName != "") {
 
      $targetDir = "../../assets/images/messages/";
      $imageName = $targetDir . uniqid() . basename($imageName);
      $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);
 
    if($_FILES['fileToUpload']['size'] > 10000000) {
 
      $errorMessage = "Sorry, your file is too large!";
      $uploadOk = 0;
    }
 
    if(strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
 
      $errorMessage = "Sorry, only jpeg, jpg and png files are allowed!";
      $uploadOk = 0;
    }
 
    if($uploadOk) {
 
      if(move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
 
        //image uploaded successfully
      }
 
      else {
        //image did not upload
        $uploadOk = 0;
      }
    }
  }
 
  	if($uploadOk) { 
 
		$message_obj = new Message($con, $userLoggedIn);
 
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date, $imageName);
 
	}
 
  	else {
 
               echo "<div style='text-align: center;' class='alert alert-danger'>
                      $errorMessage
                    </div>";
  	}
    ?>
