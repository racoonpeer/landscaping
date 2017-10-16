<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.STOCKS creat_title=$smarty.const.ADMIN_CREATING_NEW_STOCKS edit_title=$smarty.const.ADMIN_EDIT_STOCKS}>

<script type="text/javascript">

    function formCheck(form) {
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        // related products input filling
        var arIdx = new Array();
        for (var i = 0; i < form.selected_related.length; i++) {
            arIdx.push(form.selected_related[i].value);
        }
        form.related.value = arIdx.join(',');
        
        return true;
    }
    
    $(function(){
        var InputDateStart = document.getElementById("date_start"),
            InputDateEnd = document.getElementById("date_end");
        $(InputDateStart).datepicker({
            dateFormat: "dd.mm.yy"
        });
        $(InputDateEnd).datepicker({
            dateFormat: "dd.mm.yy"
        });
    });
    
</script>

<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
    <form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
        <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
        <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
        <input type="hidden" name="order" value="<{$item.order}>"/>
        <div class="tabsContainer">
            <ul class="nav">
                <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
                <li><a href="javascript:void(0);" data-target="relations">Связи</a></li>
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
                            <td id="headb" align="left">Период действия</td>
                            <td align="left">
                                с 
                                <input type="text" name="date_start" id="date_start" value="<{$item.date_start|date_format:"%d.%m.%Y"}>"/> по 
                                <input type="text" name="date_end" id="date_end" value="<{$item.date_end|date_format:"%d.%m.%Y"}>"/>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
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
                <li id="tab_relations">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="relatedTable">
                        <tr>
                            <td colspan="4">     
                                <strong>Похожие товары</strong><br/><br/>
                                <select id="relatedCats" onchange="updateRelatedOptions(this.value);" >
                                    <option> --- Выберите категорию из списка --- </option>
<{foreach name=i from=$arrPageData.arRelatedCats key=arKey item=arItem}>
                                    <option value="<{$arItem.id}>"><{$arItem.title}></option>
<{/foreach}>
                                </select>
                            </td>
                            <td class="buttons_row" valign="top" width="145" align="center">
                                <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                                <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            </td>
                        </tr>
                        <tr valign="top">
                            <td >
                                <select onChange="this.form.related_add.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_all_related" name="all_related" class="jsSelectUtils_select"></select>
                            </td>
                            <td  valign="middle">
                                <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.all_related, 'list_settings_selected_related', 3);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &gt; &nbsp;" name="related_add" class="buttons green" style="min-width: 30px;"/>
                                <br/>
                                <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.selected_related, 'list_settings_all_related');jsSelectUtils.addExtraOptionsParams(this.form.all_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &lt; &nbsp;" name="related_del" class="buttons green" style="min-width: 30px;"/>
                            </td>
                            <td >
                                <input type="hidden" name="related" value="" />
                                <select onChange="var frm=this.form; frm.related_up.disabled=frm.related_down.disabled=frm.related_del.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.all_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_selected_related" name="selected_related" class="jsSelectUtils_select">
<{section name=i loop=$item.related}>
                                    <option value="<{$item.related[i].id}>" ondblclick="openTab('/admin.php?module=catalog&task=editItem&itemID=' + this.value);" style="color: blue; text-decoration: underline;"><{$item.related[i].title}></option>
<{/section}>
                                </select>
                            </td>
                            <td  valign="middle">
                                <input type="button" onClick="jsSelectUtils.moveOptionsUp(this.form.selected_related);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок (выше)" value="Выше" class="buttons" name="related_up" style="min-width: 30px;"/>
                                <br/>
                                <input type="button" onClick="jsSelectUtils.moveOptionsDown(this.form.selected_related);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок: ниже" value="Ниже" class="buttons" name="related_down" style="min-width: 30px;"/>
                            </td>
                            <td class="buttons_row"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0 5px;font-style:italic;">Двойной клик по элементу для перехода на товар</td>
                            <td class="buttons_row"></td>
                        </tr>
                    </table>
                    <script type="text/javascript">
                        function updateRelatedOptions(CID) {
                            CID = parseInt(CID)||false;
                            var exItems = getExItems('list_settings_selected_related', '<{$item.id}>');
                            if(CID){
                                $.ajax({
                                    url: '/interactive/ajax.php',
                                    type: 'GET',
                                    dataType: 'json',
                                    data: {
                                        zone: 'admin',
                                        action: 'getRelatedItems',
                                        module: '<{$arrPageData.module}>',
                                        exclude: exItems,
                                        cid: CID
                                    }, 
                                    success: function(json){
                                        if(json.items){
                                            var html = '';
                                            for (var i in json.items){
                                                html += '<option value="' + json.items[i].id + '" ondblclick="openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);" style="color: blue; text-decoration: underline;">' + json.items[i].title + '</option>';
                                            }
                                            $('#list_settings_all_related').html(html);
                                        }
                                    }
                                });
                            }
                        }
                        function getExItems(objID, itemID){
                            var exItems = new Array();
                            $.each($('#'+objID).children('option'), function(i, el) {
                                exItems.push($(el).val());
                            });
                            if(itemID > 0){
                                exItems.push(itemID);
                            }
                            return exItems;
                        }
                    </script>
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
    <{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_STOCK}>
    <{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
    <div class="clear"></div>
    <form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
            <tr>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="center" width="30"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                <td id="headb" align="center" width="64">дата начала</td>
                <td id="headb" align="center" width="64">дата окончания</td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
<{section name=i loop=$items}>
             <tr class="<{if $items[i].outdated}>new_comment<{/if}>">
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
                <td>
                    <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a>
                </td>
                <td align="center"><{$items[i].date_start|date_format:"%d.%m.%Y"}></td>
                <td align="center"><{$items[i].date_end|date_format:"%d.%m.%Y"}></td>
                <td align="center">
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