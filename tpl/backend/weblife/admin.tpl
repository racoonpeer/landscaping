<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<!-- ++++++++++ Start TOP Menu +++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/head.tpl'}>
<!-- ++++++++++ End TOP Menu +++++++++++++++++++++++++++++++++++++++++++++++ -->    
    <body>
        <div id="win">
            <div id="top_head">
                <div class="top_menu">
<!-- ++++++++++ Start TOP Menu +++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/top_settings.tpl'}>
<!-- ++++++++++ End TOP Menu +++++++++++++++++++++++++++++++++++++++++++++++ -->

<!-- ++++++++++ Start LANG Menu ++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/top_langs.tpl'}>
<!-- ++++++++++ End LANG Menu ++++++++++++++++++++++++++++++++++++++++++++++ -->

<!-- ++++++++++ Start LEFT TOP Menu ++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/top_logout.tpl'}>
<!-- ++++++++++ End LEFT TOP Menu ++++++++++++++++++++++++++++++++++++++++++ -->

<!-- ++++++++++ Start User Info (Welcome) ++++++++++++++++++++++++++++++++++ -->
<{*include file='common/userinfo.tpl'*}>
<!-- ++++++++++ End User Info (Welcome) ++++++++++++++++++++++++++++++++++++ -->
                </div>
            </div>

            <div class="clear"></div>

            <div class="main_menu">
<!-- ++++++++++ Start LEFT TOP Menu ++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/main_menu.tpl'}>
<!-- ++++++++++ End LEFT TOP Menu ++++++++++++++++++++++++++++++++++++++++++ -->
            </div>
            <div class="clear"></div>
            
            <div id="main_content">
                <div id="content" class="<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem' || $arrPageData.module=='settings' || $arrPageData.module=='cms_settings' || $arrPageData.module=='users' || $arrPageData.module=='customers' }>editarea<{/if}>">              
<!-- ++++++++++ Start Page Content +++++++++++++++++++++++++++++++++++++++++ -->
<{include file='module/'|cat:$arrPageData.module|cat:'.tpl'}>
<!-- ++++++++++ End Page Content +++++++++++++++++++++++++++++++++++++++++++ -->
                </div>
            </div>
            <div id="footer"></div>
        </div>
    </body>
</html>