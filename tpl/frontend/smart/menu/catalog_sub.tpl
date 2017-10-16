<{* REQUIRE VARS: $arItems=array()}> <{include file='menu/catalog_sub.tpl' arItems=$HTMLHelper->getFilterMenuLevel($arItems[i]) *}>
<{if !empty($arItems)}>
<ul class="submenu">
<{foreach name=i from=$arItems key=filterID item=filter}>
<{foreach name=j from=$filter.children key=arKey item=arItem}>
    <li>
        <a href="<{$arItem.href}>"><{$arItem.title}></a>
    </li>
<{/foreach}>
<{/foreach}>
</ul>
<{/if}>