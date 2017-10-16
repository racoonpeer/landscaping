<!DOCTYPE html>
<html lang="<{$lang}>">
    <{include file="core/head.tpl"}>
    <body>
        <{include file='core/header.tpl'}>
        <{include file='menu/sub.tpl' arItems=$subMenu}>
<{if !empty($arCategory.module)}>
        <{include file='module/'|cat:$arCategory.module|cat:'.tpl'}>
<{else}>
        <{include file='core/static.tpl'}>
<{/if}>
        <{include file='core/footer.tpl'}>
        <{include file='core/footer-extra.tpl'}>
    </body>
</html>