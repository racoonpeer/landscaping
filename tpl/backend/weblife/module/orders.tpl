<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.ORDERS creat_title=$smarty.const.ADMIN_CREATING_NEW_ORDER edit_title=$smarty.const.ADMIN_EDIT_ORDER}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
<div id="right_block">
<{* +++++++++++++++++ SHOW EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='editItem'}>
<form method="post" id="orderForm" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form"  enctype="multipart/form-data">
    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Информация о заказе</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="0" cellspacing="5" cellpadding="1" class="list" id="orderTable" style="border-bottom:1px solid #b8b8b8">  
                    <tr>
                        <td width="320" style="border-right:1px solid #b8b8b8" valign="top">
                            <strong>Информация о заказе</strong><br/><br/>
                            <table width="100%" border="1" cellspacing="1" cellpadding="1" class="list order" style="text-align: left;">
                                <tbody>
                                    <tr>
                                        <td width="40%">№ заказа</td>
                                        <td><strong><{$item.id}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Статус заказа</td>
                                        <td><strong id="status"><{$item.arStatus.title}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Дата создания</td>
                                        <td><strong><{$item.created|date_format:"%d.%m.%Y %H:%M"}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Кол-во товаров</td>
                                        <td><strong><{$item.qty}></strong></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td>Имя</td>
                                        <td>
                                            <input type="text" name="firstname" value="<{$item.firstname}>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>E-mail</td>
                                        <td>
                                            <input type="text" name="email" value="<{$item.email}>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Телефон</td>
                                        <td>
                                            <input type="text" name="phone" value="<{$item.phone}>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Адрес</td>
                                        <td>
                                            <textarea name="address" rows="4" style="height: 60px;"><{$item.address}></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Доставка</td>
                                        <td><strong id="shipping"><{$item.arShipping.title}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Оплата</td>
                                        <td><strong id="payment"><{$item.arPayment.title}></strong></td>
                                    </tr>
                                    <tr>
                                        <td>Комментарий к заказу</td>
                                        <td>
                                            <textarea name="descr" rows="4" style="height: 60px;"><{$item.descr}></textarea>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <br/>
                            <br/>
                            <div>
                                <a class="buttons left" data-option='confirm' data-confirm="1" onclick="UpdateOrder(this);" style="width:200px;">
                                    &nbsp;Отправить подтверждение заказа&nbsp;
                                </a>
                                <span id="confirm" class="left" style="margin:10px;"><{if $item.confirmed==1}>
                                    <{$smarty.const.ORDER_CONFIRM_LETTER_SEND}>
                                <{else}>
                                    <{$smarty.const.ORDER_CONFIRM_LETTER_NOT_SEND}>
                                <{/if}>
                                </span>
                            </div>
                        </td>
                        <td valign="top">
                            <strong>Товары</strong><br/><br/>
                            <{include file='ajax/order_products.tpl' arItems=$item.children price=$item.price sPrice=$item.shipping_price}>
                            <script type="text/javascript">
                                
                                $(function(){
                                    OP.setup();
                                });
                                
                                var OP = {
                                    update: function() {
                                        var _self = this;
                                        $.ajax({
                                            url: '/interactive/ajax.php',
                                            type: 'GET',
                                            dataType: 'json',
                                            data: {
                                                zone: 'admin',
                                                action: 'orderProducts',
                                                option: 'update',
                                                itemID: <{$item.id}>
                                            }, 
                                            success: function(json) {
                                                if(json.output) {
                                                    $('#orderProducts').replaceWith(json.output);
                                                    _self.setup();
                                                }
                                                if(json.history) {
                                                    $('#history').html(json.history);
                                                }
                                            }
                                        });
                                    },
                                    setup: function() {
                                        var _self = this;
                                        var arExItems = new Array();
                                        $.each($('#orderProducts').children('tbody').find('tr'), function(i, tr){
                                            arExItems.push($(tr).data('pid'));
                                        });
                                        $("#orderProductsSearch").autocomplete({
                                            minLength: 3,
                                            source: function(request, response) {
                                                $.ajax({
                                                    url: "/interactive/ajax.php",
                                                    type: 'GET',
                                                    dataType: "json",
                                                    data: {
                                                        zone: 'admin',
                                                        action: 'orderProducts',
                                                        option: 'search',
                                                        itemID: <{$item.id}>,
                                                        stext: request.term,
                                                        exItems: arExItems
                                                    },
                                                    success: function(json) {
                                                        if(json.items) {
                                                            response($.map(json.items, function(item) {
                                                                return {
                                                                    label: item.title + ' (' + item.ctitle + ')',
                                                                    value: item.title,
                                                                    id: item.id,
                                                                    type: item.type
                                                                }
                                                            }));
                                                        }
                                                    }
                                                });
                                            },
                                            select: function(event, ui) {
                                                _self.add(ui.item.id, ui.item.type);
                                            }
                                        }); 
                                    },
                                    add: function(PID, TYPE) {
                                        var _self = this;
                                        $.ajax({
                                            url: "/interactive/ajax.php",
                                            type: 'GET',
                                            dataType: "json",
                                            data: {
                                                zone: 'admin',
                                                action: 'orderProducts',
                                                option: 'add',
                                                itemID: <{$item.id}>,
                                                pid: PID,
                                                type: TYPE
                                            },
                                            complete: function(){
                                                _self.update();
                                            }
                                        });
                                    },
                                    delete: function(PID) {
                                        var _self = this;
                                        $.ajax({
                                            url: "/interactive/ajax.php",
                                            type: 'GET',
                                            dataType: "json",
                                            data: {
                                                zone: 'admin',
                                                action: 'orderProducts',
                                                option: 'delete',
                                                itemID: <{$item.id}>,
                                                pid: PID
                                            },
                                            complete: function(){
                                                _self.update();
                                            }
                                        });
                                    },
                                    recalc: function(PID) {
                                        var _self = this;
                                        var QTY = $('#orderProducts').children('tbody').find('input#qty_'+PID).val(),
                                            Inputs = $('#orderProducts').children('tbody').children('tr#product_' + PID).children("td:nth-of-type(3)").find("select, input[type='checkbox']:checked"),
                                            Options = {};
                                        if (Inputs.length) {
                                            $.each(Inputs, function (j, input) {
                                                var pid = $(input).data("pid"),
                                                    oid = $(input).data("oid"),
                                                    similar = $(Inputs).parent().find("input[data-oid='" + oid + "']:checked"),
                                                    arVal   = new Array();
                                                if (!array_key_exists(pid, Options)) Options[pid] = {};
                                                if (similar.length) {
                                                    $.each(similar, function (j, cb) {
                                                        arVal.push(cb.value)
                                                    });
                                                    Options[pid][oid] = arVal;
                                                } else {
                                                    Options[pid][oid] = parseInt(input.value);
                                                }
                                            });
                                        }
                                        $.ajax({
                                            url: "/interactive/ajax.php",
                                            type: 'GET',
                                            dataType: "json",
                                            data: {
                                                zone: 'admin',
                                                action: 'orderProducts',
                                                option: 'recalc',
                                                itemID: <{$item.id}>,
                                                pid: PID,
                                                qty: QTY,
                                                options: Options
                                            },
                                            complete: function () {
                                                _self.update();
                                            }
                                        });
                                    }
                                }
                            </script>
                            <div class="loader hidden"><img src="/images/loader.gif"/></div>
                            <table width="100%" cellspacing="0" cellpadding="1" class="list">
                                <tr>
                                   <td style="padding:10px;">
                                       <strong>Комментарий администратора</strong><br/><br/>
                                       <textarea class="nosize_field" style="width: 440px; height: 36px" name="admin_comment" ><{$item.admin_comment}></textarea>
                                       <a class="buttons right"  data-option="admin_comment" onclick="UpdateOrder(this);">Сохранить комментарий</a>
                                   </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <div class="loader hidden"><img src="/images/loader.gif"/></div>
                <table width="100%" border="0" cellspacing="0" cellpadding="1" class="list order" id="editarea">
                    <tr>
                        <td  width="100">Статус заказа:</td>
                        <td width="150">
                            <select name="id" class="nosize_field" style="width: 140px;" onchange="unsetDisabled(this)">       
