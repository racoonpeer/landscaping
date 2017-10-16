<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.USERS_TITLE 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_USER 
          edit_title=$smarty.const.ADMIN_EDIT_USER 
}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
   <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table width="100%" border="0" cellspacing="1" cellpadding="0" class="" align="center">
                    <tr>
                        <td  width="250" align="center" valign="top">
                            <b><{$smarty.const.HEAD_IMAGE}>: </b>
                            <div style="margin:10px 5px;border:1px solid #999999;background-color:#E5E5E5;width:<{if !empty($item.image)}><{$item.arImageData.w}><{else}><{$arrPageData.def_img_param.w}><{/if}>px;height:<{if !empty($item.image)}><{$item.arImageData.h}><{else}><{$arrPageData.def_img_param.h}><{/if}>px;">
                                <{if !empty($item.image)}><img src="<{$arrPageData.files_url|cat:$item.image}>" border="0" alt="" /><{/if}>
                            </div>
                        </td>
                        <td  valign="top">
                            <strong>Данные пользователя: </strong><br/><br/>

                            <div class="inline">Доступ: <span class="required">*</span> </div>
                            <select name="type" class="field requirefield" style="width:132px; height:19px;"<{if $item.id==1 OR $objUserInfo->type != 'Administrator'}> disabled="disabled"<{/if}>>
                                <{section name=i loop=$arrUserTypes}>
                                    <option value="<{$arrUserTypes[i].name}>"<{if $item.type==$arrUserTypes[i].name || ($arrPageData.task=='addItem' && $arrUserTypes[i].name=='Registered')}>  selected<{/if}>> &nbsp; <{$arrUserTypes[i].title}> </option>
                                <{/section}>
                            </select><br/><br/>

                            <div class="inline"><{$smarty.const.USERS_ENABLED}>: <span class="required">*</span> </div>
                            <select name="active" class="field requirefield" style="width:132px; height:19px;"<{if $item.id==1 OR $objUserInfo->type != 'Administrator'}> disabled="disabled"<{/if}>>
                                <option value="1"> <{$smarty.const.OPTION_YES}> </option>
                                <option value="0"<{if $item.active==0}> selected<{/if}>> <{$smarty.const.OPTION_NO}> </option>
                            </select><br/><br/>

                            <div class="inline"><{$smarty.const.USERS_LOGIN}>: <span class="required">*</span> </div>
                            <input class="field requirefield" size="90" name="login" type="text" value="<{$item.login}>"<{if $item.id==1 OR $objUserInfo->type != 'Administrator'}> readonly<{/if}> />
                            <input name="old_login" size="70" type="hidden" value="<{$item.login}>" /><br/><br/>


            <{if $arrPageData.task=='addItem'}>
                             <div class="inline"><b><{$smarty.const.USERS_PASS}>: </b><span class="required">*</span> </div>
                             <input class="field requirefield" size="90" name="pass"  type="password" value="" /><br/><br/>
                             <div class="inline"><b><{$smarty.const.USERS_CONFPASS}>: </b><span class="required">*</span> </div>
                             <input class="field requirefield" size="90" name="confpass" type="password" value="" /><br/><br/>
            <{elseif $arrPageData.task=='editItem'}>
                             <{if $objUserInfo->type == 'Administrator'}> 
                                <div class="inline">Старый пароль: <{if $objUserInfo->type != 'Administrator'}><span class="required">*</span><{/if}></div>
                                <input class="field<{if $objUserInfo->type != 'Administrator'}> requirefield<{/if}>" name="old_pass"  size="90" type="password" value="" /><br/><br/>
                             <{/if}>

                             <div class="inline"><{$smarty.const.USERS_NEW_PASS}>:</div>
                             <input class="field" name="pass" size="90" type="password" value="" /><br/><br/>
                             <div class="inline"><{$smarty.const.USERS_CONFPASS}>:</div>
                             <input class="field" name="confpass" size="90" type="password" value="" /><br/><br/>
            <{/if}>
                             <div class="inline left"><{$smarty.const.USERS_AUTO_PASS}></div>
                             <input class="field left" name="autogenpass" id="autogenpass" size="28"  type="text" value="" style="margin-top:5px; margin-left: 3px; margin-right:10px;" readonly size="13"/>
                             <input id='passGenerate' name='passGenerate' class='buttons left' type='button' style="margin-right:8px; width:150px;" value='<{$smarty.const.HEAD_GENERATE}>' onclick="generatePassword(this.form);" style="cursor:pointer; margin-top:0;" />
                             <input id='passApply' name='passApply' class='buttons left' type='button' style="margin-top:5px;" value='<{$smarty.const.BUTTON_APPLY}>' onclick="applyPassword(this.form);" style="cursor:pointer; margin-top:0;" /><br/><br/>
                             <div class="clear"></div>
                             <small style="margin-left:110px;"><{$smarty.const.USERS_COPY_PASS_BEFORE_SAVE}></small><br/><br/>

                             <div class="inline"><{$smarty.const.USERS_SNAME}>: <span class="required">*</span></div>
                             <input class="field requirefield" name="surname" size="90" type="text" value="<{$item.surname}>" /><br/><br/>

                             <div class="inline"><{$smarty.const.USERS_FNAME}>: <span class="required">*</span> </div>
                             <input class="field requirefield" name="firstname" size="90" type="text" value="<{$item.firstname}>" /><br/><br/>

                             <div class="inline"><{$smarty.const.USERS_MNAME}>: </div>
                             <input class="field" name="middlename" type="text" size="90" value="<{$item.middlename}>" /><br/><br/>

                             <div class="inline"><{$smarty.const.USERS_PHONE}>: </div>
                             <input class="field" name="phone" type="text" size="90" value="<{$item.phone}>" /><br/><br/>

                             <div class="inline"><{$smarty.const.USERS_MAIL}>: <span class="required">*</span> </div>
                             <input class="field requirefield" name="email" size="90" type="text" value="<{$item.email}>" />
                             <input name="old_email" type="hidden" value="<{$item.email}>" /><br/><br/>

                             <div class="inline"><{$smarty.const.LABEL_ADDRESS}>: </div>
                             <input class="field" name="address" type="text" size="90" value="<{$item.address}>" /><br/><br/>

                             <strong><{$smarty.const.USERS_DESCR}>: </strong><br/><br/>
                             <textarea name="descr" id="descr" style="width:580px; height:60px;" class="field"><{$item.descr}></textarea><br/><br/>

                             <div class="inline"><{$smarty.const.HEAD_FILES_MANAGER}>: </div>
                             <{if $arrPageData.task=='editItem'}>
                                <a href="/admin.php?module=users_files_uploadify&ajax=1&pmodule=<{$arrPageData.module}>&pid=<{if $item.id}><{$item.id}><{else}>0<{/if}>&files_params=<{$arrPageData.files_params|serialize|base64_encode|urlencode}>" onclick="return hs.htmlExpand(this, { headingText:'<{$smarty.const.HEAD_FILES_MANAGER}>', objectType:'iframe', preserveContent: false, width:900 } );"><{$smarty.const.HEAD_OPEN_MANAGER}></a>
                             <{else}>
                                <{$smarty.const.HEAD_FILES_MANAGER_ACCESS}>                        
                             <{/if}><br/><br/>

                             <div class="inline"><{if $arrPageData.task=='addItem'}><{$smarty.const.BUTTON_ADD}><{else}>Редактировать<{/if}> изображение:</div>
                             <input name="image" type="file"<{if !empty($item.image)}> onchange="if(this.value.length){ $('#image_delete').attr({checked:true, readonly:true}); this.form.image_w.value=''; this.form.image_h.value='';}"<{/if}> /><br/><br/>

                             <div class="inline"><{$smarty.const.HEAD_IMAGE_PARAMS}>:</div>
                             <label for="image_w"><{$smarty.const.HEAD_WIDTH}>: <input class="field_smal" name="image_w" id="image_w" type="text" value="<{if !empty($item.image)}><{$item.arImageData.w}><{else}><{$arrPageData.def_img_param.w}><{/if}>" size="2" /></label>
                             &nbsp;&nbsp;
                             <label for="image_h"><{$smarty.const.HEAD_HEIGHT}>: <input class="field_smal" name="image_h" id="image_h" type="text" value="<{if !empty($item.image)}><{$item.arImageData.h}><{else}><{$arrPageData.def_img_param.h}><{/if}>" size="2" /></label>
                             &nbsp;&nbsp;
                             <label for="image_delete"><{$smarty.const.HEAD_DELETE}>: <input id="image_delete" name="image_delete" type="checkbox" value="1"<{if empty($item.image)}> disabled<{/if}> onclick="if($(this).attr('readonly')){return false;}" /></label>          
                        </td>
                        <td rowspan="0" class="buttons_row" valign="top" width="144">
            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=1}>
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
    function formCheck(form){
        var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
        var regExpPhone = new RegExp("^([0-9 \-]{7,17})$");
        var errors      = 0;

        $.each($(form).find('.requirefield'), function(i, input) {
            if ( input.value.length===0 // type=='text', type=='select-one', type=='textarea'
             || (input.name==='email' && input.value.match(regExpEmail) === null) // type=='text', name=='email'
             || (input.name==='phone' && input.value.match(regExpPhone) === null) // type=='text', name=='phone'
                ){
                    if(!errors) $(this).focus();
                    $(this).addClass('required');
                    errors++;
            } else $(this).removeClass('required');
        });

        if(!errors){
            return true;
        } else alert("<{$smarty.const.USERS_EMPTY_FIELDS}>");
        return false;
    }
    function applyPassword(form){
        if(confirm('<{$smarty.const.USERS_CONFIRM_CHANGE_PASS}>')){
            if(form.autogenpass.value.length===0){
                alert('<{$smarty.const.USERS_COPY_PASS_FIRST}>');
            } else {
                form.pass.value = form.confpass.value = form.autogenpass.value;
<{if $arrPageData.task=='editItem'}>
                form.submit_apply.click();
<{/if}>
                return true;
            }
        } return false;
    }
    function generatePassword(form){
        $.getJSON(
            "/interactive/ajax.php",
            {zone: "admin", action: "generatePassword", length:12},
            function(data){
                if(data.code) {
                    form.autogenpass.value = data.code;
                } else alert("<{$smarty.const.USERS_GENERATE_PASS_ERROR}>");
            }
        );
    }
