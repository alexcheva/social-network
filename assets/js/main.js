$(document).ready(function(){
	//button for profile post
	$('#submit_profile_post').click(function(){
		$.ajax({
			type: "POST",
			url: "includes/handlers/ajax_submit_profile_post.php",
			data: $('form.profile_post').serialize(),
			//what it does on call back
			success: function(msg){
				$("#post_form").modal('hide');
				location.reload();

			},
			error: function(){
				alert('Failure');
			}
		});
	});

});

function getUser(value, user){
	$.post("includes/handlers/ajax_friend_search.php",{
		query: value,
		userLogggedIn: user
	}, function(data){
		$(".results").html(data);
	});
}

function sendLike(id) {
 
	const sendLike = $.post("includes/handlers/send_like.php", 
		{userLoggedIn:userLoggedIn, id:id}, 
		function(response){
 			alert("sucess");
 
	});
}