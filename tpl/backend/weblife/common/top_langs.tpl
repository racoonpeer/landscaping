<div class="top_langs">
    <{if $arrLangs|@count>1}>
        <{foreach from=$arrLangs key=lnKey item=arLnItem}>
            <{if $lnKey==$lang}>
                <span class="active"><{$arLnItem.title}></span>
            <{else}>
                <a href="<{$arLangsUrls.$lnKey}>" title="<{$arLnItem.title}>">
                    <{$arLnItem.title}>
                </a>
            <{/if}>
        <{/foreach}>
    <{/if}>
</div>