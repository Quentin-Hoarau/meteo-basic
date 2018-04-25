$(document).ready(function(){
    $('i.fa-arrow-up').each(function(){
        var deg = $(this).data('rotate') || 0;
        var rotate = 'rotate('+ deg +'deg)';

        $(this).css({
            '-webkit-transform': rotate,
            '-moz-transform': rotate,
            '-o-transform': rotate,
            '-ms-transform': rotate,
            'transform': rotate
        });
    });
});

