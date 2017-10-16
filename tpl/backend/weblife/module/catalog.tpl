<{* +++++++++++++++++ START HEAD ++++++++++++++++++++++ *}>
<{include file='common/module_head.tpl' title=$smarty.const.CATALOGS creat_title=$smarty.const.ADMIN_CREATING_NEW_PRODUCT edit_title=$smarty.const.ADMIN_EDIT_PRODUCT}>

<{include file='common/left_menu.tpl' dependID=0 categoryTree=$categoryTree islist=true}>
<{* +++++++++++++++++ END HEAD ++++++++++++++++++++++ *}>
   
<div id="right_block">
<{* +++++++++++++++++ SHOW ADD OR EDIT ITEM FORM ++++++++++++++++++++++ *}>
<{if $arrPageData.task=='addItem' || $arrPageData.task=='editItem'}>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" onsubmit="return formCheck(this);" enctype="multipart/form-data">
    <input type="hidden" name="createdDate" value="<{$item.createdDate}>" />
    <input type="hidden" name="createdTime" value="<{$item.createdTime}>" />
    <input type="hidden" name="order"   value="<{$item.order}>"   />
<{if empty($categoryTree) && ($arrPageData.cid OR $item.cid)}>
    <input type="hidden" name="cid" value="<{if $item.cid}><{$item.cid}><{else}><{$arrPageData.cid}><{/if}>" />
<{/if}>

    <div class="tabsContainer">
        <ul class="nav">
            <li><a href="javascript:void(0);" data-target="main" class="active">Основные</a></li>
            <li><a href="javascript:void(0);" data-target="info">Инфо (описание)</a></li>
            <li><a href="javascript:void(0);" data-target="attributes">Характеристики</a></li>
            <li><a href="javascript:void(0);" data-target="options">Опции</a></li>
            <li><a href="javascript:void(0);" data-target="relations">Связь с товарами</a></li>
            <li><a href="javascript:void(0);" data-target="seo">SEO</a></li>
            <li><a href="javascript:void(0);" data-target="history">История</a></li>
        </ul>
        <div class="tab_line"></div>
        <ul class="tabs">
            <li class="active" id="tab_main">
                <table border="1" cellspacing="0" cellpadding="1" class="list">
                    <tr>
                        <td id="headb" align="left" width="120"><{$smarty.const.HEAD_TITLE}> <font style="color:red">*</font></td>
                        <td>
                            <input class="left" name="title" size="55" id="title" style="margin-top:5px; margin-right:10px;" type="text" value="<{$item.title}>" /> <input type="button" class="buttons left" value="Изменить SEO путь" onclick="MoveToSeoPath();"/>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
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
<{if !empty($categoryTree)}>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_CATEGORY}></td>
                        <td align="left">
                            <select  name="cid"<{if !empty($item.cid) OR !empty($arrPageData.cid)}> onchange="hideApplyBut(this, this.form.submit_apply, <{if !empty($item.cid)}><{$item.cid}><{else}><{$arrPageData.cid}><{/if}>);"<{/if}>>
<{section name=i loop=$categoryTree}>
                                <option value="<{$categoryTree[i].id}>"<{if $item.cid==$categoryTree[i].id OR (empty($item.cid) && $arrPageData.cid==$categoryTree[i].id)}>  selected<{/if}>><{$categoryTree[i].margin}><{$categoryTree[i].title}> &nbsp; [<{$smarty.const.HEAD_ITEMS}>: <{if isset($arCidCntItems[$categoryTree[i].id])}><{$arCidCntItems[$categoryTree[i].id]}><{else}>0<{/if}>] &nbsp; <{if $categoryTree[i].active==0}>( <{$smarty.const.HEAD_INACTIVE}> ) &nbsp; <{/if}></option>
<{if !empty($categoryTree[i].childrens)}>
                                <!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/tree_childrens.tpl' dependID=$item.cid arrChildrens=$categoryTree[i].childrens}>
                                <!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
<{/if}>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.LABEL_BRAND}></td>
                        <td align="left">
                            <select name="bid" style="min-width: 150px">
                                <option value="0"> -- <{$smarty.const.LABEL_SELECT}> -- </option>
<{section name=i loop=$arrPageData.arBrands}>
                                <option value="<{$arrPageData.arBrands[i].id}>" <{if $item.bid==$arrPageData.arBrands[i].id}>selected<{/if}>><{$arrPageData.arBrands[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PRODUCT_CODE}></td>
                        <td align="left">
                            <input  name="pcode" id="pcode" size="20" type="text" value="<{$item.pcode}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PRODUCT_PRICE}></td>
                        <td align="left">
                            <input  name="price" id="price" size="20" type="text" value="<{$item.price}>" />
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PRODUCT_CPRICE}></td>
                        <td align="left">
                            <input  name="cprice" id="cprice" size="20" type="text" value="<{$item.cprice}>" <{if !empty($item.discount)}>disabled<{/if}>/>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left"><{$smarty.const.HEAD_PRODUCT_DISCOUNT}></td>
                        <td align="left">
                            <label><input type="checkbox" onchange="togglePriceOptions(this);" <{if !empty($item.discount)}>checked<{/if}>/> <{$smarty.const.LABEL_ON}></label>
                            <input  name="discount" id="discount" size="6" type="text" value="<{$item.discount}>" <{if empty($item.discount)}>disabled<{/if}>/>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td id="headb" align="left">Стикеры</td>
                        <td align="left">
                            <{*<{foreach from=$PHPHelper->SELECTIONS item=title key=field}>
                                <input name="<{$field}>" id="<{$field}>" size="20" type="checkbox" value="1" <{if $item.$field}>checked<{/if}>/>
                                <{$title}>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                            <{/foreach}>*}>
                             <label><input name="isnewest" id="isnewest" size="20" type="checkbox" value="1" <{if $item.isnewest}>checked<{/if}>/> новинка </label>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <!-- ++++++++++ Start Attach Files ++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/attach_files.tpl' item=$item attachFile=false attachImages=true}>
                    <!-- ++++++++++ End Attach Files ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                </table>
            </li>
            
            <li id="tab_info">
                <table border="0" cellspacing="0" cellpadding="0" class="list">
                    <tr>
                        <td colspan="2"> 
                            <strong><{$smarty.const.HEAD_SHORT_CONTENT}></strong>
                            <a href="javascript:toggleEditor('description');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                            <textarea style="width:640px; height: 100px;" id="description" name="descr" ><{$item.descr}></textarea>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">  
                            <strong><{$smarty.const.HEAD_CONTENT}></strong>
                            <a href="javascript:toggleEditor('fulldescription');"><{$smarty.const.HEAD_SWITCH_TEXT_EDITOR}></a><br/><br/>
                            <textarea style="width:640px; height: 500px;" id="fulldescription" name="fulldescr" ><{$item.fulldescr}></textarea>
                        </td>
                        <td class="buttons_row"></td>
                    </tr> 
                </table>
            </li>
            
            <li id="tab_attributes">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
                <tr>
                    <td colspan="2"> 
                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="attrTable">
                            <thead>
                                <tr>
                                    <td style="padding-bottom:10px;">
                                        <select id="attrList" onchange="addAttributeRow(this.value);" style="width: 300px;">
                                            <option> -- <{$smarty.const.CATALOG_ATTRIBUTES_SELECT_GROUP}> -- </option>
