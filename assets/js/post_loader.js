$(function(){
    var inProgress = false;
 
    loadPosts(); //Load first posts
 
    $(window).scroll(function() {
        var bottomElement = $(".status_post").last();
        var noMorePosts = $('.posts_area').find('.noMorePosts').val();
 
        // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
        if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
            loadPosts();
        }
    });
 
    function loadPosts() {
        if(inProgress) { //If it is already in the process of loading some posts, just return
            return;
        }
        
        inProgress = true;
        $('#loading').show();
 
        var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
 
        $.ajax({
            url: "includes/handlers/ajax_load_posts.php",
            type: "POST",
            data: "page=" + page + "&userLoggedIn=" + VM.userLoggedIn,
            cache: false,
 
            success: function(response) {
                $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
                $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
                $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 
 
                $('#loading').hide();
                $(".posts_area").append(response);
                inProgress = false;
            }
        });
    }
 
    //Check if the element is in view
    function isElementInView (el) {
        var rect = el.getBoundingClientRect();
 
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
            rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
        );
    }

    VM.submitComment = function(event){
        console.log("submit comment called")
        event.preventDefault()
        event.stopPropagation()
        var $target = $(event.target)
        var $parent = $(event.target).parent()
        var action = $parent.attr('action')
        $.post(action, $parent.serialize()).done(function(response) {
            $parent.parent().html(response)
        })
    }
})
