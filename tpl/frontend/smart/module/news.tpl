<div class="main-box main-box-content">
<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
    <div class="midlle-container new">
<{if !empty($item.images)}>
        <div class="left-box">
            <div class="gallary">
                <div class="big-img">
                    <img src="<{$item.images[0].image}>" alt="">
                </div>
<{if count($item.images)>0}>
                <ul class="img-slider">
<{section name=i loop=$item.images}>
                    <li class="select<{if $smarty.section.i.first}> active<{/if}>">
                        <a href="<{$item.images[i].image}>" data-type="image">
                            <img src="<{$item.images[i].small_image}>" alt=""/>
                        </a>
                    </li>
<{/section}>
                </ul>
<{/if}>
            </div>
        </div>
<{/if}>
        <div class="right-box">
            <div class="descr">
                <p class="date"><{$item.created|date_format:"%d.%m.%Y"}></p>
                <hgroup>
                    <h1><{$item.title}></h1>
                    <h2><{$item.subtitle}></h2>
                </hgroup>
                <{$item.fulldescr}>
            </div>
            <div class="link-list">
                <div class="link">
                    <a href="<{$arrPageData.backurl}>"><{$smarty.const.URL_NEWS_BACK}></a>
                </div>
            </div>
        </div>
    </div>
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{elseif !empty($items)}>
    <div class="midlle-container news-list">
<{section name=i loop=$items}>
        <div class="news-block">
            <div class="left">
                <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                    <img src="<{$items[i].image}>" alt="<{$items[i].title}>"/>
                </a>
            </div>
            <div class="right descr">
                <p class="date" style="margin: 0"><{$items[i].created|date_format:"%d.%m.%Y"}></p>
                <h2><a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>" class="link-news" style="margin: 0"><{$items[i].title}></a></h2>
                <{$items[i].descr|unScreenData}>
            </div>
        </div>
<{/section}>
    </div>
<{*if $arrPageData.total_pages>1}>
<!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=1}>
<!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if*}>
<{* DISPLAY CATEGORY INFO *}>
<{else}>
<{include file='core/static.tpl'}>
<{/if}>
</div>