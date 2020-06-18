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
	//emoji one plugIn
	// $("#post_text").emojioneArea({
	// 	pickerPosition: "bottom"
	// });
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
			$(".search_results_footer_empty").toggleClass("search_results_footer");
			$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
		}
		$(".search_results").html(data);
		$(".search_results_footer").html("<a href='search.php?q=" + value +"'>See all results</a>");
		$(".search_results").css({ "border": "2px solid purple","border-top": "none" });
		$(".search_results_footer").css({ "border": "2px solid purple","border-top": "none" });

		if(data == ""){
			$(".search_results_footer").html("");
			$(".search_results_footer").toggleClass("search_results_footer_empty");
			$(".search_results_footer").toggleClass("search_results_footer");
			$(".search_results").css({"border": "none" });
			$(".search_results_footer").css({"border": "none" });
			$(".search_results_footer_empty").css({"border": "none" });
		}
		if($("#no_results").length){
			$(".search_results_footer").html("");
			$(".search_results_footer").toggleClass("search_results_footer_empty");
			$(".search_results_footer").toggleClass("search_results_footer");
			$(".search_results_footer").css({"border": "none" });
			$(".search_results_footer_empty").css({"border": "none" });

		}

	});

}
$(document).click(function(e){
	if(e.target.class != "search_result" && e.target.id != "search_text_input"){

		$(".search_results").html("");
		$(".search_results").css({"border": "none" });
		$(".search_results_footer").html("");
		$(".search_results_footer").toggleClass("search_results_footer_empty");
		$(".search_results_footer").toggleClass("search_results_footer");
		$(".search_results_footer_empty").css({"border": "none" });
	}
	if(e.target.class != "dropdown_data_window"){

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding": "0px", "height" : "0px", "border": "none"});
		$(".fa-bell").css({"color":""});
	}
});

function updateLikes(id) {
    return $.get("like_post.php", {post_id: id}).done((num_likes) => {
    	//when got number of likes, update text: like or likes
        $(`#total_like_${id}`).html(`${num_likes} ${num_likes === '1' ? 'Like' : 'Likes'}`)
    })
}

function sendLike(id) {
    const current_label = $(`#like_button_${id}`).val()
    // const current_class = $(`#like_button_${id}`).val()
	const sendLike = $.post("includes/handlers/send_like.php", 
		{userLoggedIn:userLoggedIn, id:id}, 
		function(response){
            updateLikes(id);
            //if value = like -> on hover change to broken, if unlike change to solid heart.
           // $(`#like_button_${id}`).val(current_label == 'Like' ? 'Unlike' : 'Like'); 

            if($(`#like_button_${id}`).val() == 'Unlike')
            {
            	$("#click_unlike").css({"display": "inline"});
            	$(".hollow").removeClass("active").addClass("hover");
            	$(".full").removeClass("hover").addClass("active");
            }
            // }else{
            // 	$(`#like_button_${id}`).removeClass("like").addClass("unlike");
            // }


	});

	
	//if heart is solid:
	// if($(`#like_${id}`).hasClass( "fas fa-heart" )){
	// 	//make broken heart:
	// 	$(`#like_${id}`).removeClass("fas fa-heart").addClass("fa-heart-broken");
	// }
// remove left hover listener
  // left.off('hover');
  // right.hover(function() {
  //   right.css("margin-left", "10%");
  //   left.css("margin-left", "-90%");
  // }, function(){
  //   right.css("margin-left", "50%");
  //   left.css("margin-left", "-50%");
  // });
// }


// 	{
//             	$(`#like_button_${id}`).val(current_label == 'Unlike');
//             	$(`#like_${id}`).removeClass("fas fa-heart").addClass("fas fa-heart");
//             }else{
//             	$(`#like_button_${id}`).val(current_label == 'Like');
//             	$(`#like_${id}`).removeClass("fas fa-heart").addClass("far fa-heart");
//             }

}
//Vladimir emoji code
$(function(){
 
    $(".emojis img:not(.toggle_emojis)").on("click", function(){
 
      const extension = $(this).attr("src").indexOf(".png");
      const emojisDash = $(this).attr("src").indexOf("emojis/");
      const num = $(this).attr("src").substring(emojisDash + 7, extension);
      const emoji = `:s${num}:`;
 
      const txt = $("#post_text");
      let caretPos = txt[0].selectionStart;
      const textAreaTxt = txt.val();
 
      txt.val(textAreaTxt.substring(0, caretPos) + emoji + textAreaTxt.substring(caretPos));
 
      if(caretPos === 0)
        caretPos = textAreaTxt.substring(-1);
    });
 
    $(".toggle_emojis").on("click", function(){
 
      if($(".emojis").css("height") !== "0px"){
        $(".emojis").css("max-height", "0px");
        $(".emojis .toggle_emojis").attr("title", "Show emojis");
      }
      if($(".emojis").css("height") === "0px"){
        $(".emojis").css("max-height", "40px");
        $(".emojis .toggle_emojis").attr("title", "Close emojis");
      }
 
    });
 
});