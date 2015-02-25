$(document).ready(function() {
    var body = $('body');
    var scrollTimer;
    $(document).on('scroll', function() {
        clearTimeout(scrollTimer);
        body.addClass('disable-hover');
        scrollTimer = setTimeout(function(){
            body.removeClass('disable-hover')
        }, 300);
    });
    $('.swipebox').swipebox();
});