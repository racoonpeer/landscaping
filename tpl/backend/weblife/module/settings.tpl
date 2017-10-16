<{include file='common/module_head.tpl' 
          title=$smarty.const.TITLE_SETTINGS 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_PRODUCT 
          edit_title=$smarty.const.ADMIN_EDIT_PRODUCT 
}>

<div id="right_block">
<form method="post" action="<{$arrPageData.current_url}>" name="settingsForm">
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" <{if empty($arrPageData.activeTab)}>class="active"<{/if}>>Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
            <li><a href="javascript:void(0);" data-target="actionsLog" 
                   <{if !empty($arrPageData.activeTab) && $arrPageData.activeTab=='actionsLog'}>class="active"<{/if}>>
                    Глобальная История
                </a>
            </li>       
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li <{if empty($arrPageData.activeTab)}>class="active"<{/if}> id="tab_main">
                <table border="0" cellspacing="0" cellpadding="1" class="list">  
                    <tr>
                        <td colspan="2">
                            <strong><{$smarty.const.SETTINGS_OWNER}></strong><br/><br/>
                            <div class="inline"><{$smarty.const.SETTINGS_FIRST_NAME}> <font color="red">*</font>:</div>
                            <input name="arValidate[ownerFirstName]" type="hidden" value="<{$smarty.const.SETTINGS_FIRST_NAME}>"/>
                            <input name="arSettings[ownerFirstName]" type="text" size="117" value="<{if isset($item.ownerFirstName)}><{$item.ownerFirstName}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_LAST_NAME}> <font color="red">*</font>: </div>
                            <input name="arValidate[ownerLastName]" type="hidden" value="<{$smarty.const.SETTINGS_LAST_NAME}>"/>
                            <input name="arSettings[ownerLastName]" type="text" size="117" value="<{if isset($item.ownerLastName)}><{$item.ownerLastName}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_EMAIL}> <font color="red">*</font>:</div>
                            <input name="arValidate[ownerEmail]" type="hidden" value="<{$smarty.const.SETTINGS_EMAIL}>|email"/>
                            <input name="arSettings[ownerEmail]" type="text" size="117" value="<{if isset($item.ownerEmail)}><{$item.ownerEmail}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_PHONE}>:</div>
                            <input name="arValidate[ownerPhone]" type="hidden" value="<{$smarty.const.SETTINGS_PHONE}>|phone|empty"/>
                            <input name="arSettings[ownerPhone]" type="text" size="117" value="<{if isset($item.ownerPhone)}><{$item.ownerPhone}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_COPYRIGHT}>:</div>
                            <input name="arSettings[copyright]" size="117" type="text" value="<{if isset($item.copyright)}><{$item.copyright}><{/if}>" class="field"/>

                            <br/><br/>
                            <strong><{$smarty.const.SETTINGS_WEBSITE}></strong><br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_WEBSITE_NAME}> <font color="red">*</font>: </div>
                            <input name="arValidate[websiteName]" type="hidden" value="<{$smarty.const.SETTINGS_WEBSITE_NAME}>"/>        
                            <input name="arSettings[websiteName]" type="text" size="117" value="<{if isset($item.websiteName)}><{$item.websiteName}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline"><{$smarty.const.SETTINGS_WEBSITE_SLOGAN}>:</div>
                            <input name="arSettings[websiteSlogan]" type="text" size="117" value="<{if isset($item.websiteSlogan)}><{$item.websiteSlogan}><{/if}>" class="field" />
                            <br/><br/>        

                            <div class="inline"><{$smarty.const.SETTINGS_WEBSITE_URL}> <font color="red">*</font>: </div>
                            <input name="arValidate[websiteUrl]" type="hidden" value="<{$smarty.const.SETTINGS_WEBSITE_URL}>"/>           
                            <input name="arSettings[websiteUrl]" type="text" size="117" value="<{if isset($item.websiteUrl)}><{$item.websiteUrl}><{/if}>" class="field" />
                            <br/><br/>

                            <div class="inline left"><{$smarty.const.SETTINGS_WEBSITE_LOGO}>:</div>
                            <input name="arSettings[logo]" type="text" size="100" value="<{if isset($item.logo)}><{$item.logo}><{/if}>" class="field left" />
                            <{if isset($item.logo)}><img align="left" style="padding:0; margin: 10px 13px 10px 10px;" src="<{$item.logo}>" width="16" alt=""/><{/if}>          
                            <div class="clear"></div>
                            <br/><br/>

                            <strong><{$smarty.const.SETTINGS_EMAIL}></strong><br/><br/>

                            <{$smarty.const.SETTINGS_NOTIFY_EMAIL}> <font color="red">*</font>:<br/><br/>
                            <input name="arValidate[notifyEmail]" type="hidden" value="<{$smarty.const.SETTINGS_NOTIFY_EMAIL}>"/>
                            <input name="arSettings[notifyEmail]" type="text" size="137" value="<{if isset($item.notifyEmail)}><{$item.notifyEmail}><{/if}>" class="field" /><br/> <br/>               

                            <{$smarty.const.SETTINGS_SITE_EMAIL}> <font color="red">*</font>:<br/><br/>
                            <input name="arValidate[siteEmail]" type="hidden" value="<{$smarty.const.SETTINGS_SITE_EMAIL}>"/>
                            <input name="arSettings[siteEmail]" type="text" size="137" value="<{if isset($item.siteEmail)}><{$item.siteEmail}><{/if}>" class="field" /><br/>

                            <br>
                            <strong><{$smarty.const.SETTINGS_ADDRESS}>:</strong>
                            <a href="javascript:toggleEditor('settingsAddress');">
                                <{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}>
                            </a><br/><br/>
                            <textarea style="width:840px; height: 100px;" id="settingsAddress" name='arSettings[ownerAddress]' cols='105' rows='5' class="field"><{if isset($item.ownerAddress)}><{$item.ownerAddress}><{/if}></textarea>
                        </td>

                        <td class="buttons_row" valign="top" width="144">
                            <div class="buttons_list">
                                <input name='submit' class='buttons' type='submit' value='<{$smarty.const.BUTTON_SAVE}>'  />
                            </div>
                        </td>
                    </tr>
                </table>
            </li>
            <li id="tab_history">
                <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
            </li>
             <li id="tab_actionsLog" <{if !empty($arrPageData.activeTab) && $arrPageData.activeTab=='actionsLog'}>class="active"<{/if}>>
                <div class="load"></div>
                <table border="0" cellspacing="0" cellpadding="1" class="list">  
                    <tr>
                        <td valign="top" width="175">
                            <div id="filters" class="filters">
                                <{include file="ajax/actions_log_filters.tpl" arFilters=$arActionsLog.arFilters selectedFilters=$arActionsLog.selectedFilters}>
                            </div>
                        </td>
                        <td valign="top">
                            <div id="history">
                                <{include file="ajax/actions_log.tpl" arHistoryData=$arActionsLog.arHistory}>
                            </div>
                        </td>
                    </tr>
                </table>
             </li>
         </ul>
     </div>
