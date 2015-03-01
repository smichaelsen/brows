$(document).ready(function () {
    var body = $('body');

    // 60 fps scrolling
    var scrollTimer;
    $(document).on('scroll', function () {
        clearTimeout(scrollTimer);
        body.addClass('disable-hover');
        scrollTimer = setTimeout(function () {
            body.removeClass('disable-hover')
        }, 300);
    });

    // Swipebox
    $('.swipebox').swipebox();

    // Fit videos into square containers
    $('video').on('loadeddata', function () {
        var video = $(this);
        var naturalHeight = video.height();
        var naturalWidth = video.width();
        if (naturalWidth < naturalHeight) {
            console.log('portrait video. Adjust width of video element');
            video.css('width', (naturalWidth / naturalHeight) * naturalWidth + 'px');
        }
        if (naturalWidth > naturalHeight) {
            console.log('landscape video. Adjust padding-top of video element and fix height of container');
            video.parent().css('height', naturalWidth + 'px');
            video.css('padding-top', (naturalWidth-naturalHeight) / 2 + 'px');
        }
        if (naturalWidth == naturalHeight) {
            console.log('square video');
        }
    });
});
