<{if $item.action=='result' && $item.result=='success'}>
<div class="dataform">
    <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
    </div>
</div>

<{else}>
<{if !empty($arCategory.text)}><{$arCategory.text}><{/if}>
<div class="dataform">
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
            var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
            var regExpPhone = new RegExp("^([0-9 \-]{7,17})$");
            var errors      = 0;
 
            $.each($(form).find('.requirefield'), function(i, input) {
                if ( input.value.length==0 // type=='text', type=='select-one', type=='textarea'
                 || (input.name=='email' && input.value.match(regExpEmail) == null) // type=='text', name=='email'
                 || (input.name=='phone' && input.value.match(regExpPhone) == null) // type=='text', name=='phone'
                    ){
                        if(!errors) $(this).focus();
                        $(this).addClass('required');
                        errors++;
                } else $(this).removeClass('required');
            });

            if(errors==0) return true;
            else alert("<{$smarty.const.FEEDBACK_ALERT_ERROR}>");
            return false;
        }
        //-->
    </script>
    <form method="post" action="" name="<{$arCategory.module}>Form" onsubmit="return formCheck(this);">
        <table id="feedback_form" cellspacing="2" cellpadding="2" width="100%" border="0">
            <tr>
                <td class="lbl"><{$smarty.const.FEEDBACK_FIRST_NAME}>:<span class="required">*</span> </td>
                <td class="frm"><input maxlength="100" name="firstname" value="<{if isset($item.firstname)}><{$item.firstname}><{/if}>" class="input requirefield" /></td>
            </tr>
            <tr>
                <td class="lbl"><{$smarty.const.FEEDBACK_TEL}>:<span class="required">*</span> </td>
                <td class="frm"><input maxlength="100" name="phone"  value="<{if isset($item.phone)}><{$item.phone}><{/if}>" class="input requirefield" /></td>
            </tr>
            <tr>
                <td class="lbl"><{$smarty.const.FEEDBACK_EMAIL}>:<span class="required">*</span> </td>
                <td class="frm"><input maxlength="100" name="email"  value="<{if isset($item.email)}><{$item.email}><{/if}>" class="input requirefield" /></td>
            </tr>
            <tr>
                <td class="lbl"><{$smarty.const.FEEDBACK_STRING_TEXT}>:<span class="required">*</span> </td>
                <td class="frm"><textarea name="text" rows="5" cols="40" class="textarea requirefield"><{if isset($item.text)}><{$item.text}><{/if}></textarea></td>
            </tr>
            <tr>
                <td class="lbl"><{$smarty.const.FEEDBACK_CODE}>:<span class="required">*</span> </td>
                <td class="frm frm-code">
                    <img border="0" align="left" src="/interactive/captcha.php?zone=site&sid=<{$Captcha->GetGeneratedSID()}>" class="conf-code-image" alt="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>" title="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>, <{$smarty.const.FEEDBACK_CODE_CASE}>" />
                    <input type="hidden" name="captcha[sid]" value="<{$Captcha->GetSID()}>" id="captcha_sid" />
                    <input type="text" name="captcha[code]" value="" maxlength="<{$Captcha->GetCodeLength()}>" class="input conf-code requirefield" id="captcha_code" title="<{$smarty.const.FEEDBACK_CODE_CASE}>" />
                </td>
            </tr>            
            <tr>
                <td class="lbl">&nbsp;</td>
                <td class="frm frm-notice"><small>* - <{$smarty.const.FEEDBACK_FILLING}></small></td>
            </tr>
            <tr>
                <td class="lbl">&nbsp;</td>
                <td class="frm frm-button">
                    <input type="submit" class="submit" value="<{$smarty.const.FEEDBACK_STRING_SEND}>" />
                </td>
            </tr>
        </table>
    </form>
</div>
<{/if}>