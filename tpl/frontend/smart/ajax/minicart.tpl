<{if $Basket->getTotalAmount()>0}>
    <span><a href="<{include file='core/href.tpl' arCategory=$arrModules.checkout}>"><strong><{$Basket->getTotalAmount()}></strong> ��. �� <strong><{$Basket->getTotalPrice()|number_format:0:'.':' '}></strong> ���</a></span>
<{else}>
    <span style="color:#fff;">������� �����</span>
<{/if}>