<{section name=i loop=$arrPageData.arStatus}>
                                    <option value="<{$arrPageData.arStatus[i].id}>" <{if $arrPageData.arStatus[i].id==$item.arStatus.id}>selected<{/if}>>
                                        <{$arrPageData.arStatus[i].title}>
                                    </option>
<{/section}>
                            </select>
                        </td>
                        <td style="padding:10px;">
                            <textarea class="nosize_field" disabled style="width: 100%; height: 50px" name="comment"></textarea>
                        </td>
                        <td width="90">
                            <a class="buttons disabled" data-status="<{$item.status}>" data-option="status" onclick="UpdateOrder(this);">Сохранить</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Оплата:</td>
                        <td>
                            <select name="id" class="nosize_field" style="width: 140px;" onchange="unsetDisabled(this)">       
<{section name=i loop=$arrPageData.arPayments}>
                                    <option value="<{$arrPageData.arPayments[i].id}>" <{if $arrPageData.arPayments[i].id==$item.arPayment.id}>selected<{/if}>>
                                        <{$arrPageData.arPayments[i].title}>
                                    </option>
<{/section}>
                            </select>
                        </td>
                        <td  style="padding:10px;">
                            <textarea class="nosize_field" disabled style="width: 100%; height: 50px" name="comment"></textarea>
                        </td>
                        <td >
                            <a class="buttons disabled" data-payment="<{$item.payment}>" data-option="payment" onclick="UpdateOrder(this);">Сохранить</a>
                        </td>
                    </tr>
                    <tr>
                        <td >Доставка:</td>
                        <td >
                            <select name="id" class="nosize_field" style="width: 140px;" onchange="unsetDisabled(this)">       
