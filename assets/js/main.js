$(document).ready(function(){

	$(".search_button").on('click', function(){
		$("#search").toggleClass("vm_search");
		$("#search").toggleClass("show_search");
	});
	
	//submit form when click on button holder div
	$(".button_holder").on('click', function(){
		document.search_form.submit();
	});
	//emoji one plugIn
	$("#post_text").emojioneArea({
		pickerPosition: "bottom"
	});
	$("textarea").emojioneArea({
		pickerPosition: "bottom"
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


	var youtube = document.querySelectorAll( ".youtube" );

	for (var i = 0; i < youtube.length; i++) {

		youtube[i].innerHTML = "<img src='https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/hqdefault.jpg' async class='play-youtube-video'><div class='play-button'></div>";
    
    	youtube[i].addEventListener( "click", function() {

            this.innerHTML = '<iframe allowfullscreen frameborder="0" class="embed-responsive-item" src="https://www.youtube.com/embed/' + this.dataset.embed + '"></iframe>';
       
    	});
	};

var embeded_images = document.querySelectorAll( ".embed-images" );

	for (var i = 0; i < embeded_images.length; i++) {

		embeded_images[i].innerHTML = "<a target='_blank' title='Open image in a new window' class='external_link' href='" + embeded_images[i].dataset.embed + "'><img class='postedImages' src='" + embeded_images[i].dataset.embed + "'></a>";
	};

var embeded_link = document.querySelectorAll( ".embed-link" );

	for (var i = 0; i < embeded_link.length; i++) {

		embeded_link[i].innerHTML = "<a target='_blank' title='Open link in a new window' class='external_link' href='" + embeded_link[i].dataset.embed + "''>" + embeded_link[i].dataset.embed + "</a>";
	};
	
});

function getUserFriends(value, user) {
	$.post("includes/handlers/ajax_friend_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}

function getUsers(value, user) {
	$.post("includes/handlers/ajax_search.php", {query:value, userLoggedIn:user}, function(data) {
		$(".results").html(data);
	});
}

function showFileUpload(){
	$("#fileToUpload").toggleClass("hide");
}
const showUpdate = message => bootbox.alert(message);

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
				$(".dropdown_data_window").css({"padding": "0px", "height" : "350", "border": "2px solid purple", "border-top": "none" });
				
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
	};
	if(e.target.class != "dropdown_data_window"){

		$(".dropdown_data_window").html("");
		$(".dropdown_data_window").css({"padding": "0px", "height" : "0px", "border": "none"});
		$(".fa-bell").css({"color":""});
	};
});

function sendComment(id) {
	const commentText = $("#comment" + id).val();
	
	if(commentText === "") {
 
		bootbox.alert("Please enter some text first!");
		return;
	}
 
	const sendComment = $.post("includes/handlers/send_comment.php", {
			userLoggedIn: userLoggedIn, 
			commentText: commentText, 
			id: id
		}, 
		function(response){
 
		if(response !== "No text") {
 
			const loadComment = $.post("includes/handlers/load_comment.php", 
				{
					id: id, 
					userLoggedIn: userLoggedIn
				}, 
				function(newComment) {

				$(".emojionearea-editor").text("");
				const noComment = $("#toggleComment" + id).find("#noComment" + id);
				
				if(noComment.length !== 0) {
					noComment.remove();
				}
 
				$("#toggleComment" + id).append(newComment);
 
			});
		}
 
		else {
 
			bootbox.alert("Something went wrong. Please try again.");
		} 
 
	});
};

function updateLikes(id) {
    return $.get("like_post.php", {post_id: id}).done((num_likes) => {
        $(`#total_like_${id}`).html(`${num_likes} ${num_likes === '1' ? 'Like' : 'Likes'}`)
    });
};

function sendLike(id) {
    const $elem = $(`#like_button_${id}`);
    const isLiked = $elem.hasClass('liked');

	const sendLike = $.post("includes/handlers/ajax_update_like.php", 
		{userLoggedIn:userLoggedIn, id:id}, 
		function(response){
            updateLikes(id);
            $elem.addClass(isLiked ? 'unliked' : 'liked');
            $elem.removeClass(isLiked ? 'liked' : 'unliked');
        });

};
function deletePost(id) {
	bootbox.confirm({
		message: "Are you sure you want to delete this post?", 
		buttons: {
			confirm: {
	            label: 'Yes'
	        },

	        cancel: {
	            label: 'No'
	        }
	    },
        callback:
    	function
		(result){
			$.post("includes/form_handlers/delete_post.php?post_id="+id,{result: result});
			if(result)
				location.reload();

		}
	});
};
function makeEmbeds(){
	var youtube = document.querySelectorAll( ".youtube" );

		for (var i = 0; i < youtube.length; i++) {

			youtube[i].innerHTML = "<img src='https://img.youtube.com/vi/" + youtube[i].dataset.embed + "/hqdefault.jpg' async class='play-youtube-video'><div class='play-button'></div>";
	    
	    	youtube[i].addEventListener( "click", function() {

	            this.innerHTML = '<iframe allowfullscreen frameborder="0" class="embed-responsive-item" src="https://www.youtube.com/embed/' + this.dataset.embed + '"></iframe>';
	       
	    	});
		};

	var embeded_images = document.querySelectorAll( ".embed-images" );

		for (var i = 0; i < embeded_images.length; i++) {

			embeded_images[i].innerHTML = "<a target='_blank' title='Open image in a new window' class='external_link' href='" + embeded_images[i].dataset.embed + "'><img class='postedImages' src='" + embeded_images[i].dataset.embed + "'></a>";
		};

	var embeded_link = document.querySelectorAll( ".embed-link" );

		for (var i = 0; i < embeded_link.length; i++) {

			embeded_link[i].innerHTML = "<a target='_blank' title='Open link in a new window' class='external_link' href='" + embeded_link[i].dataset.embed + "''>" + embeded_link[i].dataset.embed + "</a>";
		};
};

function toggle(id){
	var target = $(event.target);

	if(!target.is("a")){
		var element = document.getElementById("toggleComment" + id);
		if(element.style.display == "block")
			element.style.display = "none";
		else
			element.style.display = "block";

		$("textarea").emojioneArea({
		pickerPosition: "bottom"
		});
		makeEmbeds();
	}
};

function deleteComment(id) {
	bootbox.confirm({
		message: 'Are you sure you want to delete this comment?', 
		buttons: {
			confirm: {
	            label: 'Yes'
	        },

	        cancel: {
	            label: 'No'						        
	        }
	    },
    	
        callback:
    	function
		(result){
			$.post("includes/form_handlers/delete_comment.php?comment_id="+id,{result: result});
			if(result)
				location.reload();

		}
	});
};