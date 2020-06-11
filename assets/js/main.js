$(document).ready(function(){
	$('#search_text_input').focus(function(){
		//if device is larger than 800px
		if(window.matchMedia( "(min-width: 800px)" ).matches) {
			$(this).animate({width: "25opx"}, 500);
		}
	});
	//submit form when click on button holder div
	$(".button_holder").on('click', function(){
		document.search_form.submit();
	});

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
			$("span").remove("#unread_notification");
			$(".fa-bell").css({"color":"#d500ff"});
		}
		else if(type == 'message'){
			pageName: "ajax_load_messages.php";
			$("span").remove("#unread_message");

		}
		var ajaxreq = $.ajax({
			url: "includes/handlers/" + pageName,
			type: "POST",
			data: "page=1&userLoggedIn=" + user,
			cashe: false,
			success: function(response) {
				$(".dropdown_data_window").html(response);
				$(".dropdown_data_window").css({"padding": "0px", "height" : "300", "border": "2px solid purple" });
				
				$("#dropdown_data_type").val(type);
			}
		});

	} else {
		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding": "0px", "height" : "0px", "border": "none"});
		$(".fa-bell").css({"color":"white"});
	}
}
function getLiveSearchUsers(value, user){
	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn: user}, function(data){
		if($(".search_results_footer_empty")[0]){
			//change the class
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}
		$(".search_results").html(data);
		$(".search_results_footer").html("<a href='search.php?q=" + value +"'>See all results</a>");

		if(data = ""){
			$(".search_results_footer").html("");
			$(".search_results_footer").toggleClass("search_results_footer_empty");
			$(".search_results_footer").toggleClass("search_results_footer");
		}

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