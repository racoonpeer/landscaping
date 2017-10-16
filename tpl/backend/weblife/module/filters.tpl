<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.FILTERS creat_title=$smarty.const.ADMIN_CREATING_NEW_FILTER edit_title=$smarty.const.ADMIN_EDIT_FILTER}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<script type="text/javascript">
    function formCheck(form){
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        return true;
    }
</script>

<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
    <input type="hidden" name="order"   value="<{$item.order}>"/>

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
                        <td id="headb" align="left" width="175"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td >
                            <input class="field" name="title" id="title" size="100" type="text" value="<{$item.title}>" />
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>

                    <tr>
                        <td id="headb" align="left"><{$smarty.const.LABEL_TYPE}></td>
                        <td  align="left">
                            <select name="tid" id="formSwitcher"  class="nosize_field">
<{section name=i loop=$arrPageData.arTypes}>
                                <option value="<{$arrPageData.arTypes[i].id}>" <{if $item.tid==$arrPageData.arTypes[i].id}>selected<{/if}>><{$arrPageData.arTypes[i].title}></option>
<{/section}>
                            </select>&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" id="rangeTrigger" class="nosize_field" name="is_range" value="1" <{if empty($item.arRanges) AND ($item.tid==1 OR $item.tid==3)}>disabled<{/if}> <{if ($item.tid==2 OR $item.tid==4) AND !empty($item.arRanges)}>checked<{/if}>/> Диапазон</label>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>

                    <tr>
                        <td colspan="2">                
                            <table id="attrTable" border="1" cellspacing="0" cellpadding="1" class="list">
                                <tr>
                                    <td id="headb" align="left" width="175">
                                        <{$smarty.const.LABEL_ATTRIBUTE}>:
                                    </td>
                                    <td >
                                        <select name="aid"  class="nosize_field">
<{section name=i loop=$arrPageData.attrGroups}>
                                            <optgroup label="<{$arrPageData.attrGroups[i].title}>">
<{section name=j loop=$arrPageData.attrGroups[i].attributes}>
                                                <option value="<{$arrPageData.attrGroups[i].attributes[j].id}>" <{if $item.aid==$arrPageData.attrGroups[i].attributes[j].id}>selected<{/if}>><{$arrPageData.attrGroups[i].attributes[j].title}></option>
<{/section}>                      
                                            </optgroup>
<{/section}>
                                        </select>
                                    </td>
                                </tr>
                            </table>

                            <table id="rangeTable" border="1" cellspacing="0" cellpadding="1" class="list">
                                <tbody>
<{if !empty($item.arRanges)}>
<{section name=i loop=$item.arRanges}>
                                    <tr id="rangeRow_<{$smarty.section.i.iteration}>">
                                        <td id="headb" align="left" width="175">
                                            <{$smarty.const.LABEL_RANGE}>:
                                        </td>
                                        <td>
                                            <input name="arRanges[<{$smarty.section.i.iteration}>][id]" type="hidden" value="<{$item.arRanges[i].id}>"/>
                                            <input class="field" name="arRanges[<{$smarty.section.i.iteration}>][order]" type="text" value="<{$item.arRanges[i].order}>" style="width: 32px;"/>
                                        </td>
                                        <td>
                                            <input class="field" name="arRanges[<{$smarty.section.i.iteration}>][title]" type="text" value="<{$item.arRanges[i].title}>" style="width: 120px;"/>
                                        </td>
                                        <td>
                                            <input class="field" name="arRanges[<{$smarty.section.i.iteration}>][vmin]" type="text" value="<{$item.arRanges[i].vmin}>" style="width: 32px;"/>&nbsp;
                                            <input class="field" name="arRanges[<{$smarty.section.i.iteration}>][vmax]" type="text" value="<{$item.arRanges[i].vmax}>" style="width: 32px;"/>
                                        </td>
                                        <td>
                                            <{$smarty.const.LABEL_MEASUREMENT_UNITS}>:&nbsp;&nbsp;&nbsp;
                                            <input class="field" name="arRanges[<{$smarty.section.i.iteration}>][unit]" type="text" value="<{$item.arRanges[i].unit}>" style="width: 32px;"/>
                                        </td>
                                        <td>
                                            <a onclick="$(this).parent().parent().remove();" title="Delete!">
                                                <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />
                                            </a>
                                        </td>
                                    </tr>
