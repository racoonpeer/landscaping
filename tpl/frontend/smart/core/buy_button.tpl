<a <{if $Basket->isSetKey($item.idKey)}>
    href="<{include file='core/href.tpl' arCategory=$arrModules.checkout}>"
<{else}>
<{if $list}>
    href="javascript:void(0);" onclick="Basket.add('<{$item.id}>', 1, 0, <{$list|intval}>, {item: $(this).closest('.product-item'), target: $(this).closest('.product-item').find('.add-to-cart'), link: '<{include file='core/href.tpl' arCategory=$arrModules.checkout}>', text: '<{$smarty.const.IN_CART}>'});"
<{else}>
    href="javascript:void(0);" onclick="Basket.add('<{$item.id}>', 1, 0, <{$list|intval}>, {item: $(this).closest('.details'), target: $(this).closest('.details').find('.add-to-cart'), link: '<{include file='core/href.tpl' arCategory=$arrModules.checkout}>', text: '<{$smarty.const.IN_CART}>'});"
<{/if}> 
<{/if}> 
    class="add-to-cart f-right<{if $Basket->isSetKey($item.idKey)}> inCart f-right<{else}> addToCart btn btn-mid btn-green<{/if}>">
<{if !$Basket->isSetKey($item.idKey)}>
    <{$smarty.const.BUY}>
<{else}>
    <{$smarty.const.IN_CART}>
<{/if}>
</a>