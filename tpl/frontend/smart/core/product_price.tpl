<{if $item.new_price}>
<div class="f-left price new">
    <strong><{$item.new_price|number_format:0:'.':' '}></strong> <small>грн</small>
    <div class="old"><strong><{$item.price|number_format:0:'.':' '}></strong> <small>грн</small></div>
</div>
<{else}>
<div class="f-left price">
    <strong><{$item.price|number_format:0:'.':' '}></strong> <small>грн</small>
</div>
<{/if}>