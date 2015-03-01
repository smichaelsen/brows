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
    $('.media-type-mp4').each(function () {
        var container = $(this);
        var video = container.find('video');
        var naturalWidth = video.width();
        var naturalHeight = video.height();
        if (naturalWidth < naturalHeight) {
            video.css('width', (naturalWidth / naturalHeight) * naturalWidth + 'px');
        }
        if (naturalWidth > naturalHeight) {
            container.css('height', naturalWidth + 'px');
            video.css('padding-top', (naturalWidth-naturalHeight) / 2 + 'px');
        }
    });
});
