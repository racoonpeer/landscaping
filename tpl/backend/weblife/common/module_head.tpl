<{* REQUIRE VARS: $title=string, $showOrders=[true|false], $addNew=string, $root=string*}>
<{if $arrPageData.task=='addItem'}>
    <div class="title"><{$creat_title}><{if !empty($arrPageData.arParent.title)}> <{$arrPageData.arParent.title}><{/if}></div>
<{else if $arrPageData.task=='editItem'}>
    <div class="title"><{$edit_title}><{if !empty($arrPageData.arParent.title)}> <{$arrPageData.arParent.title}><{/if}></div>
<{else}>
    <div class="title"><{$title}><{if !empty($arrPageData.arParent.title)}> <{$arrPageData.arParent.title}><{/if}></div>
<{/if}>

<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
   
<div class="breadcrumb">
    <{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
        <a href="/admin.php?module=main">Структура разделов</a> 
        <{$arrPageData.path_arrow}> 
        <a href="<{$arrPageData.admin_url}>" title=""><{$title}></a>
    <{else if !empty($arrPageData.arParent)}>
        <a href="/admin.php?module=main">Структура разделов</a> 
    <{/if}>
    <!-- ++++++++++ Start BreadCrumb +++++++++++++++++++++++++++++++++++++++++++ -->
    <{include file='common/breadcrumb.tpl' arrBreadCrumb=$arrPageData.arrBreadCrumb}>
    <!-- ++++++++++ End BreadCrumb +++++++++++++++++++++++++++++++++++++++++++++ -->
</div>
<div class="clear"></div>
