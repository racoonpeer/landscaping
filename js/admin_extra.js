$(function() {
    
    /*
     * sortable lists
     */
    $('.sortable').sortable({
        placeholder: "ui-state-highlight",
        cancel: ".ui-state-disabled"
    }).disableSelection();
    
    // first example
    $(".category_tree").treeview({
            persist: "location",
            collapsed: true,
            unique: false
    });
    
    Tabs.setUp();
    
    $('.checkboxes').change(function() {
        if($('.checkboxes:checked').length)
            $('.dropDown').show();
        else 
            $('.dropDown').hide();
    });
    
    $('.dropDown').click(function(){
        if( $(this).hasClass('simple') || $('.checkboxes:checked').length){
            var ul = $(this).children('ul');
            
            $(ul).toggleClass('open');

            $(document).click(function(event) {
                 if(!$(event.target).closest(".dropDown").length && $(ul).hasClass('open'))
                     $(ul).toggleClass('open');
                event.stopPropagation();
            });
        } 
    });

   /* $('.dropDown').hover(function(){
        var ul = $(this).children('ul');
        $(ul).toggleClass('open');
    });*/
    
    var rbH = $('#right_block').innerHeight();
    $('#left_block').css('min-height', rbH + 'px');
    
});