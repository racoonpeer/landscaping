<{if $item.new_price}>
<{assign var=kit_price value=$item.new_price}>
<{else}>
<{assign var=kit_price value=$item.price}>
<{/if}>
<{assign var=kit_cprice value=$kit_price}>
<table>
    <tr>
        <td class="product_wrapper item">
            <{if !empty($item.defaultImage)}>
            <div class="image">
                <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>">
                    <img src="<{$item.defaultImage.middle_image}>" alt="" align="top"/>
                </a>
            </div>
            <{/if}>
            <div class="title">
                <a href="<{include file='core/href_item.tpl' arCategory=$item.arCategory arItem=$item}>"><{$item.title}></a>
            </div>
<{if $item.new_price}>
                <div class="price new">
                    <strong><{$item.new_price|number_format:0:'.':' '}></strong> <small>грн</small>
                    <div class="old">
                        <strong><{$item.price|number_format:0:'.':' '}></strong> <small>грн</small>
                    </div>
                </div>
<{else}>
                <div class="price">
                    <strong><{$item.price|number_format:0:'.':' '}></strong> <small>грн</small>
                </div>
<{/if}>
            <div class="wish">
                 <a href="javascript:void(0);" class="wishList add-list-item<{if in_array($item.id, $arrPageData.wishlist)}> disabled<{/if}>" data-alt="<{if !in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" title="<{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" data-itemid="<{$item.id}>" onclick="<{if !in_array($item.id, $arrPageData.wishlist)}>WishList.add(this);<{else}>WishList.delete(this);<{/if}>"><span><{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}></span></a>
            </div>
        </td>
<{section name=i loop=$arKits}>
        <td class="separator">+</td>
        <td class="product_wrapper item">
            <{if !empty($arKits[i].defaultImage)}>
            <div class="image">
                <a href="<{include file='core/href_item.tpl' arCategory=$arKits[i].arCategory arItem=$arKits[i]}>">
                    <img src="<{$arKits[i].defaultImage.middle_image}>" alt="" align="top"/>
                </a>
            </div>
            <{/if}>
            <div class="title">
                <a href="<{include file='core/href_item.tpl' arCategory=$arKits[i].arCategory arItem=$arKits[i]}>"><{$arKits[i].title}></a>
            </div>
            <div class="price new">
                <div class="old" style="text-decoration: line-through;">
                    <strong><{$arKits[i].price|number_format:0:'.':' '}></strong> <small>грн</small>
                </div>
                <strong><{$arKits[i].cprice|number_format:0:'.':' '}></strong> <small>грн</small>
            </div>
            <div class="wish">
                 <a href="javascript:void(0);" class="wishList add-list-item<{if in_array($arKits[i].id, $arrPageData.wishlist)}> disabled<{/if}>" data-alt="<{if !in_array($arKits[i].id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" title="<{if in_array($arKits[i].id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" data-itemid="<{$arKits[i].id}>" onclick="<{if !in_array($arKits[i].id, $arrPageData.wishlist)}>WishList.add(this);<{else}>WishList.delete(this);<{/if}>"><span><{if in_array($arKits[i].id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}></span></a>
            </div>
        </td>
<{$kit_price = ($kit_price + $arKits[i].price)}>
<{$kit_cprice = ($kit_cprice + $arKits[i].cprice)}>
<{/section}>
        <td class="separator">=</td>
        <td class="summary">
            <div class="price">
                <strong><{$kit_cprice|number_format:0:'.':' '}></strong> <small>грн</small>
                <div class="old">
                    <strong><{$kit_price|number_format:0:'.':' '}></strong> <small>грн</small>
                </div>
            </div>
            <div class="result">Вы экономите <strong><{($kit_price - $kit_cprice)|number_format:0:'.':' '}></strong> грн</div>
            <a class="btn btn-green btn-mid addToCart" href="javascript:void(0);" onclick="<{if isset($replaceItem)}>Basket.replaceItem(<{$replaceItem}>, '<{$item.arKitsIDX|implode:$Basket->kitSeparator}>');<{else}>Basket.add('<{$item.arKitsIDX|implode:$Basket->kitSeparator}>');<{/if}>">КУПИТЬ КОмплект</a>
        </td>
    </tr>
</table>