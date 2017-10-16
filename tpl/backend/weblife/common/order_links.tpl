<{* REQUIRE VARS: $arrOrderLinks=array()*}>
<div class="order_links">
    <{$smarty.const.HEAD_LINK_SORTBY}>:
<{section name=i loop=$arrOrderLinks}>
    &nbsp;&nbsp;&nbsp;<a href="<{$arrOrderLinks[i].link}>" class="bc_order"><{$arrOrderLinks[i].title}></a>
<{/section}>
</div>