//-->
</script>

<{* +++++++++++++++++ SHOW VIEW ITEM FORM ++++++++++++++++++++++ *}>
<{elseif $arrPageData.task=='viewItem'}> 
       <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
 <table border="1" cellspacing="1" cellpadding="1" class="list ">       
        <tr>
            <td  width="250" align="center" valign="top">
                <b><{$smarty.const.HEAD_IMAGE}>: </b>
                <div style="margin:10px 5px; border:1px solid #999999;background-color:#E5E5E5;width:<{if !empty($item.image)}><{$item.arImageData.w}><{else}><{$arrPageData.def_img_param.w}><{/if}>px;height:<{if !empty($item.image)}><{$item.arImageData.h}><{else}><{$arrPageData.def_img_param.h}><{/if}>px;">
                    <{if !empty($item.image)}><img src="<{$arrPageData.files_url|cat:$item.image}>" border="0" alt="" /><{/if}>
                </div>
            </td>
            <td  valign="top">
                <strong>Данные пользователя: </strong><br/><br/>
                <div class="inline"><strong><{$smarty.const.USERS_ACCESS}>:</strong></div> <{$item.typename}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_ENABLED}>:</strong></div> 
                <{if $item.active}>
                    <span style="color:green;"><{$smarty.const.OPTION_YES}></span>
                <{else}>
                    <span style="color:red;"><{$smarty.const.OPTION_NO}></span>
                <{/if}><br><br/>
                <div class="inline"><strong><{$smarty.const.USERS_LOGIN}>:</strong></div> <{$item.login}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_PASS}>:</strong></div> 
                <{if !empty($item.pass)}>
                <{$smarty.const.USERS_PASS_SET}>
                <{else}><{$smarty.const.USERS_PASS_NOT_SET}><{/if}><br/>

                <div class="inline"><strong><{$smarty.const.USERS_SNAME}>:</strong></div> <{$item.surname}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_NAME}>:</strong> </div> <{$item.firstname}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_MNAME}>:</strong></div> <{$item.middlename}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_PHONE}>: </strong></div> <{$item.phone}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_MAIL}>:</strong></div> <{$item.email}> &nbsp;(<i><{$smarty.const.USERS_CONFIRM_MAIL}>: <strong><{if $item.type!='Anonimous'}><span style="color:green;"><{$smarty.const.OPTION_YES}></span><{else}><span style="color:red;"><{$smarty.const.OPTION_NO}></span><{/if}></strong></i>)<br/>
                <div class="inline"><strong><{$smarty.const.LABEL_ADDRESS}>: </strong></div> <{$item.address}><br/>
                <div class="inline"><strong><{$smarty.const.USERS_DESCR}>: </strong></div> <{$item.descr}><br/>
                <div class="inline"><strong><{$smarty.const.HEAD_FILE}>: </strong></div>
                <{foreach name=i from=$item.arrFiles key=iKey item=iItem}>
                    &nbsp;
                    <a href="<{$arrPageData.files_url|cat:$iItem.filename}>" onclick="return parent.hs.expand (this, { });" class="highslide">
                        <{$iItem.filename}>
                    </a>
                    <{if !$smarty.foreach.i.last}>, <br/><{/if}>
                <{foreachelse}>
                    &nbsp;<{$smarty.const.USERS_EMPTY_FILES}>
                <{/foreach}>

            </td>
            <td rowspan="0" class="buttons_row" valign="top" width="144">
                <div class="buttons_list">
                <{if $item.isnew}>
                <input class="buttons" name="button_activate" type="button" onclick="if(window.confirm('<{$smarty.const.CONFIRM_USER_ACTIVATION}>')){window.location='<{$arrPageData.current_url|cat:"&task=activateItem&status=1&itemID="|cat:$item.id}>';} return false;" value="<{$smarty.const.ACTIVATE}>" />
                <{elseif !$item.isnew}>
                <input class="buttons" name="button_edit" type="button" onclick="window.location='<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$item.id}>';" value="<{$smarty.const.BUTTON_EDIT}>" />
                <{/if}>
                <input class="buttons" name="button_cancel" type="button" onclick="if(window.confirm('<{$smarty.const.CONFIRM_CLOSE_VIEW}>')){window.location='<{$arrPageData.current_url}>';} return false;" value="<{$smarty.const.CLOSE_VIEW}>" />
                <{if $item.id>1}>
                <input class="buttons" name="button_delete" type="button" onclick="if(window.confirm('<{$smarty.const.CONFIRM_DELETE}>')) {window.location='<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$item.id}>';} return false;" value="<{$smarty.const.HEAD_DELETE}>" />
                <{/if}>
                </div>
            </td>
        </tr>
 </table>
                </li>
                        <li id="tab_history">
            <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
        </li>
                </ul>
                </div>
