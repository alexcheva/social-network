$(document).ready(function(){

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
	$("textarea").emojioneArea({
		pickerPosition: "bottom"
	});
});
