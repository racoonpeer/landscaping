<div onclick="window.location='<{include file='core/href.tpl' arCategory=$arCategory params='action=remove&itemID='|cat:$item.id}>'">удалить</div>
<a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>">
    <img src="<{$item.small_image}>" />
</a>
<br/>
<a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>" ><strong><{$item.title}></strong></a>
<br/>
<{$item.price|number_format:0:'.':' '}> <{$smarty.const.SITE_CURRENCY}>
<br/>
<a class="add-to-cart" onclick="Basket.add(<{$item.id}>)" href="javascript:void(0);"><{$smarty.const.BUY}></a>
