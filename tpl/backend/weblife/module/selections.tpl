<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.SELECTIONS 
          creat_title=$smarty.const.SELECTIONS
          edit_title=$smarty.const.SELECTIONS
}>

<div id="left_block">
   <ul class="filetree category_tree treeview">  
       <li>
           &nbsp;<a href="<{$arrPageData.admin_url}>">Выборки</a>
           <ul>
            <{foreach from=$PHPHelper->SELECTIONS item=title key=field}>       
                <li class="<{if $arrPageData.type==$field}>active<{/if}>">
                    <a href="<{$arrPageData.admin_url|cat:"&type="|cat:$field}>" 
                       class="<{if $arrPageData.type==$field}>selected<{/if}>">
                       <img src="/images/admin/treeview/folder-closed.png" /> 
                        <{$title}> 
                    </a>
                </li>
            <{/foreach}>
            </ul>
        </li>
    </ul>
</div>
    
<div id="right_block">
<form method="POST" id="searchForm" action="<{$arrPageData.current_url|cat:"&task=addItem"}>">
    <{$smarty.const.CATALOG_SEARCH}>:
    <input type="hidden" name="searchItemID" value="" id="searchItemID" />
    <input type="text" size="100" class="field" id="categorySearch" value="" />
    <button type="submit" class="buttons right" style="margin-top:0"><{$smarty.const.BUTTON_ADD}></button>
</form>
<div class="clear"></div> 
<script type="text/javascript">
    $(function() {
        $('#categorySearch').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '/interactive/ajax.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        zone: 'admin',
                        action: 'liveSearch',
                        module: 'catalog',
                        searchStr: request.term,
                        type: '<{$arrPageData.type}>'
                    }, 
                    success: function(json) {
                        response($.map(json.items, function(item) {
                            return {
                                label: item.title,
                                value: item.title,
                                category: item.ctitle,
                                id: item.id
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $('#searchItemID').val(ui.item.id);
            },
            minLength: 2
        });
    });
</script>


<{if !empty($items)}>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
<{if !$arrPageData.cid}>
            <td id="headb" align="center" width="100"><{$smarty.const.HEAD_CATEGORY}></td>
<{/if}>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>
<{section name=i loop=$items}>
         <tr>
            <td ><a href="/admin.php?module=catalog&task=editItem&itemID=<{$items[i].id}>"><{$items[i].title}></a></td>
<{if !$arrPageData.cid}>
            <td ><{$items[i].cat_title}></td>
<{/if}>     
            <td  align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td  align="center" >
                <a href="/admin.php?module=catalog&task=editItem&itemID=<{$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td  align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>"  title="<{$smarty.const.LABEL_DELETE}>">
                   <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                </a>
            </td>
        </tr>
<{/section}>

    <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td align="center" width="350"></td>
            <td align="center" width="350">
                <{if $arrPageData.total_pages>1}>
                    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <{/if}>
            </td>
            <td align="right">
                <input name="submit_order" class="buttons" type="submit" value="<{$smarty.const.BUTTON_APPLY}>" />
            </td>
        </tr>
    </table>
</form>
<{else}>
    <strong><{$smarty.const.LABEL_EMPTY_SELECTIONS}></strong>
<{/if}>
</div>
