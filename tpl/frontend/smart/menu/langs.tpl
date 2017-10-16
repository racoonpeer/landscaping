<div class="soc">
    <ul>
<{if $arrLangs|@count>1}>
<{foreach from=$arrLangs key=lnKey item=arLnItem name=i}>
        <li class="language <{if $lnKey==$lang}>active<{/if}>">
<{if $lnKey!=$lang}><a href="<{$arLangsUrls.$lnKey}>"><{/if}>
            <{$arLnItem.title}>
<{if $lnKey!=$lang}></a><{/if}>
        </li>
<{/foreach}>
<{/if}>
        <li><a href="//facebook.com/landscaping.kiev.ua" class="fb" target="_blank"></a></li>
        <li><a href="//instagram.com/amazing_gardening_kiev" class="inst" target="_blank"></a></li>
    </ul>
</div>
