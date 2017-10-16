<div class="product-item" id="product_<{$item.id}>" data-cid="<{$item.cid}>">
    <div class="image">
<{if !empty($item.defaultImage)}>
        <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>">
            <img src="<{$item.defaultImage.middle_image}>" alt="" align="top"/>
        </a>
<{/if}>
<{if $item.isdiscount}>
        <div class="badge sale">Распродажа</div>
<{else if $item.isnewest}>
        <div class="badge new">Новинка</div>
<{/if}>
<{if isset($wishList) AND $wishList}>
        <a href="javascript:void(0);" class="del" onclick="window.location='<{include file='core/href.tpl' arCategory=$arrModules.wishlist params='action=remove&itemID='|cat:$item.id}>'">X</a>
<{/if}>
    </div>
    <div style="font-weight:bold;">
        <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>"><{$item.title}></a>
    </div>
    <div class="cart clearfix">
        <{include file="core/product_price.tpl"}>
        <{include file="core/buy_button.tpl" list=true}>
<{if !empty($item.options)}>
        <div class="options">
            <form>
<{foreach from=$item.options key=optionID item=option}>
                <{include file="core/_option.tpl" list=true}>
<{/foreach}>
            </form>
        </div>
<{/if}>
    </div>
<{if !isset($wishList) || !$wishList}>
        <div class="wish">
             <a href="javascript:void(0);" class="wishList add-list-item<{if in_array($item.id, $arrPageData.wishlist)}> disabled<{/if}>" data-alt="<{if !in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" title="<{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" data-itemid="<{$item.id}>" onclick="<{if !in_array($item.id, $arrPageData.wishlist)}>WishList.add(this);<{else}>WishList.delete(this);<{/if}>"><span><{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}></span></a>
        </div>
<{/if}>
    <a href="javascript:void(0);" class="add-list-item <{if in_array($item.id, $arrPageData.compare)}>disabled<{/if}>" 
       data-alt="<{if !in_array($item.id, $arrPageData.compare)}><{$smarty.const.IN_COMPARE}><{else}><{$smarty.const.ADD_TO_COMPARE}><{/if}>" 
       title="<{if in_array($item.id, $arrPageData.compare)}><{$smarty.const.IN_COMPARE}><{else}><{$smarty.const.ADD_TO_COMPARE}><{/if}>" 
       data-itemid="<{$item.id}>" onclick="<{if !in_array($item.id, $arrPageData.compare)}>Compare.add(this);<{else}>Compare.delete(this);<{/if}>">
        <{if in_array($item.id, $arrPageData.compare)}><{$smarty.const.IN_COMPARE}><{else}><{$smarty.const.ADD_TO_COMPARE}><{/if}>
    </a>
    <div class="attributes">     
<{if !empty($item.arBrand)}>
        <small>Производитель</small>
        <a href="<{include file='core/href_item.tpl' arCategory=$arrModules.brands arItem=$item.arBrand}>"><{$item.arBrand.title}></a><br/>
<{/if}>
<{if !empty($item.attrGroups)}>
<{section name=i loop=$item.attrGroups}>
<{if !empty($item.attrGroups[i].attributes)}>
<{foreach name=j from=$item.attrGroups[i].attributes item=arItem}>
        <small><{$arItem.title}></small>
<{if !empty($arItem.image)}>
        <img width="24" title="<{$arItem.values|@implode:' | '}>" src="/uploaded/attributes/<{$arItem.image}>" />
<{else}>
        <{$arItem.values|@implode:' | '}>
<{/if}>
        <br/>
<{/foreach}>
<{/if}>
<{/section}>
<{/if}>
    </div>
</div>