<?php 
include('includes/header.php');

 ?>
 <style>
.like:hover .hollow,
.like .full {
    display: none;
}
.like:hover .full {
    display: inline;
}
.unlike:hover .fa-heart,
.unlike .fa-heart-broken {
    display: none;
}
.unlike:hover .fa-heart-broken {
    display: inline;}

.border-heart{
  color: red;
  font-size: 21px;
}
.selected-heart{
  color: red;
  font-size: 22px;
}
 </style>
 <div class="main_column column">
 	<h4>User Closed</h4>
 	<p>This Account is closed.</p>
 	<div class="heart"><i class="far fa-heart border-heart"></i></div>

 	<br>
 	<a href="#" class="like">
    <i class="far fa-heart hollow"></i>
    <i class="fas fa-heart full"></i>
 	<br>
 	<a href="#" class="unlike">
    <i class="fas fa-heart"></i>
    <i class="fas fa-heart-broken"></i>
</a>
<br>
 	<img style='width: 175px; margin-bottom: 10px;' src='assets/images/icons/not-found.png'>
 	<p><a href="index.php">Click here to go back.</a></p>
 </div>
 <script>
$('.heart').on('click',function() {
  $(this).find("i").toggleClass("far fas selected-heart border-heart");
});
 </script>