<div class="m-title mt-oformlenia">
    <div class="mtc-l">Подтверждение регистрации.</div>
</div>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>         
        <script type="text/javascript">
            <!--
            function formCheck(form){
                var arErrors = new Array();
                var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");

                if(form.confirmcode.value === "" && form.email.value === "") {
                    arErrors.unshift('Введите пожалуйста код подтверждения регистрации!');
                    form.login.focus();
                }
                if ( arErrors.length > 0){
                    $('#messages').html('<ul><li>'+arErrors.join("</li><li>")+'</li></ul>')
                                  .removeAttr("class")
                                  .addClass("error");
                } else {
                    form.submit();
                    return true;
                };
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
                    <tr>
                        <td class="lbl">Код:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input class="inputs" type="text" value="" name="confirmcode" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl"></td>
                        <td class="frm">
                            <input class="inputs" type="submit" name="submitConfirmCode" value="Enter" onclick="return formCheck(this.form);" style="display:none;" />
                            <a id="submitForm" href="javascript:void(0);" onclick="formCheck(document.<{$arCategory.module}>Form);" class="obtn"><span>Отправить</span></a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </td></tr></table>
</div>

