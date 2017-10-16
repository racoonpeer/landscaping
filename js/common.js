/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
    $(window).scroll(function(){
        if ($(this).scrollTop() > 100) {
            $('.scroll-top').fadeIn();
        } else {
            $('.scroll-top').fadeOut();
        }
    }); 
    $('.scroll-top').click(function(){
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });
    resizeContentBox();
    $(window).on("load", function(){
        resizeContentBox();
    }).on("resize", function(){
        resizeContentBox();
    });
});

function resizeContentBox() {
    var viewport = verge.viewport(),
        contentBox = $(".main-box-content"),
        boxes = contentBox.siblings(".main-box"),
        height = 0;
    if (contentBox.length) {
        height = viewport.height;
        $.map(boxes, function(box){
            height -= $(box).height();
        });
        contentBox.css({
            "min-height" : height + "px"
        });
    }
}

function empty(mixed_var) {
    return (mixed_var==="" || mixed_var===0 || mixed_var==="0" || mixed_var===null || mixed_var===false || mixed_var==='undefined' || typeof(mixed_var)==='undefined' || (is_array(mixed_var) && mixed_var.length===0));
}

function is_array(mixed_var) {
    return (mixed_var instanceof Array);
}

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key]===needle) || (!strict && haystack[key]==needle)) {
            found = true;
            break;
        }
    }
    return found;
}

function implode(glue, pieces) {
    return ((pieces instanceof Array) ? pieces.join(glue) : pieces);
}