<{section name=i loop=$arrPageData.arShippings}>
                                    <option value="<{$arrPageData.arShippings[i].id}>" <{if $arrPageData.arShippings[i].id==$item.arShipping.id}>selected<{/if}>><{$arrPageData.arShippings[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td  style="padding:10px;">
                            <textarea class="nosize_field" style="width: 100%; height: 50px" name="comment" disabled></textarea>
                        </td>
                        <td >
                            <a class="buttons disabled" data-shipping="<{$item.shipping}>" data-option="shipping" onclick="UpdateOrder(this);">Сохранить</a>
                        </td>
                    </tr>
                </table>
                <div style="text-align: center; margin: 15px 0;">
                    <input type="submit" name="submit" class="buttons" style="display: inline-block;" value="Сохранить заказ"/>
                    <input class="buttons" name="submit_cancel" type="submit" style="display: inline-block;"  value="<{$smarty.const.BUTTON_CANCEL}>" onclick="return userConfirm('cancel', '<{$smarty.const.CONFIRM_NOT_SAVE}>')" />
                    <input class="buttons" name="submit_delete" type="submit" style="display: inline-block;" value="<{$smarty.const.BUTTON_DELETE}>" onclick="return userConfirm('delete', '<{$smarty.const.CONFIRM_DELETE}>')" />
                </div>
                <div id="history">                    
                    <{include file='common/object_actions_log.tpl' arHistoryData=$item.arHistory}>
                </div>
            </li>
        </ul>
    </div>
</form>                

<script type="text/javascript">
    function userConfirm(task, message) {
        if(window.confirm(message)) {
            switch (task) {
              case 'copy':
                window.location='<{$arrPageData.current_url|cat:"&task=addItem&copyID="|cat:$item.id}>';
                break
              case 'clear':
                document.forms[0].reset();
                $.each(tinyMCE.editors, function() {
                    this.setContent('');
                }); 

                $.each($('input:text, textarea'), function() {
                    $(this).val('');
                });
                if($('select').length>0){
                    $.each($('select'), function(){
                        $('option', this).removeAttr("selected");
                        $('option:nth(0)', this).attr("selected", "selected");
                    });
                }
                // catalog clear
                if($('#attrTable').length > 0) {
                    $('#attrTable tbody').html('');
                }
                if($('#list_settings_selected_related').length >0 ) {
                    $('#list_settings_selected_related').html('');
                }
                if($('#list_settings_all_related').length >0 ) {
                    $('#list_settings_all_related').html('');
                }
                if($('#list_settings_all_kits').length >0 ) {
                    $('#list_settings_all_kits').html('');
                }
                if($('#list_settings_selected_kits').length >0 ) {
                    $('#list_settings_selected_kits').html('');
                }
                // main clear
                if($('#attrGroupsList').length>0) {
                    $.each($('#attrGroupsList').find('input:checkbox'), function() {
                        $(this).removeAttr('checked');
                        updateAttributesList(this);
                    });
                }
                if($('#filtersAllList').length>0) {
                    $.each($('#filtersAllList').find('input:checkbox'), function() {
                        $(this).removeAttr('checked');
                        updateFiltersList(this);
                    });
                }
                //attributes
                if($('#defaultVals').length>0) {
                    $('#defaultVals').html('');
                }

                break
              case 'cancel':
                window.location='<{$arrPageData.current_url|cat:$arrPageData.filter_url}>';
                break
              case 'delete':
                window.location='<{$arrPageData.current_url|cat:"&task=deleteItem&itemID="|cat:$item.id}>';
                break
            }   
        }
        return false; 
    }
    
    function UpdateOrder(link) { 
        var option = $(link).attr('data-option');
        var optionID = $(link).closest('tr').find('select option:selected').val();
        var value = $(link).closest('tr').find('textarea').val();
        var loader = $(link).closest('table').prev('.loader');

        if((typeof optionID != "undefined" && $(link).attr('data-'+option) != optionID) || 
           (option=='admin_comment' && value.length!=0 && window.confirm('Сохранить комментарий?')) ||
            option=='confirm' 
        ) {
            if($(loader).length>0) {
                $(loader).css('width', $(loader).next('table').width());
                $(loader).css('height', $(loader).next('table').height());
                $(loader).find('img').css('margin-top', ($(loader).next('table').height()/2 - 10));
                $(loader).removeClass('hidden');
            }
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'editOrder',
                    orderID: '<{$item.id}>',
                    option: option,
                    optionID: optionID,
                    optionComment: value
                },
                success: function(json){
                    if(json){
                        if(json.option_title) $('#'+option).text(json.option_title);
                        if(json.history) $('#history').html(json.history);
                        
                        $(link).closest('tr').find('select option:selected').removeAttr('selected');
                        $(link).closest('tr').find('select option[value="'+optionID+'"]').prop('selected', 'true');
                        if(option!='admin_comment' && option!='confirm') $(link).closest('tr').find('textarea').val('');
                        $(link).attr('data-'+option, optionID);
                        
                        unsetDisabled($(link).closest('tr').find('select'));
                        if($(loader).length>0) {
                            $(loader).addClass('hidden');
                        }
                    }
                }
            });
        }
    }
    
    function unsetDisabled(item) {
        var option = $(item).closest('tr').find('a').attr('data-option');
        if($(item).closest('tr').find('a').attr('data-'+option) != $('option:selected', item).val()) { 
             $(item).closest('tr').find('textarea').removeAttr('disabled');
             $(item).closest('tr').find('a').removeClass('disabled');
        } else {
             $(item).closest('tr').find('textarea').attr('disabled', 'true');
             $(item).closest('tr').find('a').addClass('disabled');
        }
    }