<{section name=i loop=$arrPageData.attrGroups}>
<{if !in_array($arrPageData.attrGroups[i].id, $item.attrGroups)}>
                                            <option value="<{$arrPageData.attrGroups[i].id}>"><{$arrPageData.attrGroups[i].title}> <{if $arrPageData.attrGroups[i].descr}>(<{$arrPageData.attrGroups[i].descr}>)<{/if}></option>
<{/if}>
<{/section}>
                                        </select>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
<{section name=i loop=$arrPageData.attrGroups}>
<{if in_array($arrPageData.attrGroups[i].id, $item.attrGroups)}>
                                <tr id="attrGroup_<{$arrPageData.attrGroups[i].id}>" data-title="<{$arrPageData.attrGroups[i].title}> <{if $arrPageData.attrGroups[i].descr}>(<{$arrPageData.attrGroups[i].descr}>)<{/if}>" data-gid="<{$arrPageData.attrGroups[i].id}>">
                                    <td>
                                        <strong><{$arrPageData.attrGroups[i].title}> <{if $arrPageData.attrGroups[i].descr}>(<{$arrPageData.attrGroups[i].descr}>)<{/if}></strong>
                                        <a href="javascript:void(0);" data-gid="<{$arrPageData.attrGroups[i].id}>" class="del" onclick="removeAttributeRow(this);"><{$smarty.const.LABEL_DELETE}></a>
                                        <div style="clear: both; margin-bottom: 10px;"></div>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
<{section name=j loop=$arrPageData.attrGroups[i].attributes}>
<{assign var=attValues value=$arrPageData.attrGroups[i].attributes[j]}>
                                            <tr>
                                                <td valign="top" style="min-width:135px;" <{if $attValues.descr}>title="<{$attValues.descr}>"<{/if}>>
                                                    <{$attValues.title}>
                                                </td>
                                                <td style="padding-bottom:10px;">      
<{if !empty($attValues.values)}>
                                                        <div class="attributes">
                                                            <input type="text" placeholder="введите значение для поиска" class="searchAttrValue" data-aid="<{$attValues.id}>" value="" size="80"/>
                                                            <input class="arAttrValues" type="hidden" name="attributes[<{$attValues.id}>]" value=""/>
                                                            <div class="selectedAttr">
                                                            <{section name=l loop=$attValues.values}>
                                                            <{if isset($item.attributes[$attValues.id])}>
                                                                <{if is_array($item.attributes[$attValues.id])}>
                                                                    <{if in_array($attValues.values[l].id, $item.attributes[$attValues.id])}>
                                                                    <div class="attr" data-id="<{$attValues.values[l].id}>" data-value="<{$attValues.values[l].title}>">
                                                                        <{$attValues.values[l].title}>
                                                                        <span onclick="removeAttrValue(this);">X</span>
                                                                    </div>                   
                                                                    <{/if}>
                                                                <{else if $item.attributes[$attValues.id] == $attValues.values[l].title}>
                                                                    <div class="attr" data-id="<{$attValues.values[l].id}>" data-value="<!{$item.attributes[$attValues.id]}>">
                                                                        <{$item.attributes[$attValues.id]}>
                                                                        <span onclick="removeAttrValue(this);">X</span>
                                                                    </div> 
                                                                <{/if}>
                                                            <{/if}>
                                                            <{/section}>  
                                                            </div>
                                                        </div>
<{else}>
                                                        <input size="80" name="attributes[<{$attValues.id}>]" placeholder="<{if $attValues.tid==2}>число<{else}>текст<{/if}>" type="text" value="<{if isset($item.attributes[$attValues.id])}><{$item.attributes[$attValues.id]}><{/if}>" />
<{/if}>
                                                </td>
                                            </tr>       
<{/section}>
                                        </table>
                                    </td>
                                </tr>
<{/if}>
<{/section}>
                            </tbody>
                        </table>
                    </td>
                    <td class="buttons_row" valign="top" width="145" align="center">
                        <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                        <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                        <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                    </td>
                </tr>
                </table>
            </li>
            
            <li id="tab_options">
                <br/>
                <br/>&nbsp;&nbsp;&nbsp;
                <select id="optionsList" onchange="ProductOptions.addGroup(this.value);" style="width: 400px;">
                    <option value=""> -- Выберите опцию из списка -- </option>
<{foreach name=i from=$arrPageData.options key=optID item=option}>
                    <option value="<{$optID}>" <{if array_key_exists($optID, $item.options)}>disabled<{/if}>><{$option.title}></option>
<{/foreach}>
                </select>
                <div id="optionsHolder">
<{foreach name=i from=$item.options key=optID item=option}>
                    <table width="100%" class="list order" cellspacing="1" id="optgroup_<{$optID}>">
                        <thead>
                            <tr>
                                <td id="headb" colspan="3" align="left">
                                    <input type="hidden" name="options[<{$optID}>][id]" value="<{$optID}>"/>
                                    <a href="javascript:;" onclick="ProductOptions.deleteGroup(<{$optID}>);">
                                        <img src="/images/operation/delete.png" alt="удалить" title="удалить">
                                    </a> 
                                    <strong><{$option.title}></strong>
                                </td>
                                <td id="headb" colspan="2" align="right">
                                    <input type="checkbox" name="options[<{$optID}>][required]" value="1" <{if $option.required}>checked<{/if}>/> обязательно
                                </td>
                            </tr>
                            <tr>
                                <td id="headb">значение</td>
                                <td id="headb" align="center" width="120">оператор цены</td>
                                <td id="headb" align="center" width="100">цена</td>
                                <td id="headb" align="center" width="90">главное</td>
                                <td id="headb" align="center" width="90">удалить</td>
                            </tr>
                        </thead>
                        <tbody>
