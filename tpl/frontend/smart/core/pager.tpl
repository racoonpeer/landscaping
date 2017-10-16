<{* REQUIRE VARS: arrPager=array(), page=int, showTitle=[0|1], showFirstLast=[0|1], showPrevNext=[0|1], showAll=[0|1] *}>
<{* arrPager = array_keys(all, first, last, prev, next, count, pages, baseurl, sep) *}>
<div class="pagination">
    <ul>
<{if $showPrevNext AND $page > 1}>
        <li class="first">
            <a href="<{$arrPager->getUrl($arrPager->getPrev())}>">
                <{$smarty.const.SITE_PAGER_PREV}>
            </a>
        </li>
<{/if}>
<{* START SHOW PAGES *}>
<{foreach name=i from=$arrPager->getPages() item=iItem}>
        <li class="<{if $page==$iItem}>active<{/if}>">
<{if $arrPager->getSeparator() == $iItem}>
            <a href="javascript:void(0);"><{$iItem}></a>
<{elseif $page==$iItem}>
            <{$iItem}>
<{else}>
            <a href="<{$arrPager->getUrl($iItem)}>"><{$iItem}></a>
<{/if}>          
        </li>
<{/foreach}>
<{* END SHOW PAGES *}>
<{if $showPrevNext AND $page < $arrPager->getCount()}>
        <li class="last">
            <a href="<{$arrPager->getUrl($arrPager->getNext())}>">
                <{$smarty.const.SITE_PAGER_NEXT}>
            </a>
        </li>  
<{/if}>
    </ul>
</div>