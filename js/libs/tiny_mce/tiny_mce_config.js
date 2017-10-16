 tinyMCE.init({
    // General options
    mode        : "exact",
    elements    : "textintro,categorytext,categorydescr,description,fulldescription,ajaxfilemanager,seoText,simplePlainText",
    theme       : "advanced",
    skin    	: "o2k7",
    plugins 	: "imagemanager,filemanager,autolink,lists,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

    // Theme options
    // hidden function: styleprops, newdocument, restoredraft, insertdate,inserttime, ,preview,fullscreen,help ,|cite,abbr,acronym,del,ins,attribs|,charmap,nonbreaking,emotions,iespell,advhr,pagebreak,
    // ,|,search,replace,forecolor,backcolor,|insertlayer,moveforward,movebackward,absolute,|tablecontrols,|,template,|,print
    theme_advanced_buttons1 : "undo,redo,|,cut,copy,paste,pastetext,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,outdent,indent,blockquote,|,sub,sup,|,hr,removeformat",   
    theme_advanced_buttons2 : "cleanup,link,unlink,anchor,image,insertfile,media,|,ltr,rtl,|,visualchars,code,|,visualaid, tablecontrols, table, row_props, cell_props, delete_col, delete_row, col_after,    col_before, row_after, row_before,  split_cells, merge_cells",
theme_advanced_buttons3 : "styleselect,formatselect,fontselect,fontsizeselect",
    theme_advanced_toolbar_location 	: "top",
    theme_advanced_toolbar_align 	: "left",
    theme_advanced_statusbar_location 	: "bottom",
    theme_advanced_resizing 		: false,


    //force_br_newlines : true,

    relative_urls : false,
    remove_script_host : true,
    document_base_url : "/",
    convert_urls : true, 
    //convert_urls : false,

    // http://tinymce.moxiecode.com/wiki.php/Configuration:valid_elements
//    valid_elements: "",
//    extended_valid_elements: "",
//    invalid_elements: "",

    // Example content CSS (should be your site CSS)
    content_css : "/js/libs/tiny_mce_ext/css/content.css",

    // default interface language
    language    : "ru",

    // Drop lists for link/image/media/template dialogs
    template_external_list_url  : "/js/libs/tiny_mce_ext/lists/template_list.js",
    external_link_list_url      : "/js/libs/tiny_mce_ext/lists/link_list.js",
    external_image_list_url     : "/js/libs/tiny_mce_ext/lists/image_list.js",
    media_external_list_url     : "/js/libs/tiny_mce_ext/lists/media_list.js",

    // Style formats
    style_formats : [
        {title : 'Bold text', inline : 'b'},
        {title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
        {title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
        {title : 'Example 1', inline : 'span', classes : 'example1'},
        {title : 'Example 2', inline : 'span', classes : 'example2'},
        {title : 'Table styles'},
        {title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
    ],

    // Replace values for the template plugin
    template_replace_values : {
        username : "Some User",
        staffid : "991234",
        count : "1"
    }
});

 tinyMCE.init({
    // General options
    mode        : "exact",
    elements    : "settingsAddress",
    theme       : "simple",
    skin    	: "o2k7",
    language    : "ru",
    relative_urls : false,
    remove_script_host : true,
    document_base_url : "/",
    convert_urls : true, 
});

function toggleEditor(id) {
    if (!tinyMCE.get(id))
         tinyMCE.execCommand('mceAddControl', false, id);
    else tinyMCE.execCommand('mceRemoveControl', false, id);
}
