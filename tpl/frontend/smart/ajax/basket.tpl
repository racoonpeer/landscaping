<div class="items">
<{foreach name=i from=$Basket->getItems() key=arKey item=arItem}>
    <{if empty($arItem.arKits)}>
        <div class="product-item">     
            
            <a class="del" href="javascript:void(0)" onclick="Basket.deleteItem('<{$arKey}>', <{$arItem.quantity}>);">X</a>
            
            <div class="image">
                <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}>">
                    <img src="<{$arItem.middle_image}>" alt="" align="top"/>
                </a>
            </div>
            <div class="title">
                <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}>"><{$arItem.title}></a>
            </div>
            <{if $arItem.price!=$arItem.old_price}>
                <div class="price new">
                    <strong><{$arItem.price|number_format:0:'.':' '}></strong> <small>грн</small>
                    <div class="old">
                        <strong><{$arItem.old_price|number_format:0:'.':' '}></strong> <small>грн</small>
                    </div>
                </div>
            <{else}>
                <div class="price">
                    <strong><{$arItem.price|number_format:0:'.':' '}></strong> <small>грн</small>
                </div>
            <{/if}>
            
            <select onchange="Basket.add('<{$arKey}>', this.options[this.selectedIndex].value, 1);">
                <{section loop=6 name=c start=1 }>
                    <option value="<{$smarty.section.c.iteration}>" <{if $arItem.quantity==$smarty.section.c.iteration}>selected<{/if}>><{$smarty.section.c.iteration}></option>
                <{/section}>
                <{if $arItem.quantity > 5}>
                    <option value="<{$arItem.quantity}>" selected><{$arItem.quantity}></option>
                <{/if}>
            </select>

            <strong><{$arItem.amount|number_format:0:'.':' '}></strong> <small>грн</small>
        </div>     
        <{if !$smarty.foreach.i.last}><hr/><{/if}>
        
    <{else}>
        <div class="kit">
            <h3>Комплект</h3>
            <div class="product-item">
                <a class="del" onclick="Basket.deleteItem('<{$arKey}>', <{$arItem.quantity}>);" href="javascript:void(0);">X</a>
                <div class="image">
                    <a href="<a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}>">
                        <img src="<{$arItem.middle_image}>" alt="" align="top"/>
                    </a>
                </div>
                <div class="f-left info">
                    <div class="title">
                        <a href="<{include file='core/href_item.tpl' arCategory=$arItem.arCategory arItem=$arItem}>"><{$arItem.title}></a>
                    </div>
                    <{if $arItem.kit_price!=$arItem.old_price}>
                        <div class="price new">
                            <strong><{$arItem.kit_price|number_format:0:'.':' '}></strong> <small>грн</small>
                            <div class="old">
                                <strong><{$arItem.old_price|number_format:0:'.':' '}></strong> <small>грн</small>
                            </div>
                        </div>
                    <{else}>
                        <div class="price">
                            <strong><{$arItem.kit_price|number_format:0:'.':' '}></strong> <small>грн</small>
                        </div>
                    <{/if}>
                </div>
            </div>
            <hr/>            
            <{foreach name=k from=$arItem.arKits key=kKey item=arKit}>
                <div class="product-item">
                    <div class="image">
                        <a href="<{include file='core/href_item.tpl' arCategory=$arKit.arCategory arItem=$arKit}>">
                            <img src="<{$arKit.middle_image}>" alt="" align="top"/>
                        </a>
                    </div>
                    <div class="f-left info">
                        <div class="title">
                            <a href="<{include file='core/href_item.tpl' arCategory=$arKit.arCategory arItem=$arKit}>"><{$arKit.title}></a>
                        </div>
                        <{if $arKit.price!=$arKit.old_price}>
                        <div class="price new">
                            <strong><{$arKit.price|number_format:0:'.':' '}></strong> <small>грн</small>
                            <div class="old">
                                <strong><{$arKit.old_price|number_format:0:'.':' '}></strong> <small>грн</small>
                            </div>
                        </div>
                        <{else}>
                            <div class="price">
                                <strong><{$arKit.price|number_format:0:'.':' '}></strong> <small>грн</small>
                            </div>
                        <{/if}>
                    </div>
                </div>
            <{/foreach}>
            <select onchange="Basket.add('<{$arKey}>', this.options[this.selectedIndex].value, 1);">
                <{section loop=6 name=c start=1 }>
                    <option value="<{$smarty.section.c.iteration}>" <{if $arItem.quantity==$smarty.section.c.iteration}>selected<{/if}>><{$smarty.section.c.iteration}></option>
                <{/section}>
            </select>
            <strong><{$arItem.amount|number_format:0:'.':' '}></strong> <small>грн</small>
        </div>
    <{/if}>
<{/foreach}>
</div>

<div class="totals clearfix">
    <div class="f-left label">
        <strong>Итого</strong>
    </div>
    <div class="f-right price">
        <strong><{$Basket->getTotalPrice()|number_format:0:'.':' '}></strong> <small>грн</small>
    </div>
</div>