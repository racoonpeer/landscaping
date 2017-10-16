<div id="sectionTitle">Сверх секретные настройки</div>
<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>

<script type="text/javascript">
<!--
    function formCheck(form){
        if($('.error', form).length) {
            return false;
        }
        return true;
    }
//-->
</script>

<div id="right_block">
<form method="post" action="<{$arrPageData.current_url}>" name="settingsForm" onSubmit="return formCheck(this);">    
    <input type="hidden" value="<{$arrPageData.tab}>" name="tab" id="currentTab"/>
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" <{if $arrPageData.tab=='main'}>class="active"<{/if}>>Настройки базы</a></li>
            <li><a href="javascript:void(0);" data-target="images" <{if $arrPageData.tab=='images'}>class="active"<{/if}>>Настройки изображений</a></li>
            <li><a href="javascript:void(0);" data-target="modules" <{if $arrPageData.tab=='modules'}>class="active"<{/if}>>Управление модулями</a></li>
            <li><a href="javascript:void(0);" data-target="users" <{if $arrPageData.tab=='users'}>class="active"<{/if}>>Управление доступами</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li <{if $arrPageData.tab=='main'}>class="active"<{/if}> id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                     <tr>
                         <td id="headb" align="left" width="175">Операции с базой</td>
                         <td  class="padding">
                             <a href="/admin.php?module=mysqldumper"><{$smarty.const.TOPLINK_MYSQLDUMPER}></a> |
                             <a href="javascript:void(0)" onclick="if(window.confirm('<{$smarty.const.LABEL_QUESTION_TO_DO}>?')) {window.location='<{$arrPageData.current_url|cat:"&task=repairDBTables"}>'}; return false;" />
                             <{$smarty.const.LABEL_REPAIR_DB_TABLES}>
                             </a> |
                             <a href="javascript:void(0)" onclick="if(window.confirm('<{$smarty.const.LABEL_QUESTION_TO_DO}>?')) {window.location='<{$arrPageData.current_url|cat:"&task=optimizeDBTables"}>'}; return false;" />
                             <{$smarty.const.LABEL_OPTIMIZE_DB_TABLES}>
                             </a>
                         </td>
                         <td class="buttons_row" valign="top" width="144">
                             <div class="buttons_list">
                                 <input name='submit' class='buttons' type='submit' value='<{$smarty.const.BUTTON_SAVE}>' onclick="return formCheck(this.form);" />
                             </div>
                         </td>
                     </tr>

                     <tr>
                         <td id="headb" align="left" width="175">Операции с шаблонами</td>
                         <td  class="padding">
                             <{if !$smarty.const.TPL_BACKEND_FORSE_COMPILE || !$smarty.const.TPL_FRONTEND_FORSE_COMPILE}>
                                 <a href="javascript:void(0)" onclick="if(window.confirm('<{$smarty.const.LABEL_QUESTION_TO_DO}>?')) {window.location='<{$arrPageData.current_url|cat:"&task=clearTemplates"}>'}; return false;">
                                     <{$smarty.const.LABEL_CLEAR_TEMPLATES}>
                                 </a> 
                             <{/if}>

                             <{if $smarty.const.TPL_BACKEND_CACHING || $smarty.const.TPL_FRONTEND_CACHING}>
                                  | <a href="javascript:void(0)" onclick="if(window.confirm('<{$smarty.const.LABEL_QUESTION_TO_DO}>?')) {window.location='<{$arrPageData.current_url|cat:"&task=clearCache"}>'}; return false;" />
                                 <{$smarty.const.LABEL_CLEAR_CASHING}>
                                 </a> 
                             <{/if}>
                         </td>
                         <td class="buttons_row" valign="top" width="144"></td>
                     </tr>

                     <tr>
                         <td id="headb" align="left" valign="top" width="175">SQL запрос</td>
                         <td  class="padding">
                             <textarea class="field" style="margin:5px 0; height:200px; resize:vertical;" rows="10" name="sql_query"><{if isset($item.sql_query)}><{$item.sql_query}><{/if}></textarea>
                         </td>
                         <td class="buttons_row" valign="top" width="144"></td>
                     </tr>                     
                 </table>
            </li>

            <li <{if $arrPageData.tab=='images'}>class="active"<{/if}> id="tab_images">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                     <tr>
                         <td colspan="2">
                            <{if !empty($item.arImgModules)}>
                                <div class="modules">
                                    <{section name=i loop=$item.arImgModules}>
                                        <div id="module_<{$item.arImgModules[i].module}>" class="module">
                                            <h3 class="left">Модуль "<{$item.arImgModules[i].title}>"</h3> 
                                            <input type="button" class="buttons right" data-module='<{$item.arImgModules[i].module}>' value="Добавить изображение" onclick="addImage(this);"/><br/>
                                            <div class="clear"></div>
                                            <hr style="border-bottom: 1px solid #B6B6B6;"><br/>
                                            
                                            <div class="images">
                                                <{if !empty($item.arImgModules[i].arImages)}>
                                                    <{section name=j loop=$item.arImgModules[i].arImages}>
                                                        <{include file="common/module_images_settings.tpl" index=$smarty.section.j.index aliases=$item.arImgAliases module=$item.arImgModules[i].module arImages=$item.arImgModules[i].arImages[j]}>
                                                    <{/section}>
                                                <{/if}>
                                            </div>
                                        </div>
                                    <{/section}>
                                </div>    
                            <{/if}>
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                             <div class="buttons_list">
                                 <input name='submit' class='buttons' type='submit' value='<{$smarty.const.BUTTON_SAVE}>' 
                                        onclick="return formCheck(this.form);" />
                             </div>
                         </td>
                    </tr>
                </table>
            </li>
            <li id="tab_modules" <{if $arrPageData.tab=='modules'}>class="active"<{/if}>>
                <table border="1" cellspacing="0" cellpadding="1" class="list">
                    <tr>
                        <td>
                             <table border="1" cellspacing="0" cellpadding="1" class="list user"> 
                                 <tr>
                                     <td align="left" id="headb">модуль</td>
                                     <td align="left" id="headb">название</td>
                                     <td align="left" id="headb">краткое название</td>
                                     <td align="center" id="headb" width="40">сеогруппа</td>
                                     <td align="center" id="headb" width="30">изображение</td>
                                     <td align="center" id="headb" width="30">доступ</td>
                                     <td align="center" id="headb" width="30">история</td>
                                     <td align="center" id="headb" width="30">меню</td>
                                     <td align="center" id="headb" width="30">сорт</td>
                                </tr>
                                <{if !empty($item.arModulesParams)}>
                                    <{foreach from=$item.arModulesParams item=arItem}>
                                <tr> 
                                    <td align="left"><{$arItem.module}></td>
                                    <td align="left"><input size="30" type="text" name="arModules[<{$arItem.module}>][title]" value="<{$arItem.title}>"/></td>
                                    <td align="left"><input size="30" type="text" name="arModules[<{$arItem.module}>][short_title]" value="<{$arItem.short_title}>"/></td>
                                    <td align="center">
                                        <input type="checkbox" name="arModules[<{$arItem.module}>][seogroup]" <{if $arItem.seogroup}>checked<{/if}> value="1" class="left ml-10" onclick="toogleHiddenBlock(this, this.checked);"/>
                                        <div class="left ml-10 relative<{if !$arItem.seogroup}> hidden_block<{/if}>">
                                            <a href="javascript:void(0)" onclick="toogleHiddenBlock(this, $(this).next('div').hasClass('hidden_block'), $(this).parent().find('input'));"><img src="/images/operation/edit.png" width="14" height="14"/></a>
                                            <div class="hidden_block module_params">
                                                <input type="text" name="arModules[<{$arItem.module}>][seotable]" value="<{$arItem.seotable}>" style="width:97%" onchange="checkField(this);"/>
                                                <a class="close" href="javascript:void(0)" onclick="if(checkField($(this).prev('input'))) $(this).parent().addClass('hidden_block');"><img src="/images/operation/delete.png"/></a>
                                            </div>
                                        </div>
                                    </td>
                                    <td align="center"><input type="checkbox" name="arModules[<{$arItem.module}>][images]" <{if $arItem.images}>checked<{/if}> value="1"/></td>
                                    <td align="center"><input type="checkbox" name="arModules[<{$arItem.module}>][access]" <{if $arItem.access}>checked<{/if}> value="1"/></td>
                                    <td align="center"><input type="checkbox" name="arModules[<{$arItem.module}>][history]" <{if $arItem.history}>checked<{/if}> value="1"/></td>
                                    <td align="center"><input type="checkbox" name="arModules[<{$arItem.module}>][menu]" <{if $arItem.menu}>checked<{/if}> value="1"/></td>
                                    <td align="center"><input type="text" name="arModules[<{$arItem.module}>][order]" class="order" size="1" value="<{$arItem.order}>"/></td>
                                </tr>
                                    <{/foreach}>
                                <{/if}>
                            </table>
                        </td>
                        <td class="buttons_row" valign="top" width="144">
                            <div class="buttons_list">
                                <input name='submit_params' class='buttons' type='submit' value='<{$smarty.const.BUTTON_SAVE}>' 
                                       onclick="return formCheck(this.form);" />
                            </div>
                        </td>
                    </tr>
                </table>
            </li>
            <li id="tab_users" <{if $arrPageData.tab=='users'}>class="active"<{/if}>>
                
                <{if !empty($item.arrUserTypes)}>
                    <div class="modules">
                    <{section name=i loop=$item.arrUserTypes}>
                        <div  id="group_<{$item.arrUserTypes[i].id}>" class="module">
                            <h3>Группа "<{$item.arrUserTypes[i].name_ru}>"</h3>
                            <hr style="border-bottom: 1px solid #B6B6B6;"><br/>
                            <strong>Настройки доступов для</strong> &nbsp;
                            <select class="user" data-action='update' data-gid="<{$item.arrUserTypes[i].id}>" onchange="getAccessSettings(this);">
                                <option value="0" >всей группы</option>
                                <{if !empty($item.arrUserTypes[i].users)}>
                                    <{section name=j loop=$item.arrUserTypes[i].users}>
                                        <option value="<{$item.arrUserTypes[i].users[j].id}>"><{$item.arrUserTypes[i].users[j].firstname}> <{$item.arrUserTypes[i].users[j].surname}></option>
                                    <{/section}>
                                <{/if}>
                            </select>
                            &nbsp; <label><input type="checkbox" class="checkboxes check_all" onchange="SelectCheckBox(this, '#group_<{$item.arrUserTypes[i].id}>');" /> отметить все</label><br/><br/>
                            <div class="messages"></div>
                            <div class="load"><img src="/images/loader.gif"/></div>
                            <div class="settings">
                                <{include file="ajax/access_settings.tpl" arModules=$item.arModules gid=$item.arrUserTypes[i].id availableModules=$item.arrUserTypes[i].modules}>
                            </div>
                        </div>
                    <{/section}>
                    </div>
                <{/if}>
                
            </li>
        </ul>
    </div>
