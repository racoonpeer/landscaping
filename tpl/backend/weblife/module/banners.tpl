<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.BANNERS 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_BANNER
          edit_title=$smarty.const.ADMIN_EDIT_BANNER
}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

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
            <li><a href="javascript:void(0);" data-target="settings">Настройки</a></li>
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
                        <td id="headb" align="left"><{$smarty.const.HEAD_TITLE_REDIRECT}></td>
                        <td>    
                            <table border="0" cellspacing="0" cellpadding="0" class="list">
                                <tr>
                                    <td align="left"><{$smarty.const.HEAD_REDIRECT_LINK}></td>
                                    <td align="center">или</td>
                                    <td align="center"><{$smarty.const.HEAD_EXTERNAL_LINK}></td>
                                </tr>
                                <tr>
                                    <td>
                                        <select name="redirectid" class="field" <{if !empty($item.redirecturl)}> disabled<{/if}>>
                                            <option value="">- - <{$smarty.const.HEAD_SELECT_REDIRECT_LINK}> - -</option>
<{section name=i loop=$categoryTree}>
                                            <option value="<{$categoryTree[i].id}>"
                                                    <{if $item.redirectid==$categoryTree[i].id}>  selected<{/if}>
                                                    <{if $categoryTree[i].id==$item.id}> disabled<{/if}>>
                                                <{$categoryTree[i].margin}>
                                                <{$categoryTree[i].title}> &nbsp; 
                                                ( <{if $categoryTree[i].active==0}><{$smarty.const.HEAD_INACTIVE}>, <{/if}>
                                                  <{$categoryTree[i].menutitle|lower}> ) &nbsp; 
                                            </option>