<{/section}>
<{else}>
                                    <tr id="rangeRow_1">
                                        <td id="headb" align="left" width="175">
                                            <{$smarty.const.LABEL_RANGE}>:
                                        </td>
                                        <td >
                                            <input name="arRanges[1][id]" type="hidden" value="0"/>
                                            <input class="field" name="arRanges[1][order]" type="text" value="1" style="width: 32px;"/>
                                        </td>
                                        <td >
                                            <input class="field" name="arRanges[1][title]" type="text" value="" style="width: 120px;"/>
                                        </td>
                                        <td >
                                            <input class="field" name="arRanges[1][vmin]" type="text" value="" style="width: 32px;"/>&nbsp;&nbsp;
                                            <input class="field" name="arRanges[1][vmax]" type="text" value="" style="width: 32px;"/>
                                        </td>
                                        <td >
                                            <{$smarty.const.LABEL_MEASUREMENT_UNITS}>:&nbsp;&nbsp;&nbsp;
                                            <input class="field" name="arRanges[1][unit]" type="text" value="" style="width: 32px;"/>
                                        </td>
                                        <td >
                                            <a onclick="$(this).parent().parent().remove();" title="Delete!">
                                                <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />
                                            </a>
                                        </td>
                                    </tr>
<{/if}>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="6" align="center">
                                            <br/><a href="javascript:void(0);" onclick="addRangeRow();"><{$smarty.const.BUTTON_ADD}></a><br/><br/>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>

                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr class="last">
                        <td></td>
                        <td></td>
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

<script type="text/javascript">
    $(function(){
        var Switcher = document.getElementById('formSwitcher');
        var Trigger = document.getElementById('rangeTrigger');
        var RangeTable = document.getElementById('rangeTable');
        var AttrTable = document.getElementById('attrTable');
        
<{if empty($item.arRanges) OR ($item.tid==1 OR $item.tid==3)}>
        $(RangeTable).hide();
<{/if}>
<{if $item.aid==0}>        
        $(AttrTable).hide();
<{/if}>
        
        $(Switcher).bind('change', function(){
            var Val = parseInt($(this).val()) || 0;
            switch (Val) {
                case 1:
                    Trigger.disabled = true;
                    $(RangeTable).hide();
                    $(AttrTable).hide();
                    break;
                case 2:
                    Trigger.disabled = false;
                    $(AttrTable).hide();
                    if(Trigger.checked) {
                        $(RangeTable).show();
                    } else {
                        $(RangeTable).hide();
                    }
                    break;
                case 3:
                    Trigger.disabled = true;
                    $(RangeTable).hide();
                    $(AttrTable).show();
                    break;
                case 4:
                    Trigger.disabled = false;
                    $(AttrTable).show();
                    if(Trigger.checked) {
                        $(RangeTable).show();
                    } else {
                        $(RangeTable).hide();
                    }
                    break;
                case 5:
                    Trigger.disabled = true;
                    $(RangeTable).hide();
                    $(AttrTable).hide();
                    break;
            }
        });
        
        $(Trigger).bind('click', function(){
            if(this.checked) {
                $(RangeTable).show();
            } else {
                $(RangeTable).hide();
            }
        });
        
        
    });
    
    function addRangeRow() {
        var range_row = $('table#rangeTable > tbody > tr').length;
        var target = $('table#rangeTable > tbody');
        range_row++;
        var html;
        html +=     '<tr id="rangeRow_' + range_row + '">';
        html +=     '   <td id="headb" align="left" width="175">Диапазон:</td>';
        html +=     '   <td>';
        html +=     '      <input name="arRanges[' + range_row + '][id]" type="hidden" value="0"/>';
        html +=     '      <input class="field" name="arRanges[' + range_row + '][order]" type="text" value="' + range_row + '" style="width: 32px;"/>';
        html +=     '   </td>';
        html +=     '   <td>';        
        html +=     '       <input class="field" name="arRanges[' + range_row + '][title]" type="text" value="" style="width: 120px;"/>';        
        html +=     '   </td>';
        html +=     '   <td>';
        html +=     '       <input class="field" name="arRanges[' + range_row + '][vmin]" type="text" value="" style="width: 32px;"/>&nbsp;&nbsp;';
        html +=     '       <input class="field" name="arRanges[' + range_row + '][vmax]" type="text" value="" style="width: 32px;"/>';
        html +=     '   </td>';
        html +=     '   <td>';
        html +=     '       Ед.изм:&nbsp;&nbsp;&nbsp;';
        html +=     '       <input class="field" name="arRanges[' + range_row + '][unit]" type="text" value="" style="width: 32px;"/>';
        html +=     '   </td>';    
        html +=     '   <td>';        
        html +=     '       <a onclick="$(this).parent().parent().remove();" title="Delete!">';        
        html +=     '           <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />';        
        html +=     '       </a>';        
        html +=     '   </td>';    
        html +=     '</tr>';    
        $(target).append(html);
    }
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_FILTER}>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>
<{section name=i loop=$items}>
         <tr>
            <td ><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a></td>
            <td  align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
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