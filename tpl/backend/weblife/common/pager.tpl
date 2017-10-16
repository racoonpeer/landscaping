<{* array_keys(title, first, last, prev, next, count, pages, baseurl, sep) *}>
<{* REQUIRE VARS: arrPager=array(), showTitle=[0|1], showFirstLast=[0|1], showPrevNext=[0|1] *}>
<{if $showTitle}><{$smarty.const.SITE_PAGES}>:<{/if}>
<{if $showFirstLast}>
    <a href="<{$arrPager.baseurl}>" class="pager first"><{$smarty.const.SITE_PAGER_FIRST}></a>
<{/if}>
<{if $showPrevNext}>
    <a href="<{$arrPager.baseurl}><{if $arrPager.prev>1}><{'&page='|cat:$arrPager.prev}><{/if}>" class="pager prev"><{$smarty.const.SITE_PAGER_PREV}></a>
<{/if}>

<{section name=i loop=$arrPager.pages}>
<{if $arrPager.sep == $arrPager.pages[i]}>
    <span class="pager sep"><{$arrPager.pages[i]}></span>
<{else}>
    <a href="<{$arrPager.baseurl}><{if $arrPager.pages[i]>1}><{'&page='|cat:$arrPager.pages[i]}><{/if}>" class="pager<{if $page==$arrPager.pages[i]}> active<{/if}>"><{$arrPager.pages[i]}></a>
<{/if}>
<{/section}>

<{if $showPrevNext}>
    <a href="<{$arrPager.baseurl|cat:'&page='|cat:$arrPager.next}>" class="pager next"><{$smarty.const.SITE_PAGER_NEXT}></a>
<{/if}>
<{if $showFirstLast}>
    <a href="<{$arrPager.baseurl|cat:'&page='|cat:$arrPager.last}>" class="pager last"><{$smarty.const.SITE_PAGER_LAST}></a>
<{/if}>