<{foreach name=j from=$option.values key=valID item=value}>
                            <tr id="option_<{$optID}>_<{$smarty.foreach.j.iteration}>">
                                <td align="left">
                                    <select name="options[<{$optID}>][values][<{$smarty.foreach.j.iteration}>][id]" onchange="ProductOptions.changeOptionValue(<{$optID}>, <{$smarty.foreach.j.iteration}>, this.value);" style="width: 100%;">
<{foreach name=z from=$arrPageData.options[$optID].values key=oid item=val}>
                                        <option value="<{$oid}>" <{if $oid==$valID}>selected<{elseif $oid!=$valID AND array_key_exists($oid, $option.values)}>disabled<{/if}>><{$val.title}></option>
<{/foreach}>
                                    </select>
                                </td>
                                <td align="center">
                                    <select name="options[<{$optID}>][values][<{$smarty.foreach.j.iteration}>][operator]">
                                        <option value="+" <{if $value.operator=="+"}>selected<{/if}>> + </option>
                                        <option value="-" <{if $value.operator=="-"}>selected<{/if}>> - </option>
                                    </select>
                                </td>
                                <td align="center">
                                    <input type="text" name="options[<{$optID}>][values][<{$smarty.foreach.j.iteration}>][price]" value="<{$value.price}>" size="8"/>
                                </td>
                                <td align="center">
                                    <input type="radio" name="options[<{$optID}>][values][<{$smarty.foreach.j.iteration}>][primary]" value="1" <{if $value.primary==1}>checked<{/if}> onchange="ProductOptions.setPrimaryValue(<{$optID}>, <{$smarty.foreach.j.iteration}>);"/>
                                </td>
                                <td align="center">
                                    <a href="javascript:;" onclick="ProductOptions.deleteValueRow(<{$optID}>, <{$smarty.foreach.j.iteration}>);">
                                        <img src="/images/operation/delete.png" alt="удалить" title="удалить">
                                    </a>
                                </td>
                            </tr>
<{/foreach}>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" align="right">
                                    <button type="button" onclick="ProductOptions.addValueRow(<{$optID}>);" class="buttons">Добавить значение</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
