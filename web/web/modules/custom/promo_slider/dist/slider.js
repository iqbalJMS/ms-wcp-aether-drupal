$(document).ready(function () {
    var content = $('#promo_slider');
    var step = 300; // Change this to the number of pixels you want to scroll

    $('#promo_slider__slide-left').click(function () {
        content.animate({
            scrollLeft: "-=" + step
        }, "slow");
    });

    $('#promo_slider__slide-right').click(function () {
        content.animate({
            scrollLeft: "+=" + step
        }, "slow");
    });
});