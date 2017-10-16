<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<{$arrLangs.$lang.charset}>" />
    <title><{$arrPageData.headTitle}></title>
    <meta name="keywords" content="<{$arCategory.meta_key}>" />
    <meta name="description" content="<{$arCategory.meta_descr}>" />
<{if $arCategory.meta_robots}>
        <meta name="robots" content="<{$arCategory.meta_robots}>" />
<{/if}>
<{if $objSettingsInfo->logo}>
    <link rel="icon" href="<{$objSettingsInfo->logo}>" type="image/x-icon" />
    <link rel="shortcut icon" href="<{$objSettingsInfo->logo}>" type="image/x-icon" />
<{/if}>
    <link rel="stylesheet" type="text/css" href="<{$arrPageData.css_dir}>style.css" />
    <link rel="stylesheet" type="text/css" href="<{$arrPageData.css_dir}>style-ajax.css" />
    <!--[if IE 6]>
        <link rel="stylesheet" type="text/css" href="<{$arrPageData.css_dir}>style-ie6.css" />
        <link rel="stylesheet" type="text/css" href="<{$arrPageData.css_dir}>style-ajax-ie6.css" />
    <![endif]-->

    <script type="text/javascript" src="/js/jquery/jquery-<{$smarty.const.WLCMS_JQUERY_VERSION}>.min.js"></script>
    <script type="text/javascript" src="/js/jquery/jquery.easing.1.3.js"></script>
    <script type="text/javascript" src="/js/jquery/jquery.migrate.1.2.1.js"></script>
<{if !empty($arrPageData.headCss)}>
    <{$arrPageData.headCss|implode:"\n    "}>

<{/if}>
<{if !empty($arrPageData.headScripts)}>
    <{$arrPageData.headScripts|implode:"\n    "}>

<{/if}>
    <script type="text/javascript" src="/js/scripts_ajax.js"></script>
</head>
<body><!-- wrapper -->
<!-- ++++++++++ Start Page Content +++++++++++++++++++++++++++++++++++++++++ -->
<{if !empty($arCategory.module)}>
<{include file='module/'|cat:$arCategory.module|cat:'.tpl'}>
<{else}>
                    Ajax Модуль не загружен
<{/if}>
<!-- ++++++++++ End Page Content +++++++++++++++++++++++++++++++++++++++++++ -->
    <!-- wrapper eof -->

<{include file='core/footer-extra.tpl'}>

</body>
</html>