<{/foreach}>
                </div>
            </li>
            <script type="text/javascript">
                var ProductOptions = {
                    div: document.getElementById("optionsHolder"),
                    list: document.getElementById("optionsList"),
                    arOptions: <{$HTMLHelper->dataConv($arrPageData.options)|json_encode}>,
                    html: "",
                    addGroup : function (optionID) {
                        var _self = this;
                        var exIDX = new Array(),
                            arOptions = {};
                        $.each($(_self.div).find("table"), function(i, table) {
                            var id = $(table).attr("id");
                            var int = id.substring(strpos(id, "_"));
                            exIDX.push(parseInt(int));
                        });
                        $.each($(_self.list).find("option"), function(i, option) {
                            option.selected = false;
                            var val = $(option).val();
                            if (val == optionID) {
                                $(option).attr("disabled", "disabled");
                                option.disabled = true;
                            }
                        });
<{foreach name=z from=$arrPageData.options key=optID item=option}>
                        arOptions[<{$optID}>] = {
                            id    : <{$optID}>,
                            title : "<{$option.title}>"
                        }
<{/foreach}>
                        for (var id in arOptions) {
                            if (id == optionID) {
                                _self.html += "<table width=\"100%\" class=\"list order\" cellspacing=\"1\" id=\"optgroup_" + optionID + "\">";
                                _self.html += "<thead>";
                                _self.html += "<tr>";
                                _self.html += "<td id=\"headb\" colspan=\"3\" align=\"left\">";
                                _self.html += "<input type=\"hidden\" name=\"options[" + optionID + "][id]\" value=\"" + optionID + "\">";
                                _self.html += "<a href=\"javascript:;\" onclick=\"ProductOptions.deleteGroup(" + optionID + ");\">";
                                _self.html += "<img src=\"/images/operation/delete.png\" alt=\"удалить\" title=\"удалить\"/>";
                                _self.html += "</a>";
                                _self.html += "<strong> " + arOptions[id].title + "</strong>";
                                _self.html += "</td>";
                                _self.html += "<td id=\"headb\" colspan=\"2\" align=\"right\">";
                                _self.html += "<input type=\"checkbox\" name=\"options[" + optionID + "][required]\" value=\"1\"/> обязательно";
                                _self.html += "</td>";
                                _self.html += "</tr>";
                                _self.html += "<tr>";
                                _self.html += "<tr>";
                                _self.html += "<td id=\"headb\">значение</td>";
                                _self.html += "<td id=\"headb\" align=\"center\" width=\"120\">оператор цены</td>";
                                _self.html += "<td id=\"headb\" align=\"center\" width=\"100\">цена</td>";
                                _self.html += "<td id=\"headb\" align=\"center\" width=\"90\">главное</td>";
                                _self.html += "<td id=\"headb\" align=\"center\" width=\"90\">удалить</td>";
                                _self.html += "</tr>";
                                _self.html += "</thead>";
                                _self.html += "<tbody>";
                                _self.html += "</tbody>";
                                _self.html += "<tfoot>";
                                _self.html += "<tr>";
                                _self.html += "<td colspan=\"5\" align=\"right\">";
                                _self.html += "<button type=\"button\" onclick=\"ProductOptions.addValueRow(" + optionID + ");\" class=\"buttons\">Добавить значение</button>";
                                _self.html += "</td>";
                                _self.html += "</tr>";
                                _self.html += "</tfoot>";
                            }
                        }
                        $(_self.div).append(_self.html);
                        _self.html = "";
                    },
                    deleteGroup: function (optionID) {
                        var _self = this;
                        $(document.getElementById("optgroup_" + optionID)).remove();
                        $.each($(_self.list).find("option"), function (i, option) {
                            var val = $(option).val();
                            if (val == optionID) {
                                $(option).removeAttr("disabled");
                                option.disabled = false;
                            }
                        });
                    },
                    addValueRow: function (optionID) {
                        var _self = this,
                            arOptions = _self.arOptions, // option array
                            arOption = new Object(),  // option array
                            arValues = new Object(),  // values array
                            arValue  = new Object(),  // value array
                            arExValues = new Array(), // array of existing option values
                            table = document.getElementById("optgroup_" + optionID), // parent table
                            index = 0; // count of added option values
                        $.each($(table).children("tbody").find("tr").children("td:first-of-type").children("select"), function (i, select) {
                            arExValues.push($(select).val());
                            index++;
                        });
                        if (!empty(arOptions)) {
                            for (var id in arOptions) {
                                if (id == optionID) {
                                    arOption = arOptions[id];
                                }
                            }
                            if (!empty(arOption)) {
                                arValues = arOption.values;
                                for (var id in arOption.values) {
                                    if (!in_array(id, arExValues)) {
                                        arValue = arOption.values[id];
                                        break;
                                    }
                                }
                                var cnt = $(table).children("tbody").children("tr").size();
                                if (!empty(arValue) && count(arValue) > 0) {
                                    index++;
                                    _self.html += "<tr id=\"option_" + optionID + "_" + index + "\">";
                                    _self.html += "<td align=\"left\">";
                                    _self.html += "<select name=\"options[" + optionID + "][values][" + index + "][id]\" onchange=\"ProductOptions.changeOptionValue(" + optionID + "," + index + ", this.value);\" style=\"width: 100%;\">";
                                    for (var id in arValues) {
                                        _self.html += "<option value=\"" + id + "\" " + (in_array(id, arExValues) ? "disabled" : "") + ">" + arValues[id].title + "</option>";
                                    }
                                    _self.html += "</select>";
                                    _self.html += "</td>";
                                    _self.html += "<td align=\"center\">";
                                    _self.html += "<select name=\"options[" + optionID + "][values][" + index + "][operator]\">";
                                    _self.html += "<option value=\"+\"> + </option>";
                                    _self.html += "<option value=\"-\"> - </option>";
                                    _self.html += "</select>";
                                    _self.html += "</td>";
                                    _self.html += "<td align=\"center\">";
                                    _self.html += "<input type=\"text\" name=\"options[" + optionID + "][values][" + index + "][price]\" value=\"0\" size=\"8\">";
                                    _self.html += "</td>";
                                    _self.html += "<td align=\"center\">";
                                    _self.html += "<input type=\"radio\" name=\"options[" + optionID + "][values][" + index + "][primary]\" value=\"1\"" + ((cnt==0) ? " checked" : "") + " onchange=\"ProductOptions.setPrimaryValue(" + optionID + "," + index + ");\"/>";
                                    _self.html += "</td>";
                                    _self.html += "<td align=\"center\">";
                                    _self.html += "<a href=\"javascript:;\" onclick=\"ProductOptions.deleteValueRow(" + optionID + ", " + index + ");\">";
                                    _self.html += "<img src=\"/images/operation/delete.png\" alt=\"удалить\" title=\"удалить\"/>";
                                    _self.html += "</a>";
                                    _self.html += "</td>";
                                    _self.html += "</tr>";
                                    // append html
                                    $(table).children("tbody").append(_self.html);
                                    _self.html = "";
                                    // Find last added option value
                                    var lastVal = $(table).children("tbody").children("tr:last-of-type").children("td:first-of-type").children("select").val();
                                    $.each($(table).children("tbody").find("tr").children("td:first-of-type").children("select"), function (i, select) {
                                        if ($(select).val() != lastVal) {
                                            $.each($(select).find("option"), function (j, opt) {
                                                if ($(opt).val() == lastVal) {
                                                    $(opt).attr("disabled", "disabled");
                                                    opt.disabled = true;
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    },
                    deleteValueRow: function (optionID, index) {
                        var _self   = this,
                            table   = document.getElementById("optgroup_" + optionID),
                            row     = document.getElementById("option_" + optionID + "_" + index),
                            select  = $(row).children("td:first-of-type").children("select"),
                            lastVal = $(select).val();
                        $(row).remove();
                        $.each($(table).children("tbody").children("tr").children("td:first-of-type").children("select").children("option"), function (i, opt) {
                            if ($(opt).val()==lastVal && opt.disabled) {
                                $(opt).removeAttr("disabled");
                                opt.disabled = false;
                            }
                        });
                        var checked = $(table).children("tbody").find("input[type=\"radio\"]:checked"),
                            cnt = 0;
                        if ($(checked).size()==0) {
                            $.each($(table).children("tbody").find("input[type=\"radio\"]"), function (i, input) {
                                if (cnt > 0) return;
                                $(input).attr("checked", "checked");
                                input.checked = true;
                                cnt++;
                            });
                        }
                    },
                    changeOptionValue: function (optionID, index, val) {
                        var _self = this,
                            table = document.getElementById("optgroup_" + optionID),
                            row   = document.getElementById("option_" + optionID + "_" + index),
                            select  = $(row).children("td:first-of-type").children("select"),
                            arVal = new Array();
                        arVal.push(val);
                        $.each($(table).children("tbody").children("tr").children("td:first-of-type").children("select"), function (i, sel) {
                            if (val == $(sel).val()) return;
                            arVal.push($(sel).val());
                        });
                        $.each($(table).children("tbody").children("tr").children("td:first-of-type").children("select"), function (i, sel) {
                            var sVal = $(sel).val();
                            if (val == sVal) return; // skip current select
                            $.each($(sel).find("option"), function (i, opt) {
                                var iVal = $(opt).val(),
                                    disabled = opt.disabled;
                                if (iVal == sVal) return; // skip selected options
                                if (in_array(iVal, arVal)) {
                                    $(opt).attr("disabled", "disabled");
                                    opt.disabled = true;
                                } else {
                                    $(opt).removeAttr("disabled");
                                    opt.disabled = false;
                                }
                            });
                        });
                    },
                    setPrimaryValue: function (optionID, index) {
                        var _self = this,
                            table = document.getElementById("optgroup_" + optionID),
                            row   = document.getElementById("option_" + optionID + "_" + index);
                        $.each($(table).find("input[type=\"radio\"]:checked"), function (i, input) {
                            $(input).removeAttr("checked");
                            input.checked = false;
                        });
                        $.each($(row).find("input[type=\"radio\"]"), function (i, input) {
                            $(input).attr("checked", "checked");
                            input.checked = true;
                        });
                    }
                };
            </script>
            
            <li id="tab_relations">
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="relatedTable">
                    <tr>
                        <td colspan="4">     
                            <strong>Похожие товары</strong><br/><br/>
                            <select id="relatedCats" onchange="updateRelatedOptions(this.value);" >
                                <option> --- Выберите категорию из списка --- </option>
<{foreach name=i from=$arrPageData.arRelatedCats key=arKey item=arItem}>
                                <option value="<{$arItem.id}>"><{$arItem.title}></option>
<{/foreach}>
                            </select>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                            <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
                            <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                        </td>
                    </tr>
                    <tr valign="top">
                        <td >
                            <select onChange="this.form.related_add.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_all_related" name="all_related" class="jsSelectUtils_select"></select>
                        </td>
                        <td  valign="middle">
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.all_related, 'list_settings_selected_related', 3);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &gt; &nbsp;" name="related_add" class="buttons green" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.selected_related, 'list_settings_all_related');jsSelectUtils.addExtraOptionsParams(this.form.all_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &lt; &nbsp;" name="related_del" class="buttons green" style="min-width: 30px;"/>
                        </td>
                        <td >
                            <input type="hidden" name="related" value="" />
                            <select onChange="var frm=this.form; frm.related_up.disabled=frm.related_down.disabled=frm.related_del.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.all_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_selected_related" name="selected_related" class="jsSelectUtils_select">
<{section name=i loop=$item.related}>
                                <option value="<{$item.related[i].id}>" ondblclick="openTab('/admin.php?module=catalog&task=editItem&itemID=' + this.value);" style="color: blue; text-decoration: underline;"><{$item.related[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td  valign="middle">
                            <input type="button" onClick="jsSelectUtils.moveOptionsUp(this.form.selected_related);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок (выше)" value="Выше" class="buttons" name="related_up" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.moveOptionsDown(this.form.selected_related);jsSelectUtils.addExtraOptionsParams(this.form.selected_related, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок: ниже" value="Ниже" class="buttons" name="related_down" style="min-width: 30px;"/>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding: 0 5px;font-style:italic;">Двойной клик по элементу для перехода на товар</td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
                <br/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="similarTable">
                    <tr>
                        <td colspan="4">     
                            <strong>Также покупают</strong><br/><br/>
                            <select id="similarCats" onchange="updateSimilarOptions(this.value);" class="field">
                                <option> --- Выберите категорию из списка --- </option>
<{foreach name=i from=$arrPageData.arRelatedCats key=arKey item=arItem}>
                                <option value="<{$arItem.id}>"><{$arItem.title}></option>
<{/foreach}>
                            </select>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                        </td>
                    </tr>
                    <tr valign="top">
                        <td>
                            <select onChange="this.form.similar_add.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.selected_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_all_similar" name="all_similar" class="jsSelectUtils_select"></select>
                        </td>
                        <td valign="middle">
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.all_similar, 'list_settings_selected_similar', 3);jsSelectUtils.addExtraOptionsParams(this.form.selected_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &gt; &nbsp;" name="similar_add" class="buttons green" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.selected_similar, 'list_settings_all_similar');jsSelectUtils.addExtraOptionsParams(this.form.all_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &lt; &nbsp;" name="similar_del" class="buttons green" style="min-width: 30px;"/>
                        </td>
                        <td >
                            <input type="hidden" name="similar" value="" />
                            <select onChange="var frm=this.form; frm.similar_up.disabled=frm.similar_down.disabled=frm.similar_del.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.all_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_selected_similar" name="selected_similar" class="jsSelectUtils_select">
<{section name=i loop=$item.similar}>
                                <option value="<{$item.similar[i].id}>" ondblclick="openTab('/admin.php?module=catalog&task=editItem&itemID=' + this.value);" style="color: blue; text-decoration: underline;"><{$item.similar[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td  valign="middle">
                            <input type="button" onClick="jsSelectUtils.moveOptionsUp(this.form.selected_similar);jsSelectUtils.addExtraOptionsParams(this.form.selected_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок (выше)" value="Выше" class="buttons" name="similar_up" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.moveOptionsDown(this.form.selected_similar);jsSelectUtils.addExtraOptionsParams(this.form.selected_similar, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок: ниже" value="Ниже" class="buttons" name="similar_down" style="min-width: 30px;"/>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding: 0 5px;font-style:italic;">Двойной клик по элементу для перехода на товар</td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
                <br/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="additionsTable">
                    <tr>
                        <td colspan="4">     
                            <strong>Дополнения</strong><br/><br/>
                            <select id="additionsCats" onchange="updateAdditionsOptions(this.value);" class="field">
                                <option> --- Выберите категорию из списка --- </option>
<{foreach name=i from=$arrPageData.arRelatedCats key=arKey item=arItem}>
                                <option value="<{$arItem.id}>"><{$arItem.title}></option>
<{/foreach}>
                            </select>
                        </td>
                        <td class="buttons_row" valign="top" width="145" align="center">
                        </td>
                    </tr>
                    <tr valign="top">
                        <td>
                            <select onChange="this.form.additions_add.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.selected_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_all_additions" name="all_additions" class="jsSelectUtils_select"></select>
                        </td>
                        <td valign="middle">
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.all_additions, 'list_settings_selected_additions', 3);jsSelectUtils.addExtraOptionsParams(this.form.selected_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &gt; &nbsp;" name="additions_add" class="buttons green" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.selected_additions, 'list_settings_all_additions');jsSelectUtils.addExtraOptionsParams(this.form.all_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &lt; &nbsp;" name="additions_del" class="buttons green" style="min-width: 30px;"/>
                        </td>
                        <td >
                            <input type="hidden" name="additions" value="" />
                            <select onChange="var frm=this.form; frm.additions_up.disabled=frm.additions_down.disabled=frm.additions_del.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.all_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_selected_additions" name="selected_additions" class="jsSelectUtils_select">
<{section name=i loop=$item.additions}>
                                <option value="<{$item.additions[i].id}>" ondblclick="openTab('/admin.php?module=catalog&task=editItem&itemID=' + this.value);" style="color: blue; text-decoration: underline;"><{$item.additions[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td  valign="middle">
                            <input type="button" onClick="jsSelectUtils.moveOptionsUp(this.form.selected_additions);jsSelectUtils.addExtraOptionsParams(this.form.selected_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок (выше)" value="Выше" class="buttons" name="additions_up" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.moveOptionsDown(this.form.selected_additions);jsSelectUtils.addExtraOptionsParams(this.form.selected_additions, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок: ниже" value="Ниже" class="buttons" name="additions_down" style="min-width: 30px;"/>
                        </td>
                        <td class="buttons_row"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding: 0 5px;font-style:italic;">Двойной клик по элементу для перехода на товар</td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
                <br/>
                <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="kitsTable">
                    <tr>
                        <td colspan="4">
                            <strong><{$smarty.const.PRODUCT_KITS}></strong><br/><br/>
                            <select id="kitCats" onchange="updateKitOptions(this.value);">
                                <option> --- Выберите категорию из списка --- </option>
<{foreach name=i from=$arrPageData.arKitCats key=arKey item=arItem}>
                                <option value="<{$arItem.id}>"><{$arItem.title}></option>
<{/foreach}>
                            </select>
                        </td>
                        <td class="buttons_row" width="145"></td>
                    </tr>
                    <tr valign="top">
                        <td align="center">
                            <select onChange="this.form.kit_add.disabled=(this.selectedIndex == -1); jsSelectUtils.addExtraOptionsParams(this.form.selected_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" size="10" id="list_settings_all_kits" name="all_kits" class="jsSelectUtils_select"></select>
                        </td>
                        <td valign="middle">
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.all_kits, 'list_settings_selected_kits', 10, true, false);jsSelectUtils.addExtraOptionsParams(this.form.selected_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &gt; &nbsp;" name="kit_add" class="buttons green" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.addSelectedOptions(this.form.selected_kits, 'list_settings_all_kits', 0, false, false);jsSelectUtils.addExtraOptionsParams(this.form.all_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Выбрать отмеченные колонки" value="&nbsp; &lt; &nbsp;" name="kit_del" class="buttons green" style="min-width: 30px;"/>
                        </td>
                        <td align="center">
                            <input type="hidden" name="arKits" value="" />
                            <select onChange="var frm=this.form; frm.kit_up.disabled=frm.kit_down.disabled=frm.kit_del.disabled=(this.selectedIndex == -1);jsSelectUtils.addExtraOptionsParams(this.form.all_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" multiple="" size="10" id="list_settings_selected_kits" name="selected_kits" class="jsSelectUtils_select">
<{section name=i loop=$item.arKits}>
                                <option value="<{$item.arKits[i].id}>" ondblclick="openTab('/admin.php?module=catalog&task=editItem&itemID=' + this.value);" style="color: blue; text-decoration: underline;"><{$item.arKits[i].title}></option>
<{/section}>
                            </select>
                        </td>
                        <td valign="middle">
                            <input type="button" onClick="jsSelectUtils.moveOptionsUp(this.form.selected_kits);jsSelectUtils.addExtraOptionsParams(this.form.selected_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок (выше)" value="Выше" class="buttons green" name="kit_up" style="min-width: 30px;"/>
                            <br/>
                            <input type="button" onClick="jsSelectUtils.moveOptionsDown(this.form.selected_kits);jsSelectUtils.addExtraOptionsParams(this.form.selected_kits, {ondblclick: 'openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);', style: 'color: blue; text-decoration: underline;'});" disabled="" title="Порядок показа колонок: ниже" value="Ниже" class="buttons green" name="kit_down" style="min-width: 30px;"/>
                        </td>
                        <td class="buttons_row" width="145"></td>
                    </tr>
                    <tr>
                        <td colspan="4" style="padding: 0 5px;font-style:italic;">Двойной клик по элементу для перехода на товар</td>
                        <td class="buttons_row"></td>
                    </tr>
                </table>
            </li>
            
            <li id="tab_seo">
                <table width="101%" border="0" cellspacing="0" cellpadding="0" class="list" >  
                    <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
                    <{include file='common/meta_seo_data.tpl'}>
                    <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->  
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
    $(function() {
        $.each($('.attributes'), function() {
            initAttr($(this));           
        });
    });
    
    function initAttr(input) {
        var searchStr = $(input).find('.searchAttrValue');
        var attrParent = $(input).find('.selectedAttr');

        $(searchStr).autocomplete({
            source: function(request, response) {
                var vidx = [];
                $.each($(attrParent).find('.attr'), function(i, attr) {
                    vidx.push($(attr).attr('data-id'));
                });
                $.ajax({
                    url: '/interactive/ajax.php',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        zone: 'admin',
                        action: 'getAttributeValue',
                        aid: $(searchStr).attr('data-aid'),
                        vidx: vidx.join(','),
                        searchStr: request.term
                    }, 
                    success: function(json) {
                        response($.map(json.items, function(item) {
                            return {
                                id: item.id,
                                label: item.title,
                                value: item.title
                            }
                        }));
                    }
                });
            },
            select: function(event, ui) {
                $(attrParent).append(' <div class="attr" data-id="'+ui.item.id+'" data-value="'+ui.item.value+'">'+ui.item.value+' <span onclick="removeAttrValue(this);">X</span></div> ');
                $(this).val("");
                return false;
            },
            minLength: 2
        });   
    }
    function removeAttrValue(attr){
        $(attr).parent().remove();
    }
    
    function formCheck(form) {
        if(form.title.value.length == 0){
           alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); 
           return false;
        }
                
        $.each($('.attributes'), function() {
            var attrParent = $(this).find('.selectedAttr');
            var attrValues = $(this).find('.arAttrValues');
            
            var vidx = [];
            $.each($(attrParent).find('.attr'), function() {
                vidx.push($(this).attr('data-value'));
            });
            $(attrValues).val(vidx.join('@'));
        });
        
        // product kits input filling
        var arIdx = new Array();
        for (var i = 0; i < form.selected_kits.length; i++) {
            arIdx.push(form.selected_kits[i].value);
        }
        form.arKits.value = arIdx.join(',');
        
        // related products input filling
        var arIdx = new Array();
        for (var i = 0; i < form.selected_related.length; i++) {
            arIdx.push(form.selected_related[i].value);
        }
        form.related.value = arIdx.join(',');
        
        // similar products input filling
        var arIdx = new Array();
        for (var i = 0; i < form.selected_similar.length; i++) {
            arIdx.push(form.selected_similar[i].value);
        }
        form.similar.value = arIdx.join(',');
        
        // additions products input filling
        var arIdx = new Array();
        for (var i = 0; i < form.selected_additions.length; i++) {
            arIdx.push(form.selected_additions[i].value);
        }
        form.additions.value = arIdx.join(',');
        
        return true;
    }
    
    function togglePriceOptions(cb) {
        var checked = cb.checked || false;
        var cprice = document.getElementById('cprice');
        var discount = document.getElementById('discount');

        if(checked) {
            $(cprice).attr('disabled', true);
            $(discount).removeAttr('disabled'); 
        } else {
            $(discount).attr('disabled', true);
            $(cprice).removeAttr('disabled'); 
        }
    }

    function getExItems(objID, itemID){
        var exItems = new Array();
        $.each($('#'+objID).children('option'), function(i, el) {
            exItems.push($(el).val());
        });
        if(itemID > 0){
            exItems.push(itemID);
        }
        return exItems;
    }

    function updateKitOptions(CID){
        CID = parseInt(CID)||false;
        var exItems = getExItems('list_settings_selected_kits', '<{$item.id}>');
        if(CID){
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'getKitItems',
                    exclude: exItems,
                    cid: CID
                }, 
                success: function(json){
                    if(json.items){
                        var html = '';
                        for (var i in json.items){
                            html += '<option value="' + i + '" ondblclick="openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);" style="color: blue; text-decoration: underline;">' + json.items[i] + '</option>';
                        }
                        $('#list_settings_all_kits').html(html);
                    }
                }
            });
        } else {
            $('#list_settings_all_kits').html('');
        }
    }

    function updateRelatedOptions(CID){
        CID = parseInt(CID)||false;
        var exItems = getExItems('list_settings_selected_related', '<{$item.id}>');
        if(CID){
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'getRelatedItems',
                    module: '<{$arrPageData.module}>',
                    exclude: exItems,
                    cid: CID
                }, 
                success: function(json){
                    if(json.items){
                        var html = '';
                        for (var i in json.items){
                            html += '<option value="' + json.items[i].id + '" ondblclick="openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);" style="color: blue; text-decoration: underline;">' + json.items[i].title + '</option>';
                        }
                        $('#list_settings_all_related').html(html);
                    }
                }
            });
        }
    }

    function updateSimilarOptions(CID){
        CID = parseInt(CID)||false;
        var exItems = new Array();
        $.each($('#list_settings_selected_similar').children('option'), function(i, el) {
            exItems.push($(el).val());
        });
<{if $item.id}>
        exItems.push(<{$item.id}>);
<{/if}>
        if(CID){
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'getRelatedItems',
                    module: '<{$arrPageData.module}>',
                    exclude: exItems,
                    cid: CID
                }, 
                success: function(json){
                    if(json.items){
                        var html = '';
                        for (var i in json.items){
                            html += '<option value="' + json.items[i].id + '" ondblclick="openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);" style="color: blue; text-decoration: underline;">' + json.items[i].title + '</option>';
                        }
                        $('#list_settings_all_similar').html(html);
                    }
                }
            });
        }
    }

    function updateAdditionsOptions(CID){
        CID = parseInt(CID)||false;
        var exItems = new Array();
        $.each($('#list_settings_selected_additions').children('option'), function(i, el) {
            exItems.push($(el).val());
        });
<{if $item.id}>
        exItems.push(<{$item.id}>);
<{/if}>
        if(CID){
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'getRelatedItems',
                    module: '<{$arrPageData.module}>',
                    exclude: exItems,
                    cid: CID
                }, 
                success: function(json){
                    if(json.items){
                        var html = '';
                        for (var i in json.items){
                            html += '<option value="' + json.items[i].id + '" ondblclick="openTab(\'/admin.php?module=catalog&task=editItem&itemID=\' + this.value);" style="color: blue; text-decoration: underline;">' + json.items[i].title + '</option>';
                        }
                        $('#list_settings_all_additions').html(html);
                    }
                }
            });
        }
    }
    // adding attribute group to product form
    function addAttributeRow(gid) {
        gid = parseInt(gid) || 0;
        var table = document.getElementById('attrTable');
        // групы атрибутов, которые уже добавлены к товару
        var ArGroups = [];
        if($(table).children('tbody').has('tr')) {
            $.each($(table).children('tbody').children('tr'), function(i, tr){
                ArGroups.push($(tr).data('gid'));
            });
        }
        if(gid > 0) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'admin',
                    action: 'addAttributeRow',
                    groupID: gid,
                    itemID: parseInt(<{$item['id']}>),
                    arGroups: ArGroups
                }, 
                success: function(json) {
                    // form layout DOM insertion
                    if(json.tpl) {
                        $(table).children('tbody').append(json.tpl);
                    }
                    // updating select box
                    if(json.select) {
                        updateSelectBox(json.select);
                    }
                    $.each($('.attributes'), function() {
                        initAttr($(this));           
                    });
                }
            });
        }
    }
    
    function removeAttributeRow(a) {
        gid = parseInt($(a).data('gid')) || 0;
        var table = document.getElementById('attrTable');
        var target = $(table).children('tbody').children('tr#attrGroup_' + gid);
        var select = $(table).find('#attrList');
        var arData = {};
        $.each($(select).children('option'), function(i, opt) {
            var val = parseInt($(opt).val()) || 0;
            var title = $(opt).text();
            if(val > 0) {
                arData[val] = title;
            }
        });
        arData[gid] = $(target).data('title');
        $(target).remove();
        updateSelectBox(arData);
    }
    
    // updating select box
    function updateSelectBox(arData) {
        arData = arData || {};
        var table = document.getElementById('attrTable');
        var select = $(table).find('#attrList');
        var html = '<option> -- Выберите группу атрибутов -- </option>';
        if(!empty(arData)) {
            for (var id in arData) {
                html += '<option value="' + id + '">' + arData[id] + '</option>';
            }
        }
        $(select).html(html);
    }
