<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.CATALOGS creat_title=$smarty.const.ADMIN_CREATING_NEW_PRODUCT edit_title=$smarty.const.ADMIN_EDIT_PRODUCT}>
<{include file='common/left_menu.tpl' dependID=0 categoryTree=$categoryTree islist=true admin_url='admin.php?module=catalog'}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
   
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
<{if empty($categoryTree) && ($arrPageData.cid OR $item.cid)}>
    <input type="hidden" name="cid" value="<{if $item.cid}><{$item.cid}><{else}><{$arrPageData.cid}><{/if}>" />
<{/if}>
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
                        <td colspan="2">
                            <input type="hidden" value="<{if $item.pid}><{$item.pid}><{/if}>" name="pid" id="pid"/>
                            <input id="stext" size="110" type="text" name="stext" value="" placeholder="введите название или артикул для выбора"/> 
                        </td>
                        <td class="buttons_row" valign="top" width="150" align="center">
                            <div class="buttons_list">
                                <input class="buttons" name="submit" type="submit" value="<{$smarty.const.BUTTON_SAVE}>" />
                                <input class="buttons" name="submit_apply" type="submit" value="<{$smarty.const.BUTTON_APPLY}>"  />
<{if $arrPageData.task=='addItem'}>
                                <input class="buttons" name="submit_add" type="submit" value="<{$smarty.const.BUTTON_SAVE_ADD}>"  />
                                <input class="buttons" name="submit_clear" type="reset" value="<{$smarty.const.BUTTON_CLEAR}>" onclick="return userConfirm('clear', '<{$smarty.const.CONFIRM_EMPTY}>')" />
<{/if}>
                                <input class="buttons" name="submit_cancel" type="submit" value="<{$smarty.const.BUTTON_CANCEL}>" onclick="return userConfirm('cancel', '<{$smarty.const.CONFIRM_NOT_SAVE}>')" />

<{if $arrPageData.task=='editItem' && (!isset($item.module) || (array_key_exists($item.module, $arrModules) && $item.id != $arrModules[$item.module].id)) }>
                                <input class="buttons" name="submit_delete" type="submit" value="<{$smarty.const.BUTTON_DELETE}>" onclick="return userConfirm('delete', 'Удалить запись для текущего языка?')" />
                                <input class="buttons" name="submit_delete" type="submit" value="<{$smarty.const.BUTTON_DELETE}> со всех языков" onclick="return userConfirm('deleteall', '<{$smarty.const.CONFIRM_DELETE}>')" />
<{/if}>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td id="headb" align="left" width="110">Объект</td>
                        <td>
                            <div id="object">
<{if $item.id}>
                                    <{$item.title}>
                                    <a target="_blank" href="admin.php?module=<{$arrPageData.object_module}>&task=editItem&itemID=<{$item.pid}>" >
                                        <img width="15" src="/images/operation/edit.png"/>
                                    </a>
<{/if}>
                            </div>
                        </td>
                        <td class="buttons_row" width="145"></td>
                    </tr>
                    
                    <tr>
                        <td id="headb" align="left" valign="top" width="100"></td>
                        <td valign="top">
                            <label><input name="allLangs" type="checkbox" <{if $arrPageData.task=="addItem"}>checked<{/if}>/> применить ко всем языкам</label>
                        </td>
                        <td class="buttons_row" width="145"></td>
                    </tr>
                    
<{if !empty($categoryTree)}>
                        <tr>
                            <td id="headb" align="left"><{$smarty.const.HEAD_CATEGORY}></td>
                            <td align="left">
                                <select id="cid" name="cid" onchange="updateShortcuts();">
                                    <option value="0">выберите категорию</option>
<{section name=i loop=$categoryTree}>
                                    <option value="<{$categoryTree[i].id}>" <{if $item.cid==$categoryTree[i].id OR (empty($item.cid) && $arrPageData.cid==$categoryTree[i].id)}>  selected<{/if}>><{$categoryTree[i].margin}><{$categoryTree[i].title}> </option>
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
<{/if}>
                    
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
                </table>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
        </ul>
    </div>
</form>

<script type="text/javascript">
    function formCheck(form) {
        return true;
    }
    function userConfirm(task, message) {
        if(window.confirm(message)) {
            switch (task) {
              case 'copy':
                window.location='<{$arrPageData.current_url|cat:"&task=addItem&copyID="|cat:$item.id}>';
                break
              case 'clear':
                document.forms[0].reset();
                break
              case 'cancel':
                window.location='/admin.php?module=catalog';
                break
              case 'delete':
                window.location='<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$item.id}>';
                break
              case 'deleteall':
                window.location='<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$item.id}>&allLangs=true';
                break
            }   
        }
        return false; 
    }
    function updateShortcuts() {
        if($('#cid').val()) {
            $('#messages').text('');
            $('#messages').addClass('hidden_block');
            $.ajax({
                url: "/interactive/ajax.php",
                type: 'GET',
                dataType: "json",
                data: {
                    zone: 'admin',
                    action: 'updateShortcuts',
                    object_module: "<{$arrPageData.object_module}>",
                    cid: $('#cid').val(),
                    pid: $('#pid').val()
                },
                success: function(json) {
                    if(json && !json.enable) {
                        $('#messages').removeClass('hidden_block');
                        $('#messages').addClass('error');
                        $('#messages').text('Невозможно добавить выбранный объект как ярлык в данной категории, так как он уже существует');
                    }
                }
            });
        }
    }
    $("#stext").autocomplete({
        minLength: 3,
        source: function(request, response) {
            $.ajax({
                url: "/interactive/ajax.php",
                type: 'GET',
                dataType: "json",
                data: {
                    zone: 'admin',
                    action: 'updateShortcuts',
                    stext: request.term,
                    object_module: "<{$arrPageData.object_module}>",
                    cid: $('#cid').val(),
                    pid: $('#pid').val()
                },
                success: function(json) {
                    if(json.items) {
                        response($.map(json.items, function(item) {
                            return {
                                label: item.title + ' (' + item.ctitle + ')',
                                value: item.title,
                                id: item.id
                            }
                        }));
                    }
                }
            });
        },
        select: function(event, ui) {
            $('#messages').text('');
            $('#messages').addClass('hidden_block');
            $('#pid').val(ui.item.id);
            $('#object').html(ui.item.value+' <a href="admin.php?module=<{$arrPageData.object_module}>&task=editItem&itemID='+ui.item.id+'" ><img width="15" src="/images/operation/edit.png"/></a>');
           $(this).val("");
           return false;
        }
    });
</script>
<{/if}>
</div>
