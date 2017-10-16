<{* REQUIRE VARS: arrPager=Pager, page=int, showTitle=[0|1], showFirstLast=[0|1], showPrevNext=[0|1], showAll=[0|1] *}>
<{* arrPager have keys(all, first, last, prev, next, count, pages) and methods getUrl($page) and other for get key *}>
<div class="pagination">
    <ul>
<{if $showTitle}>
        <li>
            <{$smarty.const.SITE_PAGES}>:
        </li>
<{/if}>
<{if $showFirstLast AND $page > 1}>
        <li>
            <a href="<{$arrPager->getUrl($arrPager->getFirst())}>">
                <{$smarty.const.SITE_PAGER_FIRST}>
            </a>
        </li>
<{/if}>
<{if $showPrevNext AND $page > 1}>
        <li>
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
        <li>
            <a href="<{$arrPager->getUrl($arrPager->getNext())}>">
                <{$smarty.const.SITE_PAGER_NEXT}>
            </a>
        </li>  
<{/if}>
<{if $showFirstLast AND $page < $arrPager->getCount()}>
        <li>
            <a href="<{$arrPager->getUrl($arrPager->getLast())}>">
                <{$smarty.const.SITE_PAGER_LAST}>
            </a>
        </li>  
<{/if}>
<{if $showAll}>
        <li>
            <a href="<{$arrPager->getUrl($arrPager->getAllUrl())}>">
                <{$smarty.const.SITE_PAGER_ALL}>
            </a>
        </li>
<{/if}>
    </ul>
</div>

