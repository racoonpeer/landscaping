<!-- Каталог начало -->
<{if !empty($item) OR !empty($items) OR !empty($arrCategories)}>

<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>


<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{elseif !empty($items)}>
    <div class="content-list">
        <h1><img alt="<{$arCategory.title}>" title="<{$arCategory.title}>" src="<{$arCategory.image}>" /></h1>
        <div id="video-wrapper" class="highslide-video">
<{section name=i loop=$items}>
            <div class="videoitem">
                <p class="videotitle">
                    <a href="javascript:void(0);" onclick="document.getElementById('hs-videoid-<{$items[i].id}>').onclick()" title="<{$items[i].title}>"><{$items[i].title}></a>
                </p>
                <div class="videoimg">
                    <a id="hs-videoid-<{$items[i].id}>" href="/flash/JWPlayer/player.swf" onclick="return hs.htmlExpand(this,
                        {   objectType: 'swf',
                            swfOptions: {
                                version: '9.0.0',
                                expressInstallSwfurl: '/flash/expressInstall.swf',
                                params: { allowscriptaccess:'always', allowfullscreen: 'true', quality: 'high'},
                                flashvars: { file: '&file=<{$items[i].filename}>&backcolor=000000&frontcolor=ffffff&lightcolor=555555&screencolor=000000&screencolor=000000', skin:'/flash/JWPlayer/glow.zip', stretching:'fill',  autostart:<{if !empty($items[i].image)}>'false', image:'<{$items[i].image}>'<{else}>'true'<{/if}> },
                                attributes: { id:'player<{$items[i].id}>', name:'player<{$items[i].id}>' },
                            },
                            <{if !empty($items[i].fileinfo.highslide)}>width: <{$items[i].fileinfo.highslide.width}>,<{/if}>
                            <{if !empty($items[i].fileinfo.highslide)}>height: <{$items[i].fileinfo.highslide.height}>,<{/if}>
                            objectWidth: <{if !empty($items[i].fileinfo.highslide)}><{$items[i].fileinfo.highslide.resolution_x}><{else}>640<{/if}>,
                            objectHeight: <{if !empty($items[i].fileinfo.highslide)}><{$items[i].fileinfo.highslide.resolution_y}><{else}>480<{/if}>
                        });" class="highslide" title="<{$items[i].title}>" style="background:url('<{$items[i].small_image}>') no-repeat scroll 1px 1px transparent"><img src="<{$smarty.const.UPLOAD_URL_DIR}>video/small_nofile.png" alt="<{$items[i].title}>" /></a>
                </div>
            </div>
<{/section}>
        <br clear="all" />
        </div>
<{if $arrModules.video.id!=$arrPageData.catid}>
        <div class="backurl-link">
            <a href="<{include file='core/href.tpl' arCategory=$arrModules.video}>"><{$smarty.const.URL_NEWS_BACK}></a>
        </div>
<{/if}>

<{if $arrPageData.total_pages>1}>
        <div class="pager">
<!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=0}>
<!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <div class="cf-clear"></div>
        </div>
<{/if}>
    </div>
<{/if}>

<{* DISPLAY CATEGORY INFO *}>
<{else}>
<{include file='core/static.tpl'}>
<{/if}>
<!--Каталог конец-->
