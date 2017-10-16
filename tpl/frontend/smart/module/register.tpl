<{if $item.step==1}>
<h2>Регистрация на сайте</h2>
<br/>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <script type="text/javascript">
            $(function(){
                $('input.required').blur(function(){
                    checkField(this);
                });
            });
            
            function checkField(input){
                var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
                var regExpPhone = new RegExp("^([0-9 \-]{7,17})$");
                var errors = 0;
                
                if($(input).attr('name') == 'email') {
                    $.ajax({
                        url: '/interactive/ajax.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            zone: 'site',
                            action: 'checkUniqueEmail',
                            email: $(input).val()
                        },
                        success: function(json){
                            if(json.result) {
                                if(json.result=="success") {
                                    $(input).removeClass('error').addClass('valid');
                                    $(input).parent('td').next('td.msg').html('');
                                }
                                if(json.result=="error") {
                                    $(input).removeClass('valid').addClass('error');
                                    $(input).parent('td').next('td.msg').html('Такой e-mail уже зарегистрирован в системе');
                                    errors++;
                                }
                            }
                        }
                    });
                }
                
                if($(input).attr('name') == 'pass' || $(input).attr('name') == 'confpass') {
                    var pVal = $('input[name="pass"]').val();
                    var cpVal = $('input[name="confpass"]').val();
                    if(pVal.length>0 && cpVal.length>0) {
                        if(pVal != cpVal) {
                            $('input[name="confpass"]').parent('td').next('td.msg').html('Пароли не совпадают');
                        } else {
                            $('input[name="confpass"]').parent('td').next('td.msg').html('');
                        }
                    } else {
                        $('input[name="confpass"]').parent('td').next('td.msg').html('');
                    }
                }

                if ( 
                    $(input).val().length==0 || 
                    ($(input).attr('name') == 'phone' && $(input).val().match(regExpPhone) == null) ||
                    ($(input).attr('name') == 'email' && $(input).val().match(regExpEmail) == null)  
                ) {
                     errors++;
                    $(input).removeClass('valid').addClass('error');
                } else {
                    $(input).removeClass('error').addClass('valid');
                }
                return errors;
            }
        </script>
        <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
        <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
        <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
        </div>
            <form method="post" action="" id="<{$arCategory.module}>Form" name="<{$arCategory.module}>Form">
            <table class="userdataform" align="center" >
                <tbody>
                    <tr>
                        <td class="lbl">Имя:<font class="star">*</font></td>
                        <td class="frm">
                            <input class="input required" type="text" value="<{$item.firstname}>" name="firstname" />
                        </td>
                        <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.firstname)}>
                            <{$arrPageData.errors.firstname}>
<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Email:<font class="star">*</font></td>
                        <td class="frm">
                            <input class="input required" type="text" value="<{$item.email}>" name="email" />
                        </td>
                        <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.email)}>
                            <{$arrPageData.errors.email}>
<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Телефон:<font class="star">*</font></td>
                        <td class="frm">
                            <input class="input required" type="text" value="<{$item.phone}>" name="phone" />
                        </td>
                        <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.phone)}>
                            <{$arrPageData.errors.phone}>
<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Пароль:<font class="star">*</font></td>
                        <td class="frm">
                            <input class="input required" type="password" value="" name="pass" />
                        </td>
                        <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.pass)}>
                            <{$arrPageData.errors.pass}>
<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Повторите&nbsp;пароль:<font class="star">*</font></td>
                        <td class="frm">
                            <input class="input required" type="password" value="" name="confpass" />
                        </td>
                        <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.confpass)}>
                                <{$arrPageData.errors.confpass}>
<{/if}>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Проверочный код:<font class="star">*</font></td>
                        <td class="frm frm-cap">
                            <img border="0" align="left" src="/interactive/captcha.php?zone=site&sid=<{$Captcha->GetGeneratedSID()}>" alt="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>" title="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>, <{$smarty.const.FEEDBACK_CODE_CASE}>" />
                            <input type="hidden" name="captcha[sid]" id="captcha_sid" value="<{$Captcha->GetSID()}>" />
                            <input class="input conf-code required" type="text" name="captcha[code]" id="captcha_code" value="<{*$Captcha->GetGeneratedCode()*}>" maxlength="<{$Captcha->GetCodeLength()}>" title="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>, <{$smarty.const.FEEDBACK_CODE_CASE}>" />
                            <div class="clear"></div>
                        </td>
                        <td>
                            <td class="msg" style="color: red;">
<{if isset($arrPageData.errors.captcha)}>
                                <{$arrPageData.errors.captcha}>
<{/if}>
                            </td>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <div class="subscribe">
                                <input id="subscribe" name="subscribe" type="checkbox" value="1"<{if isset($item.subscribe) OR empty($arrPageData.errors)}> checked<{/if}> />
                                <label for="subscribe">подписатся на рассылку</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl"></td>
                        <td class="frm" align="center">
                            <a href="javascript:void(0);" onclick="$('#<{$arCategory.module}>Form').submit();" class="confirm"><span>Подтвердить</span></a>
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="3" class="info">
                            <font class="star">*</font> - поля обязательные для заполнения
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </td></tr></table>
</div>
<{elseif $item.step==2}>
<h2>Регистрация прошла успешно!</h2>
<br/>
<br/>
Вы можете <a style="color: blue; text-decoration: underline;" href="<{include file='core/href.tpl' arCategory=$arrModules.authorize}>">войти</a> на сайт используя логин и пароль.
<{elseif $item.action=='confirm'}>
<{include file='core/register_confirm.tpl'}>
<{/if}>



