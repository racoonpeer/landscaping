<{* REQUIRE VARS: $arrBreadCrumb=array()*}><{*include file='core/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb*}>
<!-- ++++++++++++++ Start BREADCRUMB Wrapper +++++++++++++++++++++++++++++++ -->
<{foreach name=i from=$arrBreadCrumb key=sKey item=sItem}>
<{if !$smarty.foreach.i.last}>
    <a href="<{$sKey}>" class="path<{if $smarty.foreach.i.first}> first<{elseif $smarty.foreach.i.last}> last<{/if}>" title="<{$sItem}>">
        <{$sItem}>
    </a> >> 
<{else}>
    <span><{$sItem}></span>
<{/if}>
<{/foreach}>
<!-- ++++++++++++++ End BREADCRUMB Wrapper +++++++++++++++++++++++++++++++++ -->