//-->
</script>

<{* +++++++++++++++++++++++++ SHOW ALL ITEMS ++++++++++++++++++++++++++ *}>
<{else}> 
<{include file='common/order_links.tpl' arrOrderLinks=$arrPageData.arrOrderLinks}>
<div class="clear"></div>
<{include file='common/new_page_btn.tpl' title=$smarty.const.ADMIN_ADD_NEW_PRODUCT shortcut=true}>
<div class="search_block">
    <form method="GET" id="searchForm" action="">
        <input type="hidden" name="module" value="<{$arrPageData.module}>" />
        <input type="hidden" name="cid" value="<{$arrPageData.cid}>" />
        <input size="89" type="text" placeholder="поиск по артикулу или названию товара" id="categorySearch" name="filters[title]" value="<{if isset($arrPageData.filters.title)}><{$arrPageData.filters.title}><{/if}>" />
        <button type="submit" class="buttons right" style="margin-top:0; margin-right:3px;"><{$smarty.const.SITE_FOUND}></button>
    </form>
</div>
<div class="clear"></div>
<script type="text/javascript">
<!--   
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
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list" id="operationTbl">
            <tr>
                <td id="headb" align="center" width="12">
                   <{* <input id="checkAll" type="checkbox" value="0" onchange="SelectCheckBox('operationTbl', 'checkAll', true)"/>
             *}>   </td>
                <td id="headb" align="center" width="38"></td>
                <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
