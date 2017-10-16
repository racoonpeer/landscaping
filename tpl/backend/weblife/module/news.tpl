<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.NEWS 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_NEWS 
          edit_title=$smarty.const.ADMIN_EDIT_NEWS 
}>

<{include file='common/left_menu.tpl' dependID=0 categoryTree=$categoryTree islist=true}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<script type="text/javascript">
<!--
    function formCheck(form){
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        if(form.cid && form.cid.value == 0){
           alert('<{$smarty.const.LABEL_SELECT_CATEGORY}>'); 
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

    <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
    <input type="hidden" name="order"   value="<{$item.order}>"   />
    
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="seo">SEO</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td>
                            <input class="left" name="title" size="70" id="title" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.title}>" /> <input type="button" class="buttons left" value="Изменить SEO путь" onclick="MoveToSeoPath();"/>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Подзаголовок</td>
                        <td align="left">
                            <input class="left" name="subtitle" size="95" id="subtitle" type="text" value="<{$item.subtitle}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
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
<{if !empty($categoryTree)}>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_CATEGORY}></td>
                        <td align="left">
                            <select class="field" name="cid"<{if !empty($item.cid) OR !empty($arrPageData.cid)}> 
                                    onchange="hideApplyBut(this, this.form.submit_apply, 
                                    <{if !empty($item.cid)}><{$item.cid}><{else}><{$arrPageData.cid}><{/if}>);"<{/if}>>
<{section name=i loop=$categoryTree}>
                                <option value="<{$categoryTree[i].id}>"
                                    <{if $item.cid==$categoryTree[i].id OR (empty($item.cid) && 
                                         $arrPageData.cid==$categoryTree[i].id)}>selected
                                    <{/if}>>
                                    <{$categoryTree[i].margin}>
                                    <{$categoryTree[i].title}> &nbsp; [<{$smarty.const.HEAD_ITEMS}>: 
                                    <{if isset($arCidCntItems[$categoryTree[i].id])}>
                                        <{$arCidCntItems[$categoryTree[i].id]}>
                                    <{else}>0<{/if}>] &nbsp; 
                                    <{if $categoryTree[i].active==0}>( <{$smarty.const.HEAD_INACTIVE}> ) &nbsp; <{/if}>
                                </option>
<{if !empty($categoryTree[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens.tpl' dependID=$item.cid arrChildrens=$categoryTree[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{elseif $arrPageData.cid OR $item.cid}>
                    <input type="hidden" name="cid" value="<{if $item.cid}><{$item.cid}><{else}><{$arrPageData.cid}><{/if}>" />
<{/if}>
                    <!-- ++++++++++ Start Attach Files ++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/attach_files.tpl' item=$item attachFile=false attachImages=true}>
                    <!-- ++++++++++ End Attach Files ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <tr>
                        <td colspan="2">  
                            <strong><{$smarty.const.HEAD_SHORT_CONTENT}></strong>
                            <a href="javascript:toggleEditor('description');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                            <textarea style="width:<{if !empty($categoryTree)}>640<{else}>840<{/if}>px; height: 100px;" id="description" name="descr" ><{$item.descr}></textarea>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>   
                    
                    <tr>
                        <td colspan="2">  
                            <strong><{$smarty.const.HEAD_CONTENT}></strong>
                            <a href="javascript:toggleEditor('fulldescription');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                            <textarea style="width:<{if !empty($categoryTree)}>640<{else}>840<{/if}>px; height: 500px;" id="fulldescription" name="fulldescr" ><{$item.fulldescr}></textarea>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>   
                </table>
            </li>    
            <li id="tab_seo">
                <table width="101%" border="0" cellspacing="0" cellpadding="0" class="list" > 
<!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/meta_seo_data.tpl'}>
<!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ --> 
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
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_NEWS}>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="38"></td>
            <td id="headb" align="center" width="30"></td>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="center" width="100">категория</td>
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
            <td align="center" valign="center" style="height:30px; width: 30px;"><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><img style="max-width:30px; max-height:30px;" src="<{if $items[i].image}><{$arrPageData.files_url|cat:$items[i].image}><{else}><{$arrPageData.files_url|cat:'noimage.jpg'}><{/if}>" /></a></td>
            <td ><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a></td>
            <td align="center"><{$items[i].category}></td>
            <td  align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
<{*if $arrPageData.cid!=$items[i].cid}>
            <td ><{$items[i].cat_title}></td>
<{/if*}>
            <td  align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td  align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
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