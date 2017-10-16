<h2><{$arCategory.title}></h2>
<{$arCategory.text}>

<{if !empty($arrItems.watched)}>
    <{include file='ajax/products.tpl' items=$arrItems.watched}>
<{/if}>
