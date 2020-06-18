<?php 
include("../../config/config.php");
include("../classes/User.php");
include("../classes/Message.php");
require_once("../classes/Emojis.php");
//for messages:
$limit = 7;//limit the amount of messages loaded at once

$message = new Message ($con, $_REQUEST['userLoggedIn']);
echo $message->getConvosDropdown($_REQUEST, $limit);


 ?>