</form>
</div>
                            
<script type=text/javascript>
    function clearDate() {
        $('#date_to').val('');
        $('#date_from').val('');
        updateHistory();
    }
    
    function ConvertDateText(dateText, date_sep){
        if(dateText.length > 0){
            if(date_sep===undefined) date_sep = '-';
            var arr = [];

            var arDateTime = dateText.split(' ');
           // if(arDateTime.length==2) {
                if(arDateTime[0].indexOf('.')!==-1){
                    arr = arDateTime[0].split('.');
                    if(date_sep==='-') arr.reverse();
                } else if(arDateTime[0].indexOf('-')!==-1){
                    arr = arDateTime[0].split('-');
                    if(date_sep==='.') arr.reverse();
                } else if(arDateTime[0].indexOf('/')!==-1){
                    arr = arDateTime[0].split('/');
                    if((date_sep==='.' && arDateTime[0].match(/^\d{4}\/\d{2}\/\d{2}$/) !== null) || (date_sep==='-' && arDateTime[0].match(/^\d{2}\/\d{2}\/\d{4}$/) !== null))
                        arr.reverse();
                }
                
             /*   if(arDateTime[1].indexOf(':')!==-1){
                    var arTime = arDateTime[1].split(':');
                    for(var i=0; i<arTime.length; i++)
                        if(arTime[i] == '00') arr.push('0');
                        else arr.push(arTime[i]);
                }*/
            //}
        }
        return arr;
    }

    function ConvertToJSDate(dateText){
        var date = '';
        if(dateText.length > 0){
           var d = ConvertDateText(dateText);
           date = +new Date(d[0], d[1]-1, d[2])/1000;
        }
        return date;
    }
        
    function toogleDateTime(cb) {
         var datetime = $('#datetime');
         if(cb.checked && $(cb).hasClass('show')) {
             $(datetime).removeClass('hidden_block');
         } else {
             $(datetime).addClass('hidden_block');
         }
         updateHistory(cb);
    };

    function updateHistory(item, refresh, page) { 
        $('.load').addClass('active');
        $('.load').css('width', $('.load').next('table').width());
        $('.load').css('height', $('.load').next('table').height());
        var arData = {};
        arData['filters[tab]'] = 'actionsLog';
        if(typeof item != 'undefined' && item!=false) arData['key'] = $(item).attr('data-type');
        if(typeof page != 'undefined') arData['filters[page]'] = page;
        if(typeof refresh == 'undefined' || refresh==false){
            $.each($('#filters').find('input, select'), function(i, input){
                if(input.checked || input.selectedIndex || 
                   (input.type =='text' && $('.datetime.show').prop('checked'))){
                    var iName = $(input).attr('name');
                    var iVal;
                    if(input.type=='text') { 
                        if($(input).val().length>0) iVal = ConvertToJSDate($(input).val());
                    }  else    iVal = $(input).val();
                    arData[iName] = iVal;
                }
            });
        } else {
            arData['filters[time]'] = '1';
        }
        
        $.ajax({
            url: '/interactive/ajax.php?zone=admin&action=filterActionsLog',
            type: 'GET',
            dataType: 'json',
            data: arData,
            success: function(json) {
                if(json) {
                    $('#history').html(json.history);
                    $('#filters').html(json.filters);
                }
                if(History.enabled) {
                    History.pushState(null, document.title, '/admin.php?module=<{$arrPageData.module}>'+json.url);
                }
            }
        });
        setTimeout(function () {$('.load').removeClass('active')}, 200);
    };   
</script>                       
