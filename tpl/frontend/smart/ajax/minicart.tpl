<{if $Basket->getTotalAmount()>0}>
    <span><a href="<{include file='core/href.tpl' arCategory=$arrModules.checkout}>"><strong><{$Basket->getTotalAmount()}></strong> шт. на <strong><{$Basket->getTotalPrice()|number_format:0:'.':' '}></strong> грн</a></span>
<{else}>
    <span style="color:#fff;">Корзина пуста</span>
<{/if}>
