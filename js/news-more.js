/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(function(){
    var Gallery = $(".gallary"),
        Thumbs = Gallery.children(".img-slider").find("a"),
        Screen = Gallery.children(".big-img");
    Thumbs.on("click", function (e) {
        var self = $(this),
            type = self.data("type"),
            src  = self[0].href,
            wrap = "";
        switch (type) {
            case "image":
                wrap += "<img src=\"" + src + "\" alt=\"\">";
                break;
            case "movie":
                wrap += "<iframe width=\"625\" height=\"385\" src=\"" + src + "\" frameborder=\"0\" allowfullscreen></iframe>";
                break;
        }
        self.closest("li").siblings("li").removeClass("active");
        self.closest("li").addClass("active");
        if (wrap.length) Screen.html(wrap);
        $('.gallary').on('click', 'li a', '.big-img a', function(e){
            e.preventDefault();
        });
    });
});