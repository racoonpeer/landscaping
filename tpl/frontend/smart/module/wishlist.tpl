<div class="left" style="width: 195px;">
<{section name=i loop=$arrPageData.categories}>
    <a href="<{include file='core/href.tpl' arCategory=$arCategory params='cid='|cat:$arrPageData.categories[i].id}>" class="<{if $arrPageData.categories[i].opened}>active<{/if}>"><{$arrPageData.categories[i].title}></a>
<{if !$smarty.section.i.last}><br/><{/if}>
<{/section}>
</div>
<div class="right" style="width:800px;">
    <h2><{$arCategory.title}></h2>
    <div id="products"><{include file='ajax/products.tpl' items=$items wishList=1}></div>
</div>