</script>
<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}>
<{include file='common/new_page_btn.tpl' title="Новый заказ"}>
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>       
<form method="GET" id="filtersForm" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url}>">
    <input type="hidden" name="module" value="<{$arrPageData.module}>" />
    <input type="hidden" name="cid" value="<{$arrPageData.cid}>" />
<{if !empty($arrPageData.orders_url)}>
<{foreach name=i from=$arrPageData.orders_url item=name key=value}>
            <input type="hidden" value="<{$name}>" name="<{$value}>"/>
<{/foreach}>
<{/if}>
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr style="height:35px">
            <td width="150">Показать:</td> 
<{section name=j loop=$arrPageData.arStatus}>
            <td align="center"><label><input type="checkbox" <{if isset($arrPageData.filters.status) && in_array($arrPageData.arStatus[j].id, $arrPageData.filters.status)}>checked<{/if}> value="<{$arrPageData.arStatus[j].id}>" name="filters[status][]"/> <{$arrPageData.arStatus[j].title}></label></td>
<{/section}>
            <td >за период: 
                c <input type="text" class="nosize_field" id="date_from" size="7" name="filters[date][from]" value="<{if isset($arrPageData.filters.date.from) && $arrPageData.filters.date.from}><{$arrPageData.filters.date.from}><{/if}>"/> <a href="javascript:void(0)" onclick="$('#date_from').val('')">x</a>
                по <input type="text" size="7" class="nosize_field" id="date_to" name="filters[date][to]" value="<{if isset($arrPageData.filters.date.to) && $arrPageData.filters.date.to}><{$arrPageData.filters.date.to}><{/if}>"/> <a href="javascript:void(0)" onclick="$('#date_to').val('')">x</a></td> 
            <td  width="150" align="right" rowspan="2">
                <button type="submit" class="buttons"><{$smarty.const.SITE_FOUND}></button>
            </td>
        </tr>
        <tr style="height:35px">
            <td>Поиск по заказам:</td>
            <td colspan="<{$arrPageData.arStatus|count}>">
                <input size="67" type="text" placeholder="введите имя и/или фамилию или номер заказа" class="field" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
            </td>
            <td>
                Тип заказа: &nbsp;
                <select name="filters[type]">
                    <option value=""> -- Не выбрано -- </option>