<{if !$arrPageData.cid}>
                <td id="headb" align="center" width="120"><{$smarty.const.HEAD_CATEGORY}></td>
<{/if}>
                <td id="headb" class="hidden" align="center" width="95"><{$smarty.const.HEAD_DATE_ADDED}></td>
<{foreach from=$PHPHelper->SELECTIONS item=title}>
                <td id="headb" class="hidden" align="center" width="15"><{$title}></td>
<{/foreach}>
                <td id="headb" align="center" width="62"><{$smarty.const.HEAD_PRICE}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_EDIT}></td>
                <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
<{section name=i loop=$items}>
             <tr>
                <td align="center"><input type="checkbox" class="checkboxes" name="<{if $items[i].isshortcut}>arShortcuts[<{$items[i].shortcutID}>]<{else}>arItems[<{$items[i].id}>]<{/if}>" onchange="SelectCheckBox(this);" value="1" /></td>
                <td align="center">
<{if $items[i].shortcutActive}>
                    <a href="<{if $items[i].isshortcut}>/admin.php?module=shortcuts&task=publishItem&status=0&itemID=<{$items[i].shortcutID}><{else}><{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}><{/if}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>">
                        <img src="<{$arrPageData.system_images}>check.png" alt="<{$smarty.const.HEAD_NO_PUBLISH}>" title="<{$smarty.const.HEAD_NO_PUBLISH}>" />
                    </a>
