<{if $objUserInfo->logined && !empty($item.id) && $arrPageData.action=='edit'}>
<div class="m-title mt-oformlenia">
    <div class="mtc-l">Редактирование профиля</div>
</div>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <script type="text/javascript">
        <!--
            function formCheck(form){
                var arErrors = new Array();
                var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
                var regExpPhone = new RegExp("^([0-9 \-]{7,17})$");
                // Required: address, city, phone, surname, firstname, currpass, confpass, pass, email //, login
                if(form.address.value === "") {
                    arErrors.unshift('Введите пожалуйста Ваш адресс!');
                    form.address.focus();
                }
                if(form.city.value === "") {
                    arErrors.unshift('Введите пожалуйста Ваш город!');
                    form.city.focus();
                }
                if(form.phone.value === "" || form.phone.value.match(regExpPhone) === null) {
                    arErrors.unshift('Введите пожалуйста Ваш телефон!');
                    form.phone.focus();
                }             
                if(form.surname.value === "") {
                    arErrors.unshift('Введите пожалуйста Вашу фамилию!');
                    form.surname.focus();
                }  
                if(form.firstname.value === "") {
                    arErrors.unshift('Введите пожалуйста Ваше Имя!');
                    form.firstname.focus();
                }
<{if $objUserInfo->type!='Administrator'}>
                if(form.currpass.value === "") {
                    arErrors.unshift('Чтобы изменить данные, введите пожалуйста Ваш Текущий Пароль!');
                    form.currpass.focus();
                }
<{/if}>
                if(form.pass.value !== "" || form.confpass.value !== "") {
                    if(form.pass.value === "") {
                        arErrors.unshift('Введите пожалуйста Ваш Новый Пароль!');
                        form.pass.focus();
                    }
                    if(form.confpass.value === "") {
                        arErrors.unshift('Подтвердите пожалуйста Ваш Новый пароль!');
                        form.confpass.focus();
                    }
                    if(form.pass.value !== form.confpass.value) {
                        arErrors.unshift('Ваш Новый Пароль и Подтверждение Нового пароля не совпадают!');
                        form.pass.focus();
                    }
                }
                if (form.email.value.match(regExpEmail) === null){
                    if (form.email.value.search(";") !== -1 || form.email.value.search(",") !== -1 || form.email.value.search(" ") !== -1  ) 
                         arErrors.unshift("Нельзя вводить более одного адреса email. Или вы ввели некорректный email адрес!");
                    else arErrors.unshift("Введите, пожалуйста, корректный Ваш электронный адрес!");
                    form.email.focus();
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
                        <td class="lbl">Email:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="hidden" value="<{$item.email}>" name="curremail" />
                                    <input type="text" value="<{$item.email}>" name="email" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr<{if $objUserInfo->type=='Administrator'}> style="display:none;"<{/if}>>
                        <td class="lbl">Текущий&nbsp;пароль:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="password" value="" name="currpass" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Новый&nbsp;пароль:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="password" value="" name="pass" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Повторите&nbsp;пароль:</td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="password" value="" name="confpass" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Имя:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="<{$item.firstname}>" name="firstname" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Отчество: </td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="<{$item.middlename}>" name="middlename" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Фамилия:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="<{$item.surname}>" name="surname" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Телефон:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="<{$item.phone}>" name="phone" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Город:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-input">
                                <div class="wp2-input">
                                    <input type="text" value="<{$item.city}>" name="city" />
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Адрес:<font>*</font></td>
                        <td class="frm">
                            <div class="wp1-textarea">
                                <div class="wp2-textarea">
                                    <textarea name="address" cols="50" rows="7"><{$item.address}></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Дополнительная&nbsp;&nbsp;&nbsp;<br />информация:<font>&nbsp;</font></td>
                        <td class="frm">
                            <div class="wp1-textarea">
                                <div class="wp2-textarea">
                                    <textarea name="descr" cols="50" rows="7"><{$item.descr}></textarea>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <div class="subscribe">
                                <input id="subscribe" name="subscribe" type="checkbox" value="1"<{if !empty($item.subscribe)}> checked<{/if}> />
                                <label for="subscribe">подписатся на рассылку</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl"></td>
                        <td class="frm">
                            <a href="javascript:void(0);" onclick="formCheck(document.<{$arCategory.module}>Form);" class="obtn"><span>Подтвердить</span></a>
                            <a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$item}>" class="obtn"><span>Отменить</span></a>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="info">
                            <font class="star">*</font> - поля обязательные для заполнения
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>
    </td></tr></table>
</div>
<{elseif $objUserInfo->logined && !empty($item.id)}>
<div class="m-title mt-oformlenia">
    <div class="mtc-l">Мой профиль</div>
</div>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
        <{if !empty($arrPageData.errors)}>
            <{$arrPageData.errors|@implode:'<br/>'}>
        <{elseif !empty($arrPageData.messages)}>
            <{$arrPageData.messages|@implode:'<br/>'}>
        <{/if}>
        </div>
        <table class="userdataform" align="center">
            <tbody>
                <tr>
                    <td class="lbl">Email:</td>
                    <td class="frm"><{$item.email}></td>
                </tr>
                <tr>
                    <td class="lbl">Имя:</td>
                    <td class="frm"><{$item.firstname}></td>
                </tr>
                <tr>
                    <td class="lbl">Отчество: </td>
                    <td class="frm"><{$item.middlename}></td>
                </tr>
                <tr>
                    <td class="lbl">Фамилия:</td>
                    <td class="frm"><{$item.surname}></td>
                </tr>
                <tr>
                    <td class="lbl">Телефон:</td>
                    <td class="frm"><{$item.phone}></td>
                </tr>
                <tr>
                    <td class="lbl">Город:</td>
                    <td class="frm"><{$item.city}></td>
                </tr>
                <tr>
                    <td class="lbl">Адрес:</td>
                    <td class="frm"><{$item.address}></td>
                </tr>
                <tr>
                    <td class="lbl">Дополнительная&nbsp;&nbsp;&nbsp;<br />информация:<font>&nbsp;</font></td>
                    <td class="frm"><{$item.descr}></td>
                </tr>
                <tr>
                    <td colspan="2"><{if $item.subscribe}>Подписан<{else}>Неподписан<{/if}> на рассылку</td>
                </tr>
                <tr>
                    <td class="lbl"></td>
                    <td class="frm"><a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$item params='action=edit'}>" class="obtn"><span>Редактировать</span></a></td>
                </tr>
            </tbody>
        </table>
    </td></tr></table>
