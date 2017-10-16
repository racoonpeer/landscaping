<!-- ++++++++++++++ Start HEADER Wrapper +++++++++++++++++++++++++++++++++++ -->
<{*include file='core/lists_links.tpl'*}>
<div id="compare_list">
    <a data-href="<{include file='core/href.tpl' arCategory=$arrModules.compare}>" 
       <{if !empty($arrPageData.compare)}>href="<{include file='core/href.tpl' arCategory=$arrModules.compare}>?compare=<{$arrPageData.compare|@implode:','}>"
       <{else}>nohref<{/if}>>
        <{$smarty.const.COMPARE_LIST}> <span><{count($arrPageData.compare)}></span>
    </a>
</div>
<div class="wrapper_wishList" style="color:#242831;<{if empty($arrPageData.wishlist)}>display:none;<{/if}>">Список покупок <a class="wishlist" href="<{include file='core/href.tpl' arCategory=$arrModules.wishlist}>"><{count($arrPageData.wishlist)}></a></div>  
<!-- ++++++++++++++ End HEADER Wrapper +++++++++++++++++++++++++++++++++++++ -->
