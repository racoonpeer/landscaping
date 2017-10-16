<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' 
          title=$smarty.const.CURRENCY 
          creat_title=$smarty.const.ADMIN_CREATING_NEW_CURRENCY 
          edit_title=$smarty.const.ADMIN_EDIT_CURRENCY 
}><{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>

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
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">       
                <tr>
                    <td id="headb" align="left" width="175"><{$smarty.const.HEAD_NAME}> <{$smarty.const.CURRENCY_NAME_EXAMPLE}>
                        <font style="color:red">*</font></td>
                    <td >
                        <input class="field requirefield" name="name" id="title" size="35" type="text" value="<{$item.name}>" />
                    </td>
                    <td class="buttons_row" valign="top" width="144">
            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    </td>
                </tr>

                <tr>
                    <td id="headb" align="left" width="175"><{$smarty.const.HEAD_TITLE}> <{$smarty.const.CURRENCY_TITLE_EXAMPLE}>
                        <font style="color:red">*</font></td>
                    <td  >
                       <input class="field requirefield" name="title" id="title" size="35" type="text" value="<{$item.title}>" />
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

                <tr>
                    <td id="headb" align="left"><{$smarty.const.CURRENCY_VIEW_DATA}>:</td>
                    <td  align="left">
                        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
                            <tr>
                                <td id="head" align="center" width="100"><{$smarty.const.HEAD_CURRENCY_CODE}><br/>(USD)</td>
                                <td id="head" align="center" width="100"><{$smarty.const.HEAD_SIGN}><br/>($)</td>
                                <td id="head" align="center" width="100"><{$smarty.const.HEAD_NOMINAL}><br/>(1)</td>
                                <td id="head" align="center" width="162"><{$smarty.const.HEAD_RATE}>/<{$smarty.const.HEAD_COEFFICIENT}><br/>(1.0000)</td>
                                <td id="head" align="center" width="135"><{$smarty.const.HEAD_DEFAULT_4_CALC}></td>
                                <td id="head" align="center" width="135"><{$smarty.const.HEAD_DEFAULT_4_SHOW}></td>
                            </tr>
                            <tr>
                                <td  align="center">
                                    <input class="field requirefield" name="code" id="code" size="5" type="text" value="<{$item.code}>" onchange="addTemplate(this, this.form);" />
                                </td>
                                <td  align="center">
                                    <input class="field requirefield" name="sign" id="sign" size="5" type="text" value="<{$item.sign}>" onchange="addTemplate(this, this.form);" />
                                </td>
                                <td  align="center">
                                    <input class="field requirefield" name="nominal" id="nominal" size="5" type="text" value="<{$item.nominal}>"<{if $arrPageData.task=='editItem' || !$objCurrency->count}> disabled<{/if}> />
                                </td>
                                <td  align="center">
                                    <input class="field requirefield" name="rate" id="rate" size="5" type="text" value="<{$item.rate}>" onchange="setDefault(this, this.form);"<{if $arrPageData.task=='editItem' || !$objCurrency->count}> disabled<{/if}> />
                                </td>
                                <td  align="center">
                                    <select class="small_field" name="def4calc" id="def4calc" onfocus="if(this.value==0 && !isDefined(1)){ alert('<{$smarty.const.CURRENCY_CHANGE_DISABLE1}>  <{$smarty.const.HEAD_DEFAULT}> <{$smarty.const.CURRENCY_CHANGE_DISABLE2}>'); return false; }"<{if $arrPageData.task=='editItem' || !$objCurrency->count}> disabled<{/if}>>
                                        <option value="1"> <{$smarty.const.OPTION_YES}> </option>
                                        <option value="0"<{if !$item.def4calc}> selected<{/if}>> <{$smarty.const.OPTION_NO}> </option>
                                    </select>
                                </td>
                                <td  align="center">
                                    <select class="small_field" name="def4show" id="def4show" onfocus="if(this.value==0 && !isDefined(2)){ alert('<{$smarty.const.CURRENCY_CHANGE_DISABLE1}>  <{$smarty.const.HEAD_DEFAULT}> <{$smarty.const.CURRENCY_CHANGE_DISABLE2}>'); return false; }"<{if $arrPageData.task=='editItem' || !$objCurrency->count}> disabled<{/if}>>
                                        <option value="1"> <{$smarty.const.OPTION_YES}> </option>
                                        <option value="0"<{if !$item.def4show}> selected<{/if}>> <{$smarty.const.OPTION_NO}> </option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="buttons_row"></td>
                </tr>  

                <tr>
                    <td id="headb" align="left" width="175"><{$smarty.const.CURRENCY_SETTINGS}></td>
                    <td >
                        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
                            <tr>
                                <td id="head" align="center" width="35"><{$smarty.const.HEAD_DECIMALS}> (2)</td>
                                <td id="head" align="center" width="35"><{$smarty.const.HEAD_DECIMALS_POINT}> (,)</td>
                                <td id="head" align="center" width="250"><{$smarty.const.HEAD_THOUSAND_SEPARATOR}> (.)</td>
                                <td id="head" align="center" width="200"><{$smarty.const.HEAD_TEMPLATE}><br/>($1.100,00)</td>
                                <td id="head" align="center" width="100"><{$smarty.const.HEAD_EXAMPLE}></td>
                            </tr>
                            <tr>
                                <td  align="center">
                                    <input class="small_field requirefield" name="decimals" id="decimals" size="5" type="text" value="<{$item.decimals}>" onchange="createTemplate(this.form);" style="padding-left:0px;text-align:center;" />
                                </td>
                                <td  align="center">
                                    <select class="small_field" name="dec_point" id="dec_point" onchange="createTemplate(this.form);">
                    <{foreach name=i from=$item.arDecPoints key=sValue item=sTitle}>
                                        <option value="<{$sValue}>"<{if $item.dec_point==$sValue}>  selected<{/if}>> &nbsp; <{$sTitle}> &nbsp; </option>
                    <{/foreach}>
                                    </select>
                                </td>
                                <td  align="center" nowrap>
                                    <select class="small_field" name="thousands_separates" onchange="correctThousandsSeparator(this.form); createTemplate(this.form);">
                    <{foreach name=i from=$item.arSepVariants key=sValue item=sTitle}>
                                        <option value="<{$sValue|escape}>"<{if $item.thousands_sep==$sValue || (isset($item.thousands_separates) && $item.thousands_separates==$sValue)}>  selected<{/if}>> &nbsp; <{$sTitle}> &nbsp; </option>
                    <{/foreach}>
                                    </select>&nbsp;
                                    <input class="small_field" name="thousands_sep" id="thousands_sep" size="5" type="text" value="<{$item.thousands_sep|escape}>" onchange="correctThousandsSeparator(this.form); createTemplate(this.form);"<{if in_array($item.thousands_sep, $item.arSepVariants|@array_keys) || (isset($item.thousands_separates) && $item.thousands_sep!='#')}> readonly<{/if}> />
                                    <input name="unbreakspace" id="unbreakspace" type="hidden" value="<{$item.unbreakspace}>" />
                                </td>
                                <td  align="center" nowrap>
                                    <select class="small_field" name="templates" id="templates" onchange="fillTemplateData(this, this.form);">
                    <{foreach name=i from=$item.arrTemplates key=sValue item=sTitle}>
                                        <option value="<{$sValue}>"<{if $item.template==$sValue || (isset($item.templates) && $item.templates==$sValue)}>  selected<{/if}>> &nbsp; <{$sTitle}> &nbsp; </option>
                    <{/foreach}>
                                    </select>&nbsp;
                                    <input class="small_field requirefield" name="template" id="template" size="5" type="text" value="<{$item.template}>" onchange="createTemplate(this.form);"<{if in_array($item.template, $item.arrTemplates|@array_keys) || (isset($item.templates) && $item.templates!='#')}> readonly<{/if}> />
                                </td>
                                <td  align="center">
                                    <div id="tpl_example" style="font-weight:bold;"><{if !empty($item.tpl_example)}><{$item.tpl_example}><{/if}></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td class="buttons_row"></td>
                </tr>

                <tr class="last">
                    <td id="headb" align="left"></td>
                    <td  align="left">
                        <div class="noticePanel">
                            <span class="required">*</span> - Поля, обязательные для заполнения.<br/>
                            &mdash; Должна присутствовать ТОЛЬКО одна валюта с курсом <u>1 - базовая</u>. Иммено в этой валюте будут сохраняются прайсы<br/>
                            &mdash; Первая валюта будет добавлена валютой "<{$smarty.const.HEAD_DEFAULT}>" с курсом, номиналом и сортировкой равной 1<br/>
                            &mdash; <{$smarty.const.HEAD_RATE}> в других валютах - это <{$smarty.const.HEAD_COEFFICIENT}> относительно основной валюты где <{$smarty.const.HEAD_RATE}> равен 1<br/>
                            &mdash; Валюта выбранная "По умолчанию для отображения" будет грузится на сайте первой<br/>
                            &mdash; Только после добавления второй валюты будет возможно изменять валюту "<{$smarty.const.HEAD_DEFAULT}>"<br/>
                            &mdash; Номинал, курс и другие основные параметры валюты редактировать можно только в режиме вывода списка валют<br/>
                        <{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
                            &mdash; Если необходимо вывести число без "<{$smarty.const.HEAD_DECIMALS}>", выставтье его значение в 0<br/>
                            &mdash; Используется только первый символ в строке "<{$smarty.const.HEAD_THOUSAND_SEPARATOR}>". Исключением является "Неразрывный пробел" - <{'&nbsp;'|escape}><br/>
                            &mdash; В шаблоне символ '#' (объязателен) будет заменен на отформатированное по настройкам число при выводе (как в примере)<br/>
                            &mdash; В шаблоне можно использовать различные символы и сущности<br/>
                        <{/if}>
                        </div>
                    </td>
                    <td class="buttons_row"></td>
                </tr>
            </table>
        </li>
        <li id="tab_history">
            <{include file="common/object_actions_log.tpl" arHistoryData=$item.arHistory}>
        </li>
    </div>
</form>
            
<script type="text/javascript">
<!--
    var signTpl = "#";
    createTemplate(document.<{$arrPageData.task}>Form);

    function randomNumber(ln) {
        var num = parseInt(Math.random()*Math.pow(10, ln));
        while(!num || num.toString().length!=ln) num = randomNumber(ln);
        return num;
    }

    function setDefault(bt, form){
        var canDef = isDefined(1);
        $("select#def4calc [value='0']").attr("selected", "selected");
        if(canDef && parseFloat(bt.value)==1 && form.active.value==1){
            $("select#def4calc [value='1']").attr("selected", "selected");
        } else if(!canDef && parseFloat(bt.value)==1){
            bt.value = '';
        }
    }

    function setUnDefault(){
        $("select#def4calc [value='0']").attr("selected", "selected");
        $("select#def4show [value='0']").attr("selected", "selected");
    }

    function isDefined(type){
        return type==1 ? <{if !$objCurrency->def4calc}>1<{else}>0<{/if}> : <{if !$objCurrency->def4show}>1<{else}>0<{/if}>;
    }

    function formCheck(form){
        var errors      = 0;
        $.each($(form).find('.requirefield'), function(i, input) {
            if ( input.value.length==0 ){
                    if(!errors) $(this).focus();
                    $(this).addClass('required');
                    errors++;
            } else $(this).removeClass('required');
        });
        if(!errors) return true;
        else alert("<{$smarty.const.FEEDBACK_ALERT_ERROR}>");
        return false;
    }

    function createNumber(form){
        var decimals = parseInt(form.decimals.value);
        var number   = randomNumber(1)+form.thousands_sep.value+randomNumber(3);
        if(decimals) number += form.dec_point.value+randomNumber(decimals);
        return number;
    }

    function createTemplate(form){
        var number  = createNumber(form);
        
        var tpl = form.template.value;
        if($(form.template).attr('readonly') && !tpl.length)
            tpl = signTpl+' '+(form.sign.value.length ? form.sign.value : form.code.value);

        if(tpl.indexOf(signTpl)>=0) tpl = tpl.replace(signTpl, number);
        else tpl = number+tpl;
        
        $('#tpl_example').html(tpl);
    }

    function fillTemplateData(sel, form){
        var inp     = form.template;
        if(sel.value==signTpl) $(inp).removeAttr('readonly').addClass('focusField').focus();
        else {
            $(form.template).attr('readonly', 'readonly').removeClass('focusField');
            inp.value=sel.value;
        }
        if(!inp.value.length) inp.value=signTpl;
        createTemplate(form);
    }

    function correctThousandsSeparator(form){
        var strSel  = decodeURIComponent(form.thousands_separates.value);
        var strInp  = decodeURIComponent(form.thousands_sep.value);

        if(strSel==signTpl) $(form.thousands_sep).removeAttr('readonly').addClass('focusField').focus();
        else {
            $(form.thousands_sep).attr('readonly', 'readonly').removeClass('focusField');
            form.thousands_sep.value=form.thousands_separates.value;
        }

        if(strSel=='&nbsp;' || (strSel==signTpl && strInp.indexOf('&nbsp;')>=0)){
               form.thousands_sep.value='&nbsp;';
               form.unbreakspace.value=1;
        } else form.unbreakspace.value=0;
    }

    function checkTemplateInSelect(str){
        var bPresent = false;
        $('select#templates option').each(function(){
            if(decodeURIComponent(this.value)==str) bPresent = true;
        });
        return bPresent;
    }

    function addTemplateToSelect(tpl){
        if(!checkTemplateInSelect(tpl)) 
            $('select#templates').prepend( $('<option value="'+tpl+'">'+tpl.replace(signTpl, "1.234,50")+'</option>'));
    }

    function addTemplate(inp, form){
        if(inp.value.length){
            addTemplateToSelect(inp.value+signTpl);
            addTemplateToSelect(inp.value+' '+signTpl);
            addTemplateToSelect(signTpl+inp.value);
            addTemplateToSelect(signTpl+' '+inp.value);
            $('select#templates :first').attr('selected', 'selected');
            form.template.value = $('select#templates :selected').val();
            createTemplate(form);
        }
    }
//-->
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
    
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_CURRENCY}>
<div class="clear"></div>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems" onsubmit="return formCheck(this);">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_PUBLICATION}></td>
            <td id="headb" align="center" width="70"><{$smarty.const.HEAD_DEFAULT}></td>
            <td id="headb" align="center" width="25"><{$smarty.const.HEAD_CURRENCY_CODE}></td>
            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
            <td id="headb" align="center" width="40"><{$smarty.const.HEAD_NOMINAL}></td>
            <td id="headb" align="center" width="60"><{$smarty.const.HEAD_RATE}> / <{$smarty.const.HEAD_COEFFICIENT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>
<{section name=i loop=$items}>
         <tr>
        <{*    <td  align="center">
                <input type="checkbox" name="arItems[<{$items[i].id}>][active]" id="active_<{$items[i].id}>" class="activs" value="<{$items[i].active}>" onchange="setActive(this, <{$items[i].id}>);"<{if $items[i].active}> checked<{/if}> />
            </td> *}>
         <td  align='center'>
<{if $items[i].id==1}><{$smarty.const.HEAD_DENIED}>
<{elseif $items[i].active==1}>
            <a href="<{$arrPageData.current_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="Enable"
               onclick="setActive(this, <{$items[i].id}>);" data-active="<{$items[i].active}>" >
                <img src="<{$arrPageData.system_images}>check.png" alt="<{$smarty.const.USERS_ACTIVE}>" />
            </a>
<{else}>
            <a href="<{$arrPageData.current_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="No Enable"
               onclick="setActive(this, <{$items[i].id}>);" data-active="<{$items[i].active}>">
                <img src="<{$arrPageData.system_images}>un_check.png" alt="<{$smarty.const.USERS_NOACTIVE}>" />
            </a>
<{/if}>
        </td>
            <td  align="center">
                <input type="radio" name="def4calc" id="def4calc_<{$items[i].id}>" value="<{$items[i].id}>" onfocus="return checkActive(<{$items[i].id}>);"<{if $items[i].def4calc}> checked<{/if}> style="display:none;" />
                <input type="radio" name="def4show" id="def4show_<{$items[i].id}>" value="<{$items[i].id}>" onfocus="return checkActive(<{$items[i].id}>);"<{if $items[i].def4show}> checked<{/if}> />
            </td>
            <td  align="center"><{$items[i].code}></td>
            <td><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].name}></a></td>
            <td  align="center">
                <input type="text" name="arItems[<{$items[i].id}>][nominal]" id="nominal_<{$items[i].id}>" class="field" value="<{$items[i].nominal}>" style="width:30px;" />
            </td>
            <td  align="center">
                <input type="hidden" name="oldrate_<{$items[i].id}>" id="oldrate_<{$items[i].id}>" value="<{$items[i].rate}>" />
                <input type="text" name="arItems[<{$items[i].id}>][rate]" id="rate_<{$items[i].id}>" class="field rates" value="<{$items[i].rate}>" onchange="setDefCalc(this.form, this, <{$items[i].id}>);" style="width:50px;" />
            </td>

            <td  align="center">
                <input type="text" name="arItems[<{$items[i].id}>][order]" id="order_<{$items[i].id}>" class="field_smal" value="<{$items[i].order}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
            </td>
            <td  align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.HEAD_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.HEAD_EDIT}>" />
                </a>
            </td>
            <td  align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.HEAD_DELETE}>">
                   <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.HEAD_DELETE}>" title="<{$smarty.const.HEAD_DELETE}>" />
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
<script type="text/javascript">
<!--
    var bSubmit   = true;
    var oldActive = null;
    function setDefCalc(form, bt, val){
        var bGo    = true;
        var newVal = parseFloat($('#rate_'+val).val());
        $.each($(form).find('input.rates[type=text]'), function(i, input) {
            if( $(this).attr('id')!='rate_'+val && newVal==1 && parseFloat(input.value)==1){
                $('#rate_'+val).val($('#oldrate_'+val).val());
                alert('<{$smarty.const.CURRENCY_ONLY_ONE}>');
                bGo = false;
            }
        });
        if(bGo && parseFloat(bt.value)==1){
            for(var i=0; i<form.def4calc.length; i++) {
                form.def4calc[i].checked = false;
                if(form.def4calc[i].value==val) form.def4calc[i].checked = true;
            } bGo = false;
        } bSubmit = bGo;
    }
    function checkActive(val){
        if(!$('#active_'+val).attr('checked')){
            alert('<{$smarty.const.CURRENCY_DENY_SELECT}>');
            return false;
        } return true;
    }
    function setActive(bt, val){
    /*    var bGo = true;
        if(!bt.checked && ($('#def4calc_'+val).attr('checked') || $('#def4show_'+val).attr('checked')) ){
            alert('<{$smarty.const.CURRENCY_DENY_TURNOFF}>');
            bt.checked = true;
            bGo = false;
        } bSubmit = bGo;*/
        
    var bGo = true;
        if($(bt).attr('data-active')==1 && ($('#def4calc_'+val).attr('checked') || $('#def4show_'+val).attr('checked')) ){
            alert('<{$smarty.const.CURRENCY_DENY_TURNOFF}>');
            bt.checked = true;
            bGo = false;
        } bSubmit = bGo;
    }
    function formCheck(form){
        if(bSubmit) {
            var iCount  = 0;
            var sErrors = '';
            $.each($(form).find('input.rates[type=text]'), function(i, input) {
                if(parseFloat(input.value)==1) iCount++;
            });
            if(iCount>1)     sErrors += '<{$smarty.const.CURRENCY_ONLY_ONE}>';
            else if(!iCount) sErrors += '<{$smarty.const.CURRENCY_ONLY_ONE_EMPTY}>';

            for(i=0, iCount = 0; i<form.def4calc.length; i++)
                if(form.def4calc[i].checked) iCount++;
            if(iCount!=1) sErrors += '<{$smarty.const.CURRENCY_EMPTY_DEFAULT}>';

            for(i=0, iCount = 0; i<form.def4show.length; i++)
                if(form.def4calc[i].checked) iCount++;
            if(iCount!=1) sErrors += '<{$smarty.const.CURRENCY_SELECT_DEFAULT}>';

            var activs = $(form).find('input.activs[type=checkbox]');
            if(!activs.length) sErrors += '<{$smarty.const.CURRENCY_EMPTY_PUBLISH}>';
            iCount = 0;
            $.each(activs, function(i, input) {
                var id = $(this).attr('id').replace("active_", '');
                if(!$(this).attr('checked') && ($('#def4calc_'+id).attr('checked') || $('#def4show_'+id).attr('checked')) ) iCount++;
            });
            if(iCount) sErrors += '<{$smarty.const.CURRENCY_UNPUBLISHED_DEFAULT_SELECT}> "<{$smarty.const.HEAD_DEFAULT}>"!';

            if(bSubmit && sErrors=='') return true;
            else alert(sErrors);
        } else bSubmit = true;
        return false;
    }