</form>
</div>
<script type="text/javascript">   
    //####################### settings functions ###############################
    function toogleHiddenBlock(el, condition, input) {
        el = $(el).next('div');
        if(condition) {
            $(el).removeClass('hidden_block');
        } else {
            if($(input).length) {
                if(checkField($(input))) {
                    $(el).addClass('hidden_block');
                }
            } else $(el).addClass('hidden_block');
        }
    }
    
    function checkField(el) {
        if(!$(el).val().length) {
            $(el).addClass('error'); 
            return false;
        } else {
            $(el).removeClass('error');
            return true;
        }
    }
    
    function getAccessSettings(item) {
        var gid = $(item).attr('data-gid');
        var action = $(item).attr('data-action');
        var uid = $('#group_'+gid).find('.user').val();
        
        var messages = $('#group_'+gid).find('.messages');
        var settings = $('#group_'+gid).find('.settings');        
        var loader = $('#group_'+gid).find('.load');
        $(loader).width($('#group_'+gid).find('.settings').width());
        $(loader).height($('#group_'+gid).find('.settings').height());
        $(loader).addClass('active');
        
        var modules = [];
        $.each($(settings).find('input:checked'), function(){
            modules.push($(this).val());
        });
        
        $.ajax({
            url: '/interactive/ajax.php?zone=admin&action=getAccessSettings',
            type: 'GET',
            dataType: 'json',
            data: {
                'gid': gid,
                'option' : action,
                'uid': uid,
                'modules' : modules.join(',')
            },
            success: function(json) {
                if(json) {
                    if(json.messages || json.errors){
                        if (json.messages){
                            $(messages).html(json.messages);
                            $(messages).addClass('message');
                        } else if(json.errors){
                            $(messages).html(json.errors);
                            $(messages).addClass('error');
                        }
                        $(messages).show();
                        setTimeout(function () {$(messages).fadeOut()}, 1000);
                    }
                    if(json.settings) $(settings).html(json.settings);
                    var inputs_count = $(".checkboxes:not(.check_all)", $('#group_'+gid)).length;
                    var checked_inputs_count = $(".checkboxes:checked:not(.check_all)", $('#group_'+gid)).length;
                    if(inputs_count>0){
                       $(".checkboxes.check_all", $('#group_'+gid)).attr('checked', (inputs_count == checked_inputs_count) ? true : false);
                    }
                }
            }
        });     
        setTimeout(function () {$(loader).removeClass('active')}, 200);
    }
    
    function SelectCheckBox(cb, container){  
        if(typeof container == 'undefind')
            container = 'document';
        if($(cb).hasClass('check_all')) {
            $(".checkboxes", container).prop('checked', $(cb).prop('checked'));
            $(".checkboxes:not('.check_all'):not('.auth')", container).prop('disabled', $(cb).prop('checked') ? false : true);
        } else if($(cb).hasClass('auth')) {
            if(!$(cb).prop('checked'))
                $(".checkboxes", container).prop('checked', false);
            $(".checkboxes:not('.check_all'):not('.auth')", container).prop('disabled', $(cb).prop('checked') ? false : true);
        } else {
            var inputs_count = $(".checkboxes:not(.check_all)", container).length;
            var checked_inputs_count = $(".checkboxes:checked:not(.check_all)", container).length;
            if(inputs_count>0){
               $(".checkboxes.check_all", container).attr('checked', (inputs_count == checked_inputs_count) ? true : false);
            }
        }
    }
    
    
    //############ images functions ##########################################
    function recalcRatio(input) {
        var width = parseInt($(input).closest('table').find('.crop.width').val());
        var height = parseInt($(input).closest('table').find('.crop.height').val());
        var inputval = isNumber($(input).val()) ? parseInt($(input).val()) : 0;
        
        if(width && height && inputval){
            if($(input).hasClass('width')){
                if(inputval>width) {
                    $(input).val(width);
                    inputval = width;
                }
                $(input).closest('tr').find('.height').val(Math.round(height*inputval/width).toFixed());    
            } else if($(input).hasClass('height'))  {
                if(inputval>height) {
                    $(input).val(height);
                    inputval = height;
                }
                $(input).closest('tr').find('.width').val(Math.round(width*inputval/height).toFixed());
            }  
        }
    }
       
    function removeImage(item) {
        $(item).closest('.image').remove();
    }
    function addImage(item) {
        var module = $(item).attr('data-module');
        var index  = $(item).parent().find('.images').find('.image').length;
        
        $.ajax({
            url: '/interactive/ajax.php?zone=admin&action=getImgSettings',
            type: 'GET',
            dataType: 'json',
            data: {
                'module': module,
                'index' : index,
            },
            success: function(json) {
                if(json && json.image) {
                    $(item).parent().find('.images').append(json.image);
                }
            }
        });   
    }
    
</script>