</div>
<{elseif !empty($items)}>
<div class="m-title mt-oformlenia">
    <div class="mtc-l">Пользователи</div>
</div>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
        <{if !empty($arrPageData.errors)}>
            <{$arrPageData.errors|@implode:'<br/>'}>
        <{elseif !empty($arrPageData.messages)}>
            <{$arrPageData.messages|@implode:'<br/>'}>
        <{/if}>
        </div>
                
        <div class="items">
<{section name=i loop=$items}>
            <table class="item">
                <tr>
                    <td class="i" rowspan="2">
                        <a class="title" href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}>">
                            <img alt="" src="<{$items[i].small_image}>" />
                        </a>
                    </td>
                    <td class="t">
                        <p><a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}>"><{$items[i].title}></a></p>
                    </td>
                </tr>
                <tr>
                    <td class="t">
                        <p><a class="detail" href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}>">Детальная информация</a></p>
                    </td>
                </tr>
            </table>
<{/section}>
        </div>
        <!-- Список пользователей -->
<{if $arrPageData.total_pages>1}>
        <div class="pager">
<!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=0}>
<!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <div class="cf-clear"></div>
        </div>
<{/if}>
    </td></tr></table>
</div>

<{else}>
<div class="m-title mt-oformlenia">
    <div class="mtc-l">Ошибка доступа</div>
</div>
<div class="m-body mb-oformlenia">
    <!-- помещаем в контейнер для static отображения текста-->
    <table class="fix-position"><tr><td>
        <div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
        <{if !empty($arrPageData.errors)}>
            <{$arrPageData.errors|@implode:'<br/>'}>
        <{elseif !empty($arrPageData.messages)}>
            <{$arrPageData.messages|@implode:'<br/>'}>
        <{/if}>
        </div>
        <p>Доступ к данной странице закрыт.</p>
        <p><a href="<{include file='core/href.tpl' arCategory=$arrModules.authorize}>" class="obtn" title="Авторизация"><span><{$arrModules.authorize.title}></span></a></p>
    </td></tr></table>
</div>
<{/if}>