<{/if}>

<{if $arrPageData.task=='default'}>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_USER}>
<div class="clear"></div>
<{/if}>

<{if !empty($arrNewItems)}>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list user">
    <tr><th  colspan="7" style="color:red;"> &diams; <{$smarty.const.USER_TO_ACTIVATE_LIST}>:</th></tr>
    <tr>
        <td id="headb" align="center" width="12"><{$smarty.const.USERS_ID}></td>
        <td id="headb" align="left"  width="100"><{$smarty.const.USERS_LOGIN}></td>
        <td id="headb" align="left"><{$smarty.const.USERS_FULL_NAME}></td>
        <td id="headb" width="200"><{$smarty.const.HEAD_CONTACTS}></td>
        <td id="headb" width="30"><{$smarty.const.USERS_ENABLED}></td>
        <td id="headb" align="center" width="32"><{$smarty.const.USER_SHOW_DATA}></td>
    </tr>
<{section name=i loop=$arrNewItems}>
    <tr>
        <td align="center"><{$arrNewItems[i].id}></td>
        <td ><{$arrNewItems[i].login}></td>
        <td ><{$arrNewItems[i].surname}> <{$arrNewItems[i].firstname}> <{$arrNewItems[i].middlename}></td>
        <td ><{if !empty($arrNewItems[i].email)}><{$arrNewItems[i].email}> &nbsp; <{/if}><{if !empty($arrNewItems[i].phone)}><{$arrNewItems[i].phone}> &nbsp; <{/if}><{$arrNewItems[i].city}></td>
        <td align="center"><strong><{if $arrNewItems[i].type=='Registered'}><span style="color:green;"><{$smarty.const.OPTION_YES}></span><{else}><span style="color:red;"><{$smarty.const.OPTION_NO}></span><{/if}></strong></td>
        <td align='center'>
            <a href="<{$arrPageData.current_url|cat:"&task=viewItem&itemID="|cat:$arrNewItems[i].id}>" title="<{$smarty.const.USERS_ACTIVATION_VIEW}>">
                <img width="20" src="<{$arrPageData.system_images}>find.png" alt="<{$smarty.const.USERS_ACTIVATION_VIEW}>" />
            </a>
        </td>
    </tr>
