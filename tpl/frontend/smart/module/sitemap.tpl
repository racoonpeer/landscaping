<{* REQUIRE VARS: $arItems=array(), $treeLevel=int*}>
<{'        '|str_repeat:$treeLevel}><ul<{if $treeLevel==0}> id="sitemap-wrapper"<{/if}>>
<{section name=i loop=$arItems}>
<{if !($arItems[i].id > 1 && $arItems[i].id < 11)}>
    <{'        '|str_repeat:$treeLevel}><li>
        <{'        '|str_repeat:$treeLevel}><a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>" title="<{$arItems[i].title}>"><{$arItems[i].title}></a>
<{if !empty($arItems[i].childrens)}>
<{include file='module/sitemap.tpl' arItems=$arItems[i].childrens treeLevel=$arItems[i].level}>
<{/if}>
    <{'        '|str_repeat:$treeLevel}></li>
<{/if}>
<{/section}>
<{'        '|str_repeat:$treeLevel}></ul>
