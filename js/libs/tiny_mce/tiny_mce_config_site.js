tinyMCE.init({
    // General options
    mode        : "exact",
    elements    : "itemtext",
    theme       : "advanced",
    skin        : "default",
    plugins     : "imagemanager,filemanager,autolink,lists,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,media,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist",

    // Theme options
/*
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,fontselect,fontsizeselect,|,forecolor,backcolor,|,removeformat,cleanup,|,code",
    theme_advanced_buttons2 : "newdocument,|,undo,redo,|,pastetext,pasteword,bullist,numlist,outdent,indent,|,link,unlink,anchor,image,insertfile,media,|,insertlayer,moveforward,movebackward,absolute",
    theme_advanced_buttons3 : "tablecontrols,|,sub,sup,charmap,hr,advhr,emotions,nonbreaking,blockquote",
*/
    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,charmap,advhr,nonbreaking,blockquote,|,forecolor,|,removeformat,cleanup,|,code",
    theme_advanced_buttons2 : "newdocument,pastetext,pasteword,|,undo,redo,|,bullist,numlist,outdent,indent,|,link,unlink,anchor,image,insertfile,media,emotions",
    theme_advanced_buttons3 : "",

    theme_advanced_toolbar_location     : "top",
    theme_advanced_toolbar_align        : "left",
    theme_advanced_statusbar_location   : "bottom",
    theme_advanced_resizing             : false,

    //force_br_newlines : true,

    width: '495px',
    relative_urls : false,

    // Example content CSS (should be your site CSS)
    content_css : "/js/libs/tiny_mce_ext/css/content.css",

    // default interface language
    language    : "ru",

    // Drop lists for link/image/media/template dialogs
    template_external_list_url  : "/js/libs/tiny_mce_ext/lists/template_list.js",
    external_link_list_url      : "/js/libs/tiny_mce_ext/lists/link_list.js",
    external_image_list_url     : "/js/libs/tiny_mce_ext/lists/image_list.js",
    media_external_list_url     : "/js/libs/tiny_mce_ext/lists/media_list.js"

});

function toggleEditor(id) {
    if (!tinyMCE.get(id))
         tinyMCE.execCommand('mceAddControl', false, id);
    else tinyMCE.execCommand('mceRemoveControl', false, id);
}