//-->
</script>

<div class="noticePanel">
    <span class="required">*</span> - Поля, обязательные для заполнения.<br/>
    &mdash; Должна присутствовать ТОЛЬКО одна валюта с курсом <u>1 - базовая</u>. Иммено в этой валюте будут сохраняются прайсы<br/>
    &mdash; Первая валюта будет добавлена валютой "<{$smarty.const.HEAD_DEFAULT}>" с курсом, номиналом и сортировкой равной 1<br/>
    &mdash; <{$smarty.const.HEAD_RATE}> в других валютах - это <{$smarty.const.HEAD_COEFFICIENT}> относительно основной валюты где <{$smarty.const.HEAD_RATE}> равен 1<br/>
    &mdash; Валюта выбранная "По умолчанию для отображения" будет грузится на сайте первой<br/>
    &mdash; Только после добавления второй валюты будет возможно изменять валюту "<{$smarty.const.HEAD_DEFAULT}>"<br/>
    &mdash; Номинал, курс и другие основные параметры валюты редактировать можно только в режиме вывода списка валют<br/>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
    &mdash; Если необходимо вывести число без "<{$smarty.const.HEAD_DECIMALS}>", выставтье его значение в 0<br/>
    &mdash; Используется только первый символ в строке "<{$smarty.const.HEAD_THOUSAND_SEPARATOR}>". Исключением является "Неразрывный пробел" - <{'&nbsp;'|escape}><br/>
    &mdash; В шаблоне символ '#' (объязателен) будет заменен на отформатированное по настройкам число при выводе (как в примере)<br/>
    &mdash; В шаблоне можно использовать различные символы и сущности<br/>
<{/if}>
</div>
                
<{/if}>
</div>