<{section name=i loop=$arrPageData.arTypes}>
                    <option value="<{$arrPageData.arTypes[i].id}>" <{if isset($arrPageData.filters.type) AND $arrPageData.filters.type==$arrPageData.arTypes[i].id}>selected<{/if}>><{$arrPageData.arTypes[i].title}></option>
<{/section}>
                </select>
            </td>
        </tr>
    </table>
</form>

<script type="text/javascript">
<!--
    var dateFrom = $('#date_from');
    var dateTo = $('#date_to');

    $(dateFrom).datepicker({timeFormat: "HH:mm:ss",dateFormat: "yy-mm-dd"});
    $(dateTo).datepicker({timeFormat: "HH:mm:ss",dateFormat: "yy-mm-dd"});
    
    $(dateFrom).on('change', function(){ 
        $(dateTo).datepicker('option', 'minDate', $(dateFrom).val()); 
    });    
              
    $(function() {        
        $('#categorySearch').autocomplete({
            source: function(request, response) {
                $.ajax({
                    url: '/interactive/ajax.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        zone: 'admin',
                        action: 'liveSearch',
                        module: '<{$arrPageData.module}>',
                        cid: <{$arrPageData.cid}>,
                        searchStr: request.term
                    }, 
                    success: function(json) {
                        response($.map(json.items, function(item) {
                            return {
                                label: item.title,
                                value: item.title,
                                category: item.ctitle
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {},
            minLength: 2
        });
    });
//-->
</script>

<form method="post" action="<{$arrPageData.current_url|cat:"&task=reorderItems"}>" name="reorderItems">
    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list">
        <tr>
            <td id="headb" align="center" width="20">id</td>
            <td id="headb" align="center" width="90">Тип заказа</td>
            <td id="headb" align="center" width="90">Статус заказа</td>
            <td id="headb" align="left">Клиент</td>
            <td id="headb" align="center" width="100">Телефон</td>
            <td id="headb" align="center" width="95"><{$smarty.const.HEAD_DATE_ADDED}></td>
            
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
        </tr>       
<{section name=i loop=$items}>
         <tr>
             
            <td align="center"><{$items[i].id}></td>
            <td align="center">
<{section name=j loop=$arrPageData.arTypes}>
<{if $arrPageData.arTypes[j].id==$items[i].type}>
                <{$arrPageData.arTypes[j].title}>
<{/if}>
<{/section}>
            </td>
            <td align="center">
<{section name=j loop=$arrPageData.arStatus}>
<{if $arrPageData.arStatus[j].id==$items[i].status}>
                <{$arrPageData.arStatus[j].title}>
<{/if}>
<{/section}>
            </td>
            <td><a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}></a></td>
            <td align="center"><{$items[i].phone}></td>
            <td align="center"><{$items[i].created|date_format:"%d.%m.%y %H:%M"}></td>

            <td align="center" >
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_EDIT}>">
                    <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                </a>
            </td>
            <td align="center">
                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                   <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
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
              <{*  <input name="submit_order" class="buttons" type="submit" value="<{$smarty.const.BUTTON_APPLY}>" />*}>
            </td>
        </tr>
    </table>
</form>
<{/if}>
</div>