<{else}>
                    <a href="<{if $items[i].isshortcut}>/admin.php?module=shortcuts&task=publishItem&status=1&itemID=<{$items[i].shortcutID}><{else}><{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}><{/if}>" title="<{$smarty.const.HEAD_PUBLISH}>">
                        <img src="<{$arrPageData.system_images}>un_check.png" alt="<{$smarty.const.HEAD_PUBLISH}>" title="<{$smarty.const.HEAD_PUBLISH}>" />
                    </a>
<{/if}>
                </td>
                <td>
                    <{if $items[i].isshortcut}>
                        <a style="position:relative; z-index:10" href="/admin.php?module=shortcuts&task=editItem&itemID=<{$items[i].shortcutID}>">
                            <{$items[i].title}> 
                        </a>
                        <a target="_blank" style="position:relative" href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>">
                            <img style="position: absolute; right: -15px; top: -30px;" src="<{$arrPageData.system_images}>shortcut.png" />
                        </a>
                    <{else}>
                        <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}>"><{$items[i].title}><{if $items[i].pcode}>, (<{$items[i].pcode}>)<{/if}></a>
                    <{/if}>
                </td>
<{if !$arrPageData.cid}>
                <td align="left"><{$items[i].cat_title}></td>
<{/if}>
       <{*         <td class="hidden" align="center"><{$items[i].created|date_format:"%d.%m.%y %H:%M:%S"}></td>
<{foreach from=$PHPHelper->SELECTIONS item=title key=field}>
                    <td class="hidden" align="center"><input type="checkbox" name="<{$field}>[]" id="<{$field}>_<{$items[i].id}>" value="<{$items[i].id}>" <{if $items[i].$field}> checked<{/if}> /></td>
<{/foreach}>*}>
                <td align="center"> 
                    <{if $items[i].isshortcut}>
                        <{$items[i].price}>
                    <{else}>
                        <input type="text" size="7" value="<{$items[i].price}>" name="arPrices[<{$items[i].id}>]"/>
                    <{/if}>
                </td>
                <td align="center"><input type="text" name="<{if $items[i].isshortcut}>arShortcutsOrder[<{$items[i].shortcutID}>]<{else}>arOrder[<{$items[i].id}>]<{/if}>" id="arOrder_<{$items[i].id}>" class="order" value="<{if $items[i].isshortcut}><{$items[i].shortcutOrder}><{else}><{$items[i].order}><{/if}>" maxlength="4" /></td>
                <td align="center" >
                    <a href="<{if $items[i].isshortcut}>/admin.php?module=shortcuts&task=editItem&itemID=<{$items[i].shortcutID}><{else}><{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItem&itemID="|cat:$items[i].id}><{/if}>" title="<{$smarty.const.LABEL_EDIT}>">
                        <img src="<{$arrPageData.system_images}>edit.png" alt="<{$smarty.const.LABEL_EDIT}>" />
                    </a>
                </td>
              <{*  <td align="center" >
                    <a href="<{$arrPageData.current_url|cat:"&task=addItem&copyID="|cat:$items[i].id}>" title="<{$smarty.const.LABEL_COPY}>">
                        <img src="<{$arrPageData.system_images}>copy_small.png" alt="<{$smarty.const.LABEL_COPY}>" />
                    </a>
                </td>*}>
                <td align="center">
                    <a href="<{if $items[i].isshortcut}>/admin.php?module=shortcuts&task=deleteItem&itemID=<{$items[i].shortcutID}><{else}><{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}><{/if}>" onclick="return confirm('<{$smarty.const.CONFIRM_DELETE}>');" title="<{$smarty.const.LABEL_DELETE}>">
                       <img src="<{$arrPageData.system_images}>delete.png" alt="<{$smarty.const.LABEL_DELETE}>" title="<{$smarty.const.LABEL_DELETE}>" />
                    </a>
                </td>
            </tr>
