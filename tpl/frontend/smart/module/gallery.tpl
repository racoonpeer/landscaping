<div class="main-box main-box-content">
    <div class="midlle-container">
<{if !empty($item)}>
        <div class="page-slider-slick">
            <div class="single-item-2">
<{section name=i loop=$item.images}>
                <div class="slider-element slick-slide">
                    <img src="<{$item.images[i].image}>" alt=""/>
                </div>
<{/section}>
            </div>
        </div>
        <div class="link-list">
            <div class="link">
                <a href="<{$arrPageData.backurl}>"><{$smarty.const.URL_GALLERY_BACK}></a>
            </div>
        </div>
    </div>
    <div class="midlle-container">
        <div class="page-content">
            <h1 class="page-title"><{$item.title}></h1>
<{if !empty($item.features)}>
            <ul class="list-features">
<{section name=i loop=$item.features}>
                <li><strong><{$item.features[i].title|unScreenData}></strong> — <{$item.features[i].value|unScreenData}></li>
<{/section}>
            </ul>
<{/if}>
            <{$item.descr}>
        </div>
<{elseif !empty($items)}>
        <div class="projects-box">
<{section name=i loop=$items}>
            <div class="project">
                <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>" class="img-project">
                    <img src="<{$items[i].cover}>" alt="">
                </a>
                <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>" class="link-title"><{$items[i].title}></a>
            </div>
<{/section}>
        </div>
<{if $arrPageData.total_pages>1}>
        <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=0}>
<{/if}>
<{else}>
        <div class="page-content">
            <br/><br/><br/>
            <br/><br/><br/>
            <center><{$smarty.const.NO_CONTENT}></center>
            <br/><br/><br/>
        </div>
<{/if}>
    </div>
</div>