<{/section}>
    <tr>
        <td colspan="7" id="line"><{$smarty.const.SITE_COUNT_RECORDS}><{$arrNewItems|@count}></td>
    </tr>
</table>
<div>&nbsp;</div>
<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
    <tr><th  style="color:black;"> &diams; <{$smarty.const.USERS_LIST}>:</th></tr>
</table>                
<{/if}>

<table width="100%" border="0" cellspacing="1" cellpadding="0" class="list user">
    <tr>
        <td id="headb" align="center" width="38"></td>
        <td id="headb" align="left"  width="100"><{$smarty.const.USERS_LOGIN}></td>
        <td id="headb" align="left"><{$smarty.const.USERS_FULL_NAME}></td>
        <td id="headb" align="center" width="45"><{$smarty.const.USERS_MAIL}></td>
        <td id="headb" align="center" width="45"><{$smarty.const.USERS_PHONE}></td>
        <td id="headb" align="center" width="45"><{$smarty.const.USERS_ACCESS}></td>
        <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
        <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
    </tr>
<{section name=i loop=$items}>
    <tr>
        <td align='center'>
<{if $items[i].id==1}><{$smarty.const.HEAD_DENIED}>
<{elseif $items[i].active==1}>
            <a href="<{$arrPageData.current_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="Enable">
                <img src="<{$arrPageData.system_images}>check.png" alt="<{$smarty.const.USERS_ACTIVE}>" />
            </a>