<{/section}>
    </table>

    <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr>
            <td width="107" align="left" style="padding:6px">
                <input type="checkbox" value="0" class="checkboxes check_all" onchange="SelectCheckBox(this);"/> Отметить все &nbsp;
            </td>
            <td width="155">
                <{*<a href="javascript:void(0);" onclick="toggleHiddenFields();"><{$smarty.const.ADMIN_TOGGLE_FIELDS}></a>*}>
                <div class="dropDown" style="display:none;">
                    C отмеченными
                    <ul>
                        <li data-val="publish" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                            <img src="/images/operation/check.png"/>&nbsp;&nbsp;опубликовать
                        </li>
                        <li data-val="unpublish" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                            <img src="/images/operation/un_check.png"/>&nbsp;&nbsp;не публиковать
                        </li>
                        <li data-val="delete" onclick="$(this).parent().parent().find('input').val($(this).data('val')); $(this).closest('form').submit();">
                            <img src="/images/operation/delete.png"/>&nbsp;&nbsp;удалить
                        </li>
                    </ul>
                    <input type="hidden" name="allitems" value=""/>
                </div>
            </td>
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
        <tr>
            <td align="left" colspan="3">&nbsp;Экспорт всех товаров: &nbsp;
                <a href="<{$arrPageData.current_url|cat:"&task=exportToCsv"}>">CSV</a>, 
                <a href="<{$arrPageData.current_url|cat:"&task=exportToYml"}>">YML</a>
            </td>
        </tr>
    </table>
</form>
<{/if}>
</div>
