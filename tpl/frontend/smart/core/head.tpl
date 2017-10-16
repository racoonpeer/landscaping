<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<{$arrLangs.$lang.charset}>" />
    <title><{$HTMLHelper->prepareHeadTitle($arCategory)}></title>
    <meta name="keywords" content="<{$arCategory.meta_key}>"/>
    <meta name="description" content="<{$arCategory.meta_descr}>"/>
<{if $arCategory.meta_robots}>
    <meta name="robots" content="<{$arCategory.meta_robots}>" />
<{/if}>
<{if $objSettingsInfo->logo}>
    <link rel="icon" href="<{$objSettingsInfo->logo}>" type="image/x-icon" />
    <link rel="shortcut icon" href="<{$objSettingsInfo->logo}>" type="image/x-icon" />
<{/if}>
<{foreach from=$arrPageData.headCss item=style}>
    <link href="<{$style}>?v=<{$smarty.now}>" type="text/css" rel="stylesheet"/>
<{foreachelse}>
    <link href="/css/store/common.css?v=<{$smarty.now}>" type="text/css" rel="stylesheet"/>
<{/foreach}>
<{foreach from=$arrPageData.headScripts item=script}>
    <script type="text/javascript" src="<{$script}>?v=<{$smarty.now}>"></script>
<{/foreach}>
    <{include file='core/header-extra.tpl'}>
</head>