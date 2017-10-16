<{section name=i loop=$topMenu}>
<a href="<{include file='core/href.tpl' arCategory=$topMenu[i]}>" title="<{$topMenu[i].title}>">
    <img alt="<{$topMenu[i].title}>" src="<{$smarty.const.MAIN_CATEGORIES_URL_DIR}><{$topMenu[i].image}>" />
</a>
<{/section}>
