        <div class="form-layout">
            <div id="messages"<{if empty($arrPageData.errors) && empty($arrPageData.messages)}> class="hide"<{/if}>>
                <{if !empty($arrPageData.messages)}><div class="info"><{$arrPageData.messages|@implode:'<br/>'}></div><{/if}>
                <{if !empty($arrPageData.errors)}><div class="errors"><{$arrPageData.errors|@implode:'<br/>'}></div><{/if}>
            </div>
<{if $item.action=='success'}>
            <center>
                <table class="password-recovery-table form" align="center" cellspacing="0" cellpadding="0"><tbody>
                    <tr>
                        <td class="col2 frm-links" align="center">
                            <a href="<{include file='core/href.tpl' arCategory=$arrModules.authorize}>" title="<{$smarty.const.USER_LOGIN_TITLE}>" onclick="localRedirect(this);"><{$arrModules.authorize.title}></a>
                            &nbsp; | &nbsp;
                            <a href="<{include file='core/href.tpl' arCategory=$arrModules.register}>" title="<{$smarty.const.USER_REGISTER_TITLE}>" onclick="parentRedirect(this);"><{$arrModules.register.title}></a>
                            &nbsp; | &nbsp;
                            <a href="<{include file='core/href.tpl' arCategory=$arrModules.recovery}>" title="<{$smarty.const.TRY_AGAIN_ACTION}>" onclick="localRedirect(this);"><{$smarty.const.TRY_AGAIN_ACTION}></a>
                        </td>
                    </tr>
                </tbody></table>
            </center>
            
<{else}>
            <center>
                <form method="post" action="" name="<{$arCategory.module}>Form" onsubmit="return formCheck(this);">
                    <table class="password-recovery-table form" align="center" cellspacing="0" cellpadding="0"><tbody>
<{if $item.action=='generate'}>
                        <tr>
                            <td class="col1"><{$smarty.const.USERS_MAIL}>:<font></font></td>
                            <td class="col2">
                                <div class="input input-212"><div>
                                    <input type="text" name="email" value="" />
                                </div></div>
                            </td>
                        </tr>
<{elseif $item.action=='confirm'}>
                        <tr>
                            <td class="col1"><{$smarty.const.RECOVERY_CODE}>:<font></font></td>
                            <td class="col2">
                                <div class="input input-212"><div>
                                    <input type="text" name="checkword" value="" />
                                </div></div>
                            </td>
                        </tr>
<{/if}>
                        <tr>
                            <td class="col1"></td>
                            <td class="col2 frm-submit">
                                <input type="submit" name="submitRecoveryForm" value="<{$smarty.const.BUTTON_SEND}>" onclick="return formCheck(this.form);" style="display:none;" />
                                <a id="submitForm" href="javascript:void(0);" onclick="formCheck(document.<{$arCategory.module}>Form);" class="input-btn"><span><{if $item.action=='generate'}><{$smarty.const.BUTTON_SEND}><{elseif $item.action=='confirm'}><{$smarty.const.RECOVERY_TITLE}><{/if}></span></a>
                                <a href="javascript:void(0);" onclick="closeHS();" class="input-btn"><span><{$smarty.const.BUTTON_CLOSE}></span></a>
                            </td>
                        </tr>
                        <tr>
                            <td class="col2 frm-links" colspan="2" align="center">
                                <a href="<{include file='core/href.tpl' arCategory=$arrModules.authorize}>" title="<{$smarty.const.USER_LOGIN_TITLE}>" onclick="localRedirect(this);"><{$arrModules.authorize.title}></a>
                                &nbsp; | &nbsp;
                                <a href="<{include file='core/href.tpl' arCategory=$arrModules.register}>" title="<{$smarty.const.USER_REGISTER_TITLE}>" onclick="parentRedirect(this);"><{$arrModules.register.title}></a>
                            </td>
                        </tr>
                    </tbody></table>
                </form>
            </center>        
            <script type="text/javascript">
                <!--
                function formCheck(form){
                    var arErrors = new Array();
<{if $item.action=='generate'}>
                    var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
                    if (form.email.value.match(regExpEmail) === null){
                        if (form.email.value.search(";") !== -1 || form.email.value.search(",") !== -1 || form.email.value.search(" ") !== -1  )
                             arErrors.unshift('<{$smarty.const.FEEDBACK_EMAIL_MULTI_ERROR}>');
                        else arErrors.unshift('<{$smarty.const.FEEDBACK_EMAIL_ERROR}>');
                        form.email.focus();
                    }
<{elseif $item.action=='confirm'}>
                    if(form.checkword.value === "") {
                        arErrors.unshift('<{$smarty.const.FEEDBACK_FILL_REQUIRED_FIELD|sprintf:$smarty.const.FEEDBACK_CONFIRMATION_CODE}>');
                        form.checkword.focus();
                    }
<{/if}>
                    if ( arErrors.length > 0){
                        $('#messages').html('').show().append('<div class="errors"><ul><li>'+arErrors.join('</li><li>')+'</li></ul></div>');
                    } else {
                        form.submit();
                        return true;
                    };
                    return false;
                }
                //-->
            </script>
<{/if}>
        </div>

