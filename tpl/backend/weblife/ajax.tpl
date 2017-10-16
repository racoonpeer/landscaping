<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <{include file='common/head.tpl'}>
    </head>
    <body class="highslide_ajax">
        <div id="win">
         <{*   <div id="top_head">
                <div class="top_menu">
                    <div class="top_version">
                    <{$smarty.const.ADMIN_AJAX_MODE}> 
                    <{$smarty.const.ADMIN_COPYRIGHT}> 
                    <{$smarty.const.WLCMS_VERSION}>
                    </div>
                </div>
            </div>

            <div class="clear"></div>       *}>   
            <div id="main_content">
                <div id="content">
<!-- ++++++++++ Start Page Content +++++++++++++++++++++++++++++++++++++++++ -->
<{include file='ajax/'|cat:$arrPageData.module|cat:'.tpl'}>
<!-- ++++++++++ End Page Content +++++++++++++++++++++++++++++++++++++++++++ -->
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>