<{if !empty($categoryTree[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/depends_tree_childrens.tpl' itemID=$item.id dependID=$item.redirectid arrChildrens=$categoryTree[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                                        </select>
                                    </td>
                                    <td align="center">
                                        <input id="redirectype" name="redirectype" 
                                               type="checkbox" value="1" class="field"
                                               onclick="manageSelections(this, this.form.redirectid, this.form.redirecturl);"
                                               <{if !empty($item.redirecturl)}> checked<{/if}> />
                                   </td>
                                   <td align="center">
                                        <input id="redirecturl" name="redirecturl" type="text" size="70"
                                               value="<{$item.redirecturl}>"  class="field"
                                               <{if empty($item.redirecturl)}> disabled<{/if}> />
                                   </td>
                                </tr>
                            </table>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_POSITION}></td>
                        <td>
                            <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
                                <tr>
                                    <td id="head" align="left"><{$smarty.const.HEAD_POSITION}> <font style="color:red">*</font></td>
                                    <td id="head" align="left"><{$smarty.const.HEAD_MODULE}> <font style="color:red">*</font></td>
                                    <td id="head" align="left"><{$smarty.const.HEAD_TARGET}></td>
                                </tr>
                                <tr>
                                    <td align="left">
                                        <select name="position" class="field">
<{foreach name=i from=$arrPageData.arPositions key=iKey item=iItem}>
                                            <option value="<{$iKey}>"<{if $item.position==$iKey OR (empty($item.position) && $arrPageData.posid==$iKey)}>  selected<{/if}>><{$iItem}></option>
<{/foreach}>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <select name="module" class="field" onchange="moduleManager(this.value);">
<{foreach name=i from=$arrPageData.arModules key=iKey item=iItem}>
                                            <option value="<{$iKey}>"<{if $item.module==$iKey OR (empty($item.module) && $arrPageData.modname==$iKey)}>  selected<{/if}>> &nbsp; <{$iItem}> &nbsp; </option>
<{/foreach}>
                                        </select>
                                    </td>
                                    <td align="left">
                                        <select name="target" class="field">
<{foreach name=i from=$arrPageData.arTargets key=iKey item=iItem}>
                                            <option value="<{$iKey}>"<{if $item.target==$iKey}>  selected<{/if}>> &nbsp; <{$iItem}> &nbsp; </option>
<{/foreach}>
                                        </select>
                                    </td>
                            </table>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<!-- ++++++++++ Start Attach Files ++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/attach_files.tpl' item=$item attachFile=false attachImages=true}>
<!-- ++++++++++ End Attach Files ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <tr id="textBlock">
                        <td colspan="2" align="left">  
                            <strong><{$smarty.const.HEAD_CONTENT}></strong>
                            <textarea style="width:840px; height: 80px;" name="customcode" ><{$item.customcode}></textarea>
                        </td>
                        <td class="buttons_row"></td>
                    </tr> 
                </table>
            </li>
            <li id="tab_settings">
                <table border="1" cellspacing="0" cellpadding="1" class="list">   
                    <tr>
                        <td valign="top" align="left" width="420"> 
                            <strong><{$smarty.const.HEAD_SIGNIFICANCE}></strong><br/><br/>
                            <div class="inline" style="width: 200px;"><{$smarty.const.HEAD_WEIGHT}> (<{$smarty.const.HEAD_PRIORITY|lower}>)</div>
                            <input id="weight" name="weight" type="text" value="<{if $item.weight>0}><{$item.weight}><{/if}>" class="field" size="5" />
                            &nbsp;| <{$smarty.const.LABEL_EXAMPLE}>: 700<br/><br/>
                            
                            <strong><{$smarty.const.HEAD_HITS}></strong><br/><br/>
                            <div class="inline" style="width: 200px;"><{$smarty.const.HEAD_COUNT_HITS}></div>
                            <select name="countviews" class="field" onchange="changeParams(this, 'views');">
                                <option value="1"> <{$smarty.const.OPTION_YES}> </option>
                                <option value="0"<{if $item.countviews==0}> selected<{/if}>> 
                                    <{$smarty.const.OPTION_NO}> 
                                </option>
                            </select><br/><br/>

                            <div class="inline" style="width: 200px;"><{$smarty.const.HEAD_MAX_HITS}></div>
                            <input id="views" name="views" type="hidden" value="<{if $item.views>0}><{$item.views}><{else}>0<{/if}>" />
                            <input id="maxviews" name="maxviews" type="text" value="<{if $item.maxviews>0}><{$item.maxviews}><{/if}>" class="field" size="5"<{if empty($item.countviews)}> readonly<{/if}> />
                            &nbsp;| <b><{$item.views}></b> <label><{$smarty.const.LABEL_RESET}>: <input id="reset_maxviews" name="reset[views]" type="checkbox" value="1"<{if empty($item.views)}> disabled<{/if}> /></label>
                            <br/><br/>

                            <strong><{$smarty.const.HEAD_CLICKS}></strong><br/><br/>
                            <div class="inline" style="width: 200px;"><{$smarty.const.HEAD_COUNT_CLICKS}></div>
                            <select name="countclicks" class="field" onchange="changeParams(this, 'clicks');">
                                <option value="1"> <{$smarty.const.OPTION_YES}> </option>
                                <option value="0"<{if $item.countclicks==0}> selected<{/if}>> <{$smarty.const.OPTION_NO}> </option>
                            </select><br/><br/>
                            
                            <div class="inline" style="width: 200px;"><{$smarty.const.HEAD_MAX_CLICKS}></div>
                            <input id="clicks" name="clicks" type="hidden" value="<{if $item.clicks>0}><{$item.clicks}><{else}>0<{/if}>" />
                            <input name="maxclicks" type="text" id="maxclicks" value="<{if $item.maxclicks>0}><{$item.maxclicks}><{/if}>" class="field" size="5"<{if empty($item.countclicks)}> readonly<{/if}> />
                            &nbsp;| <b><{$item.clicks}></b> <label><{$smarty.const.LABEL_RESET}>: <input id="reset_maxclicks" name="reset[clicks]" type="checkbox" value="1"<{if empty($item.views)}> disabled<{/if}> /></label>
                        </td>
                        
                        <td align="left">
                            <strong><{$smarty.const.HEAD_AVAILABLE_ON_PAGES}></strong><br/><br/>
                            <label class="lbl" style="text-align:left;"><input type="radio" onclick="changeDisabledSelections(true);" value="all" name="page_selector" id="page_selector_all" checked /> <{$smarty.const.LABEL_ALL}> </label>&nbsp; 
                            <label class="lbl" style="text-align:left;"><input type="radio" onclick="enableSelections();" value="selected" name="page_selector" id="page_selector_selected"> <{$smarty.const.HEAD_SELECT_FROM_LIST}> </label>&nbsp; 
                            <select id="selections" class="inputbox" name="cids[]" multiple="multiple" style="border-top:1px solid #DCDEDF; width:100%; height:227px !important; overflow:hidden;">
<{section name=i loop=$categoryTree}>
                                <option value="<{$categoryTree[i].id}>"
                                        <{if in_array($categoryTree[i].id, $item.cids)}>  selected<{/if}>>
                                    <{$categoryTree[i].margin}><{$categoryTree[i].title}> &nbsp; 
                                    ( <{if $categoryTree[i].active==0}><{$smarty.const.HEAD_INACTIVE}>,
                                    <{/if}><{$categoryTree[i].menutitle|lower}> ) &nbsp; 
                               </option>
<{if !empty($categoryTree[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens_depends.tpl' dependIDS=$item.cids arrChildrens=$categoryTree[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                            </select>
                            </fieldset>
                                
                            <div class="noticePanel">
                                <span class="required">*</span> - Поля, обязательные для заполнения.
                                <div style="text-align:left;padding-top:10px;word-wrap: break-word; ">
                                    <b>Ссылки для флеш:</b><br/>
                                    &nbsp; а) <u>Без фиксации клика:</u> [ <{$Banners->makeItemLink($item)}> ]<br/>
                                    &nbsp; б) <u>С фиксацией клика:</u> &nbsp;[ <{$Banners->makeAccountClickURL(0, $item.id, $Banners->makeItemLink($item))}> ]
                                </div>
                            </div>   
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                </table>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>


</form>
                     
<script type="text/javascript">
<!--
$(document).ready(function() {
<{if empty($item.cids)}>
    changeDisabledSelections(true);
<{else}>
    //Установить кнопку переключатель на выбраные
    var e = document.getElementById('page_selector_selected');
    e.checked = true;
<{/if}>
    moduleManager('<{$item.module}>');
});

    function formCheck(form){
        if(form.title.value.length==0) {
            alert('<{$smarty.const.BANNER_TITLE_EMPTY}>');
            form.title.focus();
            return false;
        } else if(form.position.value==0) {
            form.position.focus();
            alert('<{$smarty.const.BANNER_POSITION_EMPTY}>');
            return false;
        } else if(form.module.value.length==0) {
            form.module.focus();
            alert('<{$smarty.const.BANNER_MODULE_EMPTY}>');
            return false;
        }
        return true;
    }
    function changeDisabledSelections(bSelect) {
        var e = document.getElementById('selections');
        var i = 0;
        var n = e.options.length;
        e.disabled = true;
        for (i = 0; i < n; i++) {
            e.options[i].disabled = true;
            e.options[i].selected = bSelect;
        }
    }
    function enableSelections() {
        var e = document.getElementById('selections');
        var i = 0;
        var n = e.options.length;
        e.disabled = false;
        for (i = 0; i < n; i++) {
            e.options[i].disabled = false;
        }
    }
    function changeParams(select, id){
        if(select.value==1){
            $('#'+'max'+id).removeAttr('readonly').focus();
            if($('#'+id).val()>0) $('#'+'reset_max'+id).removeAttr('disabled').attr('checked', 'checked');
        } else {
            $('#'+'max'+id).val('');
            $('#'+'max'+id).attr('readonly', 'readonly');
            $('#'+'reset_max'+id).removeAttr('checked');
        }
    }
    function moduleManager(module){
        switch(module){
            case 'image':
                itemsShowHide(new Array('imageBlock'));
                break;
            case 'text':
                itemsShowHide(new Array('textBlock'));
                break;
            case 'image_text':
                itemsShowHide(new Array('imageBlock', 'textBlock'));
                break;
            default:
                itemsShowHide(new Array('imageBlock'));
                break;
        }
    }
    function itemsShowHide(arrDisplay) {
        var bts = new Array('imageBlock', 'textBlock');
        if(bts.length > 0){
            for(var i=0; i<bts.length; i++){
                var display = 'none';
                if(arrDisplay instanceof Array && arrDisplay.length > 0){
                    for(var j=0; j<arrDisplay.length && display=='none'; j++){
                        if(bts[i]==arrDisplay[j]) display = '';
                    }
                }
                if(document.getElementById(bts[i]))
                    document.getElementById(bts[i]).style.display = display;
            }
        }
    }
//-->
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<br/>    
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_BANNER}>
<div class="filter_box">
    <form action="" method="get" name="filterForm">
        <{$smarty.const.LABEL_FILTER}>: &nbsp;
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        <select name="posid" onchange="this.form.submit()">
    <{foreach name=i from=$arrPageData.arPositions key=iKey item=iItem}>
            <option value="<{$iKey}>"<{if $arrPageData.posid==$iKey}>  selected<{/if}>><{$iItem}></option>
    <{/foreach}>
        </select>
        &nbsp;&nbsp;&nbsp;
        <select name="modname" onchange="this.form.submit()">
    <{foreach name=i from=$arrPageData.arModules key=iKey item=iItem}>
            <option value="<{$iKey}>"<{if $arrPageData.modname==$iKey}>  selected<{/if}>> &nbsp; <{$iItem}> &nbsp; </option>
    <{/foreach}>
        </select>
    </form>
</div><br/>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="38"></td>
            <td id="headb" align="center" width="30"></td>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="left" width="50"><{$smarty.const.HEAD_CATEGORY}></td>
            <td id="headb" align="center" width="85"><{$smarty.const.HEAD_POSITION}></td>
            <td id="headb" align="center" width="40"><{$smarty.const.HEAD_MODULE}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            <{*
            <td id="headb" class="hidden" align="center" width="95"><{$smarty.const.HEAD_DATE_ADDED}></td>
            <td id="headb" class="hidden" align="center" width="30"><{$smarty.const.HEAD_HITS}></td>
            <td id="headb" class="hidden" align="center" width="30"><{$smarty.const.HEAD_CLICKS}></td>
            *}>
        </tr>
<{section name=i loop=$items}>
         <tr>
            <td align="center">
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
            <td align="center"><{$items[i].cids}></td>
            <td align="center"><{$items[i].ptitle}></td>
            <td align="center"><{$items[i].mtitle}></td>
            <td align="center">
                <input type="hidden" name="arItems[<{$items[i].id}>]" value="1" />
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                   <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                </a>
            </td>
            <{*
            <td class="hidden" align="center"><{if $items[i].countviews}><{$items[i].views}><{else}>-<{/if}></td>
            <td class="hidden" align="center"><{if $items[i].countclicks}><{$items[i].clicks}><{else}>-<{/if}></td>
            <td class="hidden" align="center"><{$items[i].created|date_format:"%d.%m.%y %H:%M:%S"}></td>
            *}>
        </tr>
<{/section}>
    </table>

    <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td align="center" width="247"></td>
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
