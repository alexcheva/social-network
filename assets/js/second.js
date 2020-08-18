//tabs functionality
$("#about").on('click', function(){
	$("#newsfeed").removeClass("active_tab");
	$("#about").addClass("active_tab");
	$("#newsfeed_div").removeClass("active").addClass("fade");
	$("#edit_about").removeClass("active").addClass("fade");
	$("#about_div").removeClass("fade").addClass("active");
	
});
$('#newsfeed').on('click', function(){
	$("#about").removeClass("active_tab");
	$("#newsfeed").addClass("active_tab");
	$("#edit_about").removeClass("active").addClass("fade");
	$("#about_div").removeClass("active").addClass("fade");
	$("#newsfeed_div").removeClass("fade").addClass("active");
	$(".message").html("");
});

$('.edit_button').on('click', function(){
	$("#about_div").removeClass("active").addClass("fade");
	$("#edit_about").removeClass("fade").addClass("active");
});
$('.close_edit').on('click', function(){
	$("#edit_about").removeClass("active").addClass("fade");
	$("#about_div").removeClass("fade").addClass("active");
	$(".message").html("");
});

if($(".message").length){
	$("#newsfeed").removeClass("active_tab");
	$("#about").addClass("active_tab");
	$("#newsfeed_div").removeClass("active").addClass("fade");
	$("#about_div").removeClass("fade").addClass("active");
};

if($(".errorMessage").length){
	$("#about_div").removeClass("active").addClass("fade");
	$("#edit_about").removeClass("fade").addClass("active");
}