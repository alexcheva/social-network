function sendComment(id) {
 	const userLoggedIn = '<?php echo $userLoggedIn; ?>';
	const commentText = $("#comment" + id).val();
	
	if(commentText === "") {
 
		alert("Please enter some text first");
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
 
				$("#comment" + id).val("");
				const noComment = $("#toggleComment" + id).find("#noComment" + id);
				
				if(noComment.length !== 0) {
					noComment.remove();
				}
 
				$("#toggleComment" + id).append(newComment);
 
			});
		}
 
		else {
 
			alert("Something went wrong. Please try again");
		} 
 
	});
};