<{if !empty($items)}>
    <{section name=i loop=$items}>
        <{include file='core/product.tpl' item=$items[i]}>
    <{/section}>
    <{if $arrPageData.total_pages>1}>
    <div class="clearfix"></div>
    <{if isset($simple) && $simple}>
    <{assign var='tamplate' value='pager'}>
    <{else}>
    <{assign var='tamplate' value='pager_catalog'}>
    <{/if}>
    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
    <{include file='core/'|cat:$tamplate|cat:'.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=1 showPrevNext=0 showAll=0}>
    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
    <{/if}>
<{else}>
    <center>
        <h2>:( Товары не найдены!</h2>
    </center>
<{/if}>