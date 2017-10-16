<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.PRODUCT_OPTIONS creat_title=$smarty.const.ADMIN_CREATING_NEW_OPTION edit_title=$smarty.const.ADMIN_EDIT_OPTION}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<script type="text/javascript">
<!--
    $(function() {
        $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
        $(document).keypress(function(e){
            if (e.which == 13 && $('#optValue').val().length>0){
                addOptValue();
                return false;
            }
        });
        $('#optValue').autocomplete({
            source: function(request, response) {
                var arrValues = {};
                $.each($('ul.sortable').find('li').find('.field'), function() {
                    if($(this).val().indexOf(request.term)!=-1) {
                        arrValues[$(this).attr('name')] = $(this).val();
                    }
                });
                response($.map(arrValues, function(item, i) {
                    return {
                        label: item,
                        value: item,
                        name: i
                    }
                }));
            },
            select: function(event, ui) {
                $('ul.sortable').scrollTop($('ul.sortable').find('input[name="'+ui.item.name+'"]').position().top);
                $('ul.sortable').find('input[name="'+ui.item.name+'"]').focus();
                $(this).val("");
                return false;
            },
            minLength: 2
        });
    });
    
    function formCheck(form){
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
        return true;
    }
    
    function addOptValue() {
        if( $('#optType option:selected').val()==2 && !isNumber($('#optValue').val())){
            $('#tip').text('Введите число или измените тип на "Текстовый"');
            $('#optValue').addClass('error');
            return false;
        } else {
            if($('#optValue').val().length>0) {
                $('#optValue').removeClass('error');
                var maxID = <{if isset($item.arValuesMaxID)}><{$item.arValuesMaxID}> + <{/if}>$('ul.sortable').find('li').length;
                var html = '<li class="ui-state-default optsort">'+
                           '<input type="hidden" name="arValues['+maxID+'][id]" value=""/>'+
                           '<input name="arValues['+maxID+'][value]" class="left field" type="text" value="'+$('#optValue').val()+'"/>'+
                           '<input type="file" name="arValues['+maxID+'][image]" value=""/>'+ 
                           '<a class="right" href="javascript:void(0)" onclick="removeOptVal(this);">'+
                           '<img src="images/admin/error.png"/></a>'+
                           '<img class="right" title="Нажмите и перетащите элемент на новое место в списке" src="images/sort.png"/>'+
                           '<div class="clear"></div>'+
                           '</li>';                           
                $('ul.sortable').append(html);  
                $('#optValue').val('');
                $('#tip').text('');
                $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
            }
        }   
    }
       
    function removeOptVal(item, removeAll) {
        if(typeof removeAll == 'undefined')
            $(item).parent().remove();
        else 
            $('ul.sortable').html('');
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
<{if $arrPageData.task=='editItem'}>
            <li><a href="javascript:void(0);" data-target="values">Значения</a></li>
<{/if}>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">      
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PUBLISH_PAGE}></td>
                        <td  align="left">
                            <input type="radio" name="active" value="1" <{if $item.active==1}>checked<{/if}>>
                            <{$smarty.const.OPTION_YES}>
                            <input type="radio" name="active" value="0" <{if $item.active==0}>checked<{/if}>>
                            <{$smarty.const.OPTION_NO}>
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>

                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_SHOW_IN_CART}></td>
                        <td  align="left">
                            <input type="radio" name="basket" value="1" <{if $item.basket==1}>checked<{/if}>>
                            <{$smarty.const.OPTION_YES}>
                            <input type="radio" name="basket" value="0" <{if $item.basket==0}>checked<{/if}>>
                            <{$smarty.const.OPTION_NO}>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>

                    <tr>
                        <td id="headb" align="left" width="175"><{$smarty.const.HEAD_TYPE}> <font style="color:red">*</font></td>
                        <td>
                            <select name="type_id" id="type_id">
<{section name=i loop=$arrPageData.types}>
                                <option value="<{$arrPageData.types[i].id}>" <{if $arrPageData.types[i].id==$item.type_id}>selected<{/if}>><{$arrPageData.types[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>

                    <tr>
                        <td id="headb" align="left" width="175"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td>
                            <input class="field" name="title" size="100" id="title" type="text" value="<{$item.title}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>

                    <tr>
                        <td id="headb" align="left" width="175"><{$smarty.const.HEAD_SHORT_TITLE}> <font style="color:red">*</font></td>
                        <td>
                            <input class="field" name="stitle" size="100" id="stitle" type="text" value="<{$item.stitle}>" />
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
                </table>
            </li>
<{if $arrPageData.task=='editItem'}>
            <li id="tab_values">
                <br/><strong>Настройка значений опции</strong><br><br/>
                <div id="tip"></div>
                <div class="left"><input id="optValue" style="margin-top:5px; height:24px; padding-left:5px;" type="text" value="" placeholder="введите значение" class="nosize_field" size="120"/>&nbsp;&nbsp;&nbsp;</div>
                <div class="left"><input type="button" class="buttons" value="Добавить" onclick="addOptValue();"/></div>
                <div class="clear"></div> 
                <br/><strong>Значения опции:</strong> <a href="javascript:void(0)" onclick="removeOptVal(this, 'all');">Очистить список</a>
                <div class="sortable-wrapper" style="width:100%">
                    <ul class="sortable" id="defaultVals">
<{if !empty($item.arValues)}>
<{section loop=$item.arValues name=i}>
                        <li class="ui-state-default optsort">
                            <input type="hidden" name="arValues[<{$item.arValues[i].id}>][id]" value="<{$item.arValues[i].id}>"/>
                            <input class="left field" type="text" name="arValues[<{$item.arValues[i].id}>][value]" value="<{$item.arValues[i].title}>" <{if !$item.arValues[i].edit}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input type="file" name="arValues[<{$item.arValues[i].id}>][image]" value="" style="margin-top:4px"/>
<{if !empty($item.arValues[i].image)}>
                                <img src="<{$arrPageData.files_url|cat:$item.arValues[i].image}>" style="max-width:20px; max-height:20px;"/>
                                <input type="checkbox" name="arValues[<{$item.arValues[i].id}>][delete_image]" value="1"/> удалить 
<{/if}>
<{if $item.arValues[i].edit}>
                                <a class="right" href="javascript:void(0)" onclick="removeOptVal(this);"><img src="images/admin/error.png"/></a>&nbsp;
<{/if}>
                            <img class="right" src="images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" <{if !$item.arValues[i].edit}>readonly title="недоступно для редактирования, так как используется в товаре" style="margin-right:35px;"<{/if}>/>
                            <div class="clear"></div>
                        </li>
<{/section}>
<{/if}>
                    </ul>
                </div>
            </li>
<{/if}>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_OPTION}>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="38"></td>
            <td id="headb" align="center" width="30"></td>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
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
            <td align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{if $items[i].cnt>0}>Данная опция связана с <{$items[i].cnt}> товарами. Все записи будут удалены. Продолжить?<{else}><{$smarty.const.CONFIRM_DELETE}><{/if}>');" title="<{$smarty.const.LABEL_DELETE}>">
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