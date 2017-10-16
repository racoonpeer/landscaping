<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.ATTRIBUTES creat_title=$smarty.const.ADMIN_CREATING_NEW_ATTR edit_title=$smarty.const.ADMIN_EDIT_ATTR}>
<{if !empty($categoryTree)}>
    <div id="left_block">
        <ul class="filetree category_tree">
            <li>
                <a href="<{$arrPageData.admin_url}>">&nbsp;<img src="/images/admin/treeview/folder.png" /> &nbsp;Все группы</a>
                <ul>
<{section name=i loop=$categoryTree}>
                        <li class="<{if $arrPageData.GID == $categoryTree[i].id}>active collapsable<{/if}>">
                            &nbsp; <img src="/images/admin/treeview/folder-closed.png" />  &nbsp;
                            <a href="/admin.php?module=attributes&gid=<{$categoryTree[i].id}>"><{$categoryTree[i].title}></a>
                            <a href="/admin.php?module=attribute_groups&task=editItem&itemID=<{$categoryTree[i].id}>">
                                <img src="/images/operation/edit.png" height="10"/>
                            </a>
<{if !empty($categoryTree[i].children)}>
                                <ul>
<{section name=j loop=$categoryTree[i].children}>
                                    <li>
                                        <a href="/admin.php?module=attributes&task=editItem&itemID=<{$categoryTree[i].children[j].id}>"><{$categoryTree[i].children[j].title}></a>
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
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="order"   value="<{$item.order}>"   />
    <input type="hidden" name="arAttrValues"  id="arAttrValues" value=""   />
    
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="defaults">Значения атрибутов</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                    <tr>
                        <td id="headb" align="left" width="175"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td  >
                            <input class="field" size="70" name="title" id="title" type="text" value="<{$item.title}>" />
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>

                    <tr>
                        <td id="headb" align="left" width="175">Краткое описание<br/>(только для админзоны)</td>
                        <td  >
                            <input class="field" size="70" name="descr" id="descr" type="text" value="<{$item.descr}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.LABEL_GROUP}> <font style="color:red">*</font></td>
                        <td  align="left">
                            <select name="gid" class="nosize_field">
                                <option value=""> -- Выберите -- </option>
<{section name=i loop=$arrPageData.arGroups}>
                                <option value="<{$arrPageData.arGroups[i].id}>" <{if $item.gid==$arrPageData.arGroups[i].id || ($arrPageData.task=='addItem' && $arrPageData.arGroups[i].id==$arrPageData.GID)}>selected<{/if}>><{$arrPageData.arGroups[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>

                    <tr>
                        <td id="headb" align="left"><{$smarty.const.LABEL_TYPE}> <font style="color:red">*</font></td>
                        <td  align="left">
                            <select id="attrType" name="tid" class="nosize_field">
                                <option value=""> -- Выберите -- </option>
<{section name=i loop=$arrPageData.arTypes}>
                                <option value="<{$arrPageData.arTypes[i].id}>" <{if $item.tid==$arrPageData.arTypes[i].id}>selected<{/if}>><{$arrPageData.arTypes[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
            </li>
            <li id="tab_defaults">
                <br/><strong>Настройка допустимых значений</strong><br><br/>
                <div id="tip"></div>
                <div class="left">
                    <input id="attrValue" style="margin-top:5px; height:24px; padding-left:5px;" type="text" value="" placeholder="введите значение" class="nosize_field" size="104"/>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="left">
                    <input type="button" class="buttons" value="Добавить" onclick="addAttrValue();"/>
                </div>
                <div class="clear"></div> 
                <br/>
                <strong>Допустимые значения атрибута:</strong> <a href="javascript:void(0)" onclick="removeAttrVal(this, 'all');">Очистить список</a>
                <div class="sortable-wrapper" style="width:100%">
                    <ul class="sortable" id="defaultVals">
<{foreach name=i from=$item.arValues key=arKey item=arValue}>
                        <li class="ui-state-default attrsort">
                            <input type="hidden" name="arValues[<{$arKey}>][id]" value="<{$arKey}>"/>
                            <input class="left field" type="text" name="arValues[<{$arKey}>][value]" value="<{$arValue.title}>" style="width: 170px;" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input class="left field" type="text" name="arValues[<{$arKey}>][seo_path]" value="<{$arValue.seo_path}>" style="width: 170px;" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form['arValues[<{$arKey}>][value]'].value.length==0){alert('Вы не ввели значение атрибута!'); this.form['arValues[<{$arKey}>][value]'].focus(); return false; } else{ generateSeoPath(this.form['arValues[<{$arKey}>][seo_path]'], this.form['arValues[<{$arKey}>][value]'].value, this.form.title.value);}">
                            <input type="file" name="arValues[<{$arKey}>][image]" value="" style="margin-top:4px"/>
<{if !empty($arValue.image)}>
                                <img src="<{$arrPageData.files_url|cat:$arValue.image}>" style="max-width:20px; max-height:20px;"/>
                                <input type="checkbox" name="arValues[<{$arKey}>][delete_image]" value="1"/> удалить 
<{/if}>
<{if $arValue.edit}>
                                <a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);"><img src="images/admin/error.png"/></a>&nbsp;
<{/if}>
                            <img class="right" src="images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре" style="margin-right:35px;"<{/if}>/>
                            <div class="clear"></div>
                        </li>
<{/foreach}>
                    </ul>
                </div>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>
<script type="text/javascript">    
<!--  
    $(function() {
        $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
        $(document).keypress(function(e){
            if (e.which == 13 && $('#attrValue').val().length>0){
                addAttrValue();
                return false;
            }
        });
        $('#attrValue').autocomplete({
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
    
    function addAttrValue() {
        if( $('#attrType option:selected').val()==2 && !isNumber($('#attrValue').val())){
            $('#tip').text('Введите число или измените тип на "Текстовый"');
            $('#attrValue').addClass('error');
            return false;
        } else {
            if($('#attrValue').val().length>0) {
                $('#attrValue').removeClass('error');
                var maxID = <{if isset($item.arValuesMaxID)}><{$item.arValuesMaxID}> + <{/if}>$('ul.sortable').find('li').length;
                var html = '<li class="ui-state-default attrsort">'+
                           '<input type="hidden" name="arValues['+maxID+'][id]" value=""/>'+
                           '<input name="arValues['+maxID+'][value]" class="left field" type="text" value="'+$('#attrValue').val()+'" style="width: 170px;"/>'+
                           '<input name="arValues['+maxID+'][seo_path]" class="left field" type="text" value="" style="width: 170px;"/>'+
                           '<input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form[\'arValues['+maxID+'][value]\'].value.length==0){alert(\'Вы не ввели значение атрибута!\'); this.form[\'arValues['+maxID+'][value]\'].focus(); return false; } else{ generateSeoPath(this.form[\'arValues['+maxID+'][seo_path]\'], this.form[\'arValues['+maxID+'][value]\'].value, this.form.title.value);}">'+
                           '<input type="file" name="arValues['+maxID+'][image]" value=""/>'+ 
                           '<a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);">'+
                           '<img src="images/admin/error.png"/></a>'+
                           '<img class="right" title="Нажмите и перетащите элемент на новое место в списке" src="images/sort.png"/>'+
                           '<div class="clear"></div>'+
                           '</li>';                           
                $('ul.sortable').append(html);  
                $('#attrValue').val('');
                $('#tip').text('');
                $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
            }
        }   
    }
       
    function removeAttrVal(item, removeAll) {
        if(typeof removeAll == 'undefined')
            $(item).parent().remove();
        else 
            $('ul.sortable').html('');
    }
//-->
</script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_ATTR}>
<div class="clear"></div>
<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
<{if empty($arrPageData.GID)}>
            <td id="headb" align="center" width="200"><{$smarty.const.HEAD_GROUP}></td>
<{/if}>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>
<{section name=i loop=$items}>
         <tr>
            <td ><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}> <{if !empty($items[i].descr)}>(<{$items[i].descr}>)<{/if}></a></td>
<{if empty($arrPageData.GID)}>
            <td  align="center" width="30"><{$items[i].gtitle}></td>
<{/if}>
            <td  align="center">
                <input type="text" name="arOrder[<{$items[i].id}>]" id="arOrder_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td  align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td  align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" <{if !empty($items[i].products) OR !empty($items[i].filters)}>onclick="return confirm('Данный атрибут связан с <{$items[i].products|@count}> товарами и <{$items[i].filters|@count}> фильтрами. Все связанные записи будут удалены. Продолжить?');"<{/if}> title="<{$smarty.const.LABEL_DELETE}>">
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