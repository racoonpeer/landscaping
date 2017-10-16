<h2>Авторизация на сайте</h2>

<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <script type="text/javascript">
            <!--
            var banTimerID = null;
            var banTimerRunning = false;
<{if $item.bannedTime>0}>
            // Запускаем отсчет
            startBanTimer();
            //Функции
            function startBanTimer() {
                stopBanTimer(); // Убедиться, что часы остановлены
                banTimerID = setInterval("updateBanTimer()", 1000);
                banTimerRunning = true;
                $('#submitForm').unbind('click');
            }
            function stopBanTimer() {
                if(banTimerRunning)      
                    clearInterval(banTimerID);
                banTimerRunning = false;
                $('#submitForm').bind('click');
                $('#messages').html('')
                              .removeAttr("class")
                              .addClass("hidden_block");
            }    
            function updateBanTimer(){
                var banDate = new Date(<{$item.bannedTime}>*1000); // seconds*1000 in JavaScripts Milisecons Timestamp
                var nowDate = new Date();
                if( banDate >= nowDate && document.getElementById('banTimer') !== null &&
                    nowDate.getFullYear() === banDate.getFullYear() &&
                    nowDate.getMonth()    === banDate.getMonth() &&
                    nowDate.getDate()     === banDate.getDate() ) {
                        var totalRemains = (banDate.getTime()-nowDate.getTime());
                        var RemainsSec=(parseInt(totalRemains/1000));
                        var RemainsFullDays=(parseInt(RemainsSec/(24*60*60)));
                        var secInLastDay=RemainsSec-RemainsFullDays*24*3600;
                        var RemainsFullHours=(parseInt(secInLastDay/3600));
                        //if (RemainsFullHours<10){RemainsFullHours="0"+RemainsFullHours};
                        var secInLastHour=secInLastDay-RemainsFullHours*3600;
                        var RemainsMinutes=(parseInt(secInLastHour/60));
                        //if (RemainsMinutes<10){RemainsMinutes="0"+RemainsMinutes};
                        var lastSec=secInLastHour-RemainsMinutes*60;
                        //if (lastSec<10){lastSec="0"+lastSec};
                        document.getElementById('banTimer').innerHTML = /*RemainsFullHours+" hours "+*/RemainsMinutes+' min '+lastSec+' sec';
                } else if(banTimerID !== null) {
                    stopBanTimer();
                }
            }
<{/if}>
            function formCheck(form){
                if(!banTimerRunning){
                    var arErrors = new Array();
<{if $item.showCaptcha}>
                    if($('#captcha_code').val() === "") {
                        arErrors.unshift('Введите пожалуйста Код подтверждения!');
                        $('#captcha_code').focus();
                    }
<{/if}>
                    if(form.pass.value === "") {
                        arErrors.unshift('Введите пожалуйста Ваш Пароль!');
                        form.pass.focus();
                    }
/*

                    var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
                    if (form.email.value.match(regExpEmail) == null){
                        if (form.email.value.search(";") != -1 || form.email.value.search(",") != -1 || form.email.value.search(" ") != -1  )
                             arErrors.unshift("Нельзя вводить более одного адреса email. Или вы ввели некорректный email адрес!");
                        else arErrors.unshift("Введите, пожалуйста, корректный Ваш электронный адрес!");
                        form.email.focus();
                    }
*/
                    if(form.login.value === "") {
                        arErrors.unshift('Введите пожалуйста Ваш Логин!');
                        form.login.focus();
                    }
                    // Display Errors
                    if ( arErrors.length > 0){
                        $('#messages').html('<ul><li>'+arErrors.join("</li><li>")+'</li></ul>')
                                      .removeAttr("class")
                                      .addClass("error");
                    } else {
                        form.submit();
                        return true;
                    };
                } else $('#submitForm').unbind('click');
                return false;
            }
            //-->
        </script>
        <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
        <{if !empty($arrPageData.errors)}>
            <{$arrPageData.errors|@implode:'<br/>'}>
        <{elseif !empty($arrPageData.messages)}>
            <{$arrPageData.messages|@implode:'<br/>'}>
        <{/if}>
        </div>
        <form method="post" action="" name="<{$arCategory.module}>Form" onsubmit="return formCheck(this);">
            <table class="userdataform" align="center">
                <tbody>
<{*
                    <tr>
                        <td class="lbl">Email:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="" name="email" />
                                </div>
                            </div>
                        </td>
                    </tr>
*}>
                    <tr>
                        <td class="lbl">Логин:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input class="input" type="text" value="" name="login" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Пароль:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input class="input" type="password" value="" name="pass" />
                                </div>
                            </div>
                        </td>
                    </tr>
<{if $item.showCaptcha}>
                    <tr>
                        <td class="lbl">Повторите&nbsp;цифры:</td>
                        <td class="frm frm-cap">
                            <img border="0" align="left" src="/interactive/captcha.php?zone=site&sid=<{$Captcha->GetGeneratedSID()}>" alt="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>" title="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>, <{$smarty.const.FEEDBACK_CODE_CASE}>" />
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="hidden" name="captcha[sid]" id="captcha_sid" value="<{$Captcha->GetSID()}>" />
                                    <input class="input conf-code" type="text" name="captcha[code]" id="captcha_code" value="<{*$Captcha->GetGeneratedCode()*}>" maxlength="<{$Captcha->GetCodeLength()}>" title="<{$smarty.const.FEEDBACK_CONFIRMATION_CODE}>, <{$smarty.const.FEEDBACK_CODE_CASE}>" />
                                </div>
                            </div>
                            <div class="clear"></div>
                        </td>
                    </tr>
<{/if}>
                    <tr>
                        <td class="lbl"></td>
                        <td class="frm">
                            <input class="input" type="submit" name="submitAuthForm" value="Enter" onclick="return formCheck(this.form);" style="display:none;" />
                            <a id="submitForm" href="javascript:void(0);" onclick="formCheck(document.<{$arCategory.module}>Form);" class="confirm"><span>Вход</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td class="frm" colspan="2" align="center">
                            <a href="<{include file='core/href.tpl' arCategory=$arrModules.register}>" title="Регистрация на сайте">Регистрация на сайте</a>
                            &nbsp; | &nbsp;
                            <a href="<{include file='core/href.tpl' arCategory=$arrModules.recovery}>" title="Восстановление пароля">Восстановление пароля</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </td></tr>
        <tr>
            <td>
                Войти на сайт с помощью: 
                <a href="http://www.facebook.com/dialog/oauth/?client_id=<{$OAuth->fb_appID}>&redirect_uri=<{$OAuth->getFBurl()|cat:"&option=token"|urlencode}>&response_type=token&scope=email,user_location" onclick="OAuth.dialog(this, 'fb'); return false;" target="_self">Facebook</a> 
                или 
                <a href="https://oauth.vk.com/authorize?client_id=<{$OAuth->vk_appID}>&redirect_uri=<{$OAuth->getVKurl()|urlencode}>&response_type=code" onclick="OAuth.dialog(this, 'vk'); return false;" target="_self">VKontakte</a>
            </td>
        </tr>
    </table>
</div>

