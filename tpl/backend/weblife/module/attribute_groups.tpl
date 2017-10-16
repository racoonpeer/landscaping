<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.ATTRIBUTE_GROUPS 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_ATTR_GROUP 
          edit_title=$smarty.const.ADMIN_EDIT_ATTR_GROUP 
}>

<{if !empty($categoryTree)}>
    <div id="left_block">
       <ul class="filetree category_tree">
           <li>
               <a href="<{$arrPageData.admin_url}>">
                   &nbsp;<img src="/images/admin/treeview/folder.png" /> &nbsp;
                   Все группы
               </a>

                <ul>
                    <{section name=i loop=$categoryTree}>
                          <li>
                            &nbsp; <img src="/images/admin/treeview/folder-closed.png" />  &nbsp;
                                <a href="/admin.php?module=attributes&gid=<{$categoryTree[i].id}>">
                                    <{$categoryTree[i].title}>
                                </a>
                                <a href="<{$arrPageData.admin_url|cat:"&task=editItem&itemID="|cat:$categoryTree[i].id}>">
                                    <img src="/images/operation/edit.png" height="10"/>
                                </a>
                            <{if !empty($categoryTree[i].children)}>
                                <ul>
                                <{section name=j loop=$categoryTree[i].children}>
                                    <li>
                                            <a href="/admin.php?module=attributes&task=editItem&itemID=<{$categoryTree[i].children[j].id}>">
                                                <{$categoryTree[i].children[j].title}>
                                            </a>
                                            <a href="/admin.php?module=attributes&task=editItem&itemID=<{$categoryTree[i].children[j].id}>">
                                                <img src="/images/operation/edit.png" height="10"/>
                                            </a>
                                    </li>
                                <{/section}>
                                </ul>
                            <{/if}>
                        </li>
                    <{/section}>
                </ul>
            </li>
        </ul>
    </div>
<{/if}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<script type="text/javascript">
<!--
    function formCheck(form){
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        return true;
    }
//-->
</script>

<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="order"   value="<{$item.order}>"   />
    
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                    <tr>
                        <td id="headb" align="left" width="175">Заголовок <font style="color:red">*</font><br/>(для отображения в товаре)</td>
                        <td>
                            <input size="70" class="field" name="title" id="title" type="text" value="<{$item.title}>" />
                        </td>
                        <td class="buttons_row" valign="top" width="144">
            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td id="headb" align="left" width="175">Краткое описание <font style="color:red">*</font><br/>(только для админзоны)</td>
                        <td>
                            <input size="70" class="field" name="descr" id="descr" type="text" value="<{$item.descr}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PUBLISH_PAGE}></td>
                        <td align="left">
                            <input type="radio" name="active" value="1" <{if $item.active==1}>checked<{/if}>>
                            <{$smarty.const.OPTION_YES}>
                            <input type="radio" name="active" value="0" <{if $item.active==0}>checked<{/if}>>
                            <{$smarty.const.OPTION_NO}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"></td>
                        <td align="left">
                            
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_ATTR_GROUP}>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="38"></td>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.ATTRIBUTES}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>
<{section name=i loop=$items}>
         <tr>
            <td  align="center">
<{if $items[i].active==1}>
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>">
                    <img src="<{$arrPageData.system_images}>check.png" alt="<{$smarty.const.HEAD_NO_PUBLISH}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>" />
                </a>
<{else}>
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="<{$smarty.const.HEAD_PUBLISH}>">
                    <img src="<{$arrPageData.system_images}>un_check.png" alt="<{$smarty.const.HEAD_PUBLISH}>" title="<{$smarty.const.HEAD_PUBLISH}>" />
                </a>
<{/if}>
            </td>
            <td><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>"><{$items[i].title}> <{if !empty($items[i].descr)}>(<{$items[i].descr}>)<{/if}></a></td>
            <td  align="center" >
                <a href="/admin.php?module=attributes&gid=<{$items[i].id}>" title="<{$smarty.const.HEAD_ADD_VIEW_SUB_PAGES}>">
                    <img src="<{$arrPageData.system_images}>add_tree.png" alt="<{$smarty.const.HEAD_ADD_VIEW_SUB_PAGES}>" title="<{$smarty.const.HEAD_ADD_VIEW_SUB_PAGES}>" />
                </a>
                <{if !empty($items[i].attributes)}><small class="subchildrens"><{$items[i].attributes|count}></small><{/if}>
            </td>
            <td align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td  align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td  align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" <{if !empty($items[i].attributes)}>onclick="return confirm('Данная группа атрибутов связана с <{$items[i].attributes|@count}> атрибутами, <{$items[i].filters|@count}> фильтрами и <{$items[i].products|@count}> товарами. Все связанные записи будут удалены. Продолжить?');"<{/if}> title="<{$smarty.const.LABEL_DELETE}>">
                   <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                </a>
            </td>
        </tr>
<{/section}>
    </table>
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
<{/if}>
</div>
