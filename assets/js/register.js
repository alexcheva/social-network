$(document).ready(function(){

//on click signup, hide login div
$("#signup").click(function(){
	$("#first").slideUp("slow", function(){
		$("#second").slideDown("slow");
	});
});

//on click login, hide sign up page
$(".login").click(function(){
	$("#second").slideUp("slow", function(){
		$("#first").slideDown("slow");
	});
});
});