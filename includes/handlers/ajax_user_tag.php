<?php
 
    include("../../config/config.php");
    include("../classes/User.php");
 
    $userLoggedIn = $_POST['userLoggedIn'];
 
    $userLoggedIn = new User($con, $userLoggedIn);
 
    $result = array();
    $result = $userLoggedIn->getFriendArray();
 
    $friend_array_string = trim($result, ",");
 
    if ($friend_array_string !== "") {
 
    $no_commas = explode(",", $friend_array_string);
 
      foreach ($no_commas as $value) {
 
          $friend = mysqli_query($con, "SELECT first_name, last_name, username, profile_pic FROM users WHERE username='$value'");
 
          $row = mysqli_fetch_assoc($friend);
 
           echo "<div class='displayTag'>
 
                        <a href=" . $row['username'] . ">         
 
                            <div>
                                <img src='" . $row['profile_pic'] . "'>
                            </div>  
 
                            <div>
 
                                " . $row['first_name'] . " " . $row['last_name'] . "
                                <p style='margin: 0;'></p>
                                <p id='grey'></p>
 
                            </div>
 
                            </a>    
 
                    </div>";
 
      }
 
    }
 
    else {
 
    echo "<br><p id='ynf'>You don't have friends yet. Please add someone to friends first.</p>";
    }
