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

function getUsers(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}


function updateLikes(id) {
    return $.get("like_post.php", {post_id: id}).done((num_likes) => {
        $(`#total_like_${id}`).html(`${num_likes} ${num_likes === '1' ? 'Like' : 'Likes'}`)
    })
}

function sendLike(id) {
    const current_label = $(`#like_button_${id}`).val()
	const sendLike = $.post("includes/handlers/send_like.php", 
		{userLoggedIn:userLoggedIn, id:id}, 
		function(response){
            updateLikes(id);
            $(`#like_button_${id}`).val(current_label == 'Like' ? 'Unlike' : 'Like'); 
	});
}