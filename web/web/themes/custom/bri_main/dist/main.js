// check if there is jquery
$(document).ready(function () {
    $(window).scroll(function () {
        var navbar = $('.header-main');
        if ($(window).scrollTop() > 80) {
            navbar.addClass('bgr-white');
        } else {
            navbar.removeClass('bgr-white');
        }
    });
});