<{else}>
            <a href="<{$arrPageData.current_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="No Enable">
                <img src="<{$arrPageData.system_images}>un_check.png" alt="<{$smarty.const.USERS_NOACTIVE}>" />
            </a>
<{/if}>
        </td>
        <td ><{$items[i].login}></td>
        <td align="left">
            <a href="<{$arrPageData.current_url|cat:"&task=viewItem&itemID="|cat:$items[i].id}>" title="Просмотр">
                <{$items[i].surname}> <{$items[i].firstname}> <{$items[i].middlename}>
            </a>
        </td>
        <td ><{$items[i].email}></td>
        <td ><{$items[i].phone}></td>
        <td ><{$items[i].typename}></td>

        <td align='center'>
<{if $items[i].id==1 && $objUserInfo->type != 'Administrator'}>
            <{$smarty.const.HEAD_DENIED}>
<{else}>
            <a href="<{$arrPageData.current_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
            </a>
<{/if}>
        </td>
        <td align='center'>
<{if $items[i].id==1 OR $objUserInfo->type != 'Administrator'}>
            <{$smarty.const.HEAD_DENIED}>
<{else}>
        <a href="<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
            <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" />
        </a>
<{/if}>
        </td>
    </tr>
<{/section}>
</table>
<table width="100%" border="0" cellspacing="1" cellpadding="0">
    <tr>
        <td align="center" width="345"></td>
        <td align="center" width="350">
            <{if $arrPageData.total_pages>1}>
                <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{/if}>
        </td>
        <td align="right">
            
        </td>
    </tr>
</table>
</div>