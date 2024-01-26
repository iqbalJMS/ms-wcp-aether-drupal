$(document).ready(function () {
    var content = $('#home_subcompany');
    var step = 300; // Change this to the number of pixels you want to scroll

    $('#home_subcompany__slide-left').click(function () {
        content.animate({
            scrollLeft: "-=" + step
        }, "slow");
    });

    $('#home_subcompany__slide-right').click(function () {
        content.animate({
            scrollLeft: "+=" + step
        }, "slow");
    });
});