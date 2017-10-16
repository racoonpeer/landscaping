<{if !empty($arCategories)}>
    <div class="left">
        <ul>
            <{section name=i loop=$arCategories}>
                <li class="<{if $arrPageData.cid==$arCategories[i].id}>active<{/if}>">
                    <a href="<{include file='core/href.tpl' arCategory=$arCategory params='cid='|cat:$arCategories[i].id}><{if !empty($arrPageData.stext)}>&stext=<{$arrPageData.stext}><{/if}>" title="<{$arCategories[i].title}>"><{$arCategories[i].title}></a>
                </li>
            <{/section}>
        </ul>
    </div>
<{/if}>

<div class="right">
    <{include file='ajax/products.tpl' items=$items count=4 simple=1}>
</div>

<div class="clear"></div>
