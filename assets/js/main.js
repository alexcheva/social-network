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
function getDropdownData(user, type){
	//might need to delete type  all together later
	if($(".dropdown_data_window").css("height") == "0px"){
		var pageName;

		if(type == 'notification') {
			pageName = "ajax_load_notifications.php";
		}
		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1$userLoggedIn=" + user,
			cashe: false,
			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding": "0px", "height" : "280px"});
				$("#dropdown_data_type").val(type);
			}
		});

	} else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding": "0px", "height" : "0px"});
	}
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