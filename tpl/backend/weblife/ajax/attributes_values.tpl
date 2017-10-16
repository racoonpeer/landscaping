<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task="|cat:$arrPageData.task}><{if $arrPageData.itemID>0}><{''|cat:"&itemID="|cat:$arrPageData.itemID}><{/if}>" name="<{$arrPageData.task}>Form" enctype="multipart/form-data">
    <table class="list" width="100%">
        <tr>
            <td>
                <strong>Настройка допустимых значений</strong><br/><br/>
                <div id="tip"></div>
                <div class="left">
                    <input id="attrValue" style="margin-top:5px; height:24px; padding-left:5px;" type="text" value="" placeholder="введите значение" class="nosize_field" size="94"/>&nbsp;&nbsp;&nbsp;
                </div>
                <div class="left">
                    <input type="button" class="buttons" value="Добавить" onclick="addAttrValue();"/>
                </div>
                <div class="clear"></div> 
                <br/><strong>Допустимые значения атрибута:</strong> <a href="javascript:void(0)" onclick="removeAttrVal(this, 'all');">Очистить список</a>
                <div class="sortable-wrapper" style="width: 100%;">
                    <ul class="sortable" id="defaultVals">
<{foreach name=i from=$item.arValues key=arKey item=arValue}>
                        <li class="ui-state-default attrsort">
                            <input type="hidden" name="arValues[<{$arKey}>][id]" value="<{$arKey}>"/>
                            <input class="left field" type="text" name="arValues[<{$arKey}>][value]" value="<{$arValue.title}>" style="width: 200px;" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input class="left field" type="text" name="arValues[<{$arKey}>][seo_path]" value="<{$arValue.seo_path}>" style="width: 200px;" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре"<{/if}>/>
                            <input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form['arValues[<{$arKey}>][value]'].value.length==0){alert('Вы не ввели значение атрибута!'); this.form['arValues[<{$arKey}>][value]'].focus(); return false; } else{ generateSeoPath(this.form['arValues[<{$arKey}>][seo_path]'], this.form['arValues[<{$arKey}>][value]'].value, this.form.title.value);}">
                            <input type="file" name="arValues[<{$arKey}>][image]" value="" style="margin-top:4px"/>
<{if !empty($arValue.image)}>
                            <img src="<{$arrPageData.files_url|cat:$arValue.image}>" style="max-width:20px; max-height:20px;"/>
                            <input type="checkbox" name="arValues[<{$arKey}>][delete_image]" value="1"/> удалить 
<{/if}>
<{if $arValue.edit}>
                            <a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);"><img src="images/admin/error.png"/></a>&nbsp;
<{/if}>
                            <img class="right" src="images/sort.png" title="Нажмите и перетащите элемент на новое место в списке" <{if !$arValue.edit}>readonly title="недоступно для редактирования, так как используется в товаре" style="margin-right:35px;"<{/if}>/>
                            <div class="clear"></div>
                        </li>
<{/foreach}>
                    </ul>
                </div>
            </td>
        </tr>
        <tr>
            <td align="center">
                <input class="buttons" name="submit" type="submit" value="Сохранить">
            </td>
        </tr>
    </table>
</form>
<script type="text/javascript">    
    $(function() {
        $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
        $(document).keypress(function(e){
            if (e.which == 13 && $('#attrValue').val().length>0){
                addAttrValue();
                return false;
            }
        });
        
        $('#attrValue').autocomplete({
            source: function(request, response) {
                var arrValues = {};
                $.each($('ul.sortable').find('li').find('.field'), function() {
                    if($(this).val().indexOf(request.term)!=-1) {
                        arrValues[$(this).attr('name')] = $(this).val();
                    }
                });
                response($.map(arrValues, function(item, i) {
                    return {
                        label: item,
                        value: item,
                        name: i
                    }
                }));
            },
            select: function(event, ui) {
                $('ul.sortable').scrollTop($('ul.sortable').find('input[name="'+ui.item.name+'"]').position().top);
                $('ul.sortable').find('input[name="'+ui.item.name+'"]').focus();
                $(this).val("");
                return false;
            },
            minLength: 2
        });
    
    });
    
    function addAttrValue() {
        if( $('#attrType option:selected').val()==2 && !isNumber($('#attrValue').val())){
            $('#tip').text('Введите число или измените тип на "Текстовый"');
            $('#attrValue').addClass('error');
            return false;
        } else {
            if($('#attrValue').val().length>0) {
                $('#attrValue').removeClass('error');
                var maxID = <{if isset($item.arValuesMaxID)}><{$item.arValuesMaxID}> + <{/if}>$('ul.sortable').find('li').length;
                var html = '<li class="ui-state-default attrsort">'+
                           '<input type="hidden" name="arValues['+maxID+'][id]" value=""/>'+
                           '<input name="arValues['+maxID+'][value]" class="left field" type="text" value="'+$('#attrValue').val()+'" style="width: 200px;"/>'+
                           '<input name="arValues['+maxID+'][seo_path]" class="left field" type="text" value="" style="width: 200px;"/>'+
                           '<input type="button" value="Генерировать" style="float: left; margin: 3px 5px 0 0; min-width: 100px;" class="buttons" onclick="if(this.form[\'arValues['+maxID+'][value]\'].value.length==0){alert(\'Вы не ввели значение атрибута!\'); this.form[\'arValues['+maxID+'][value]\'].focus(); return false; } else{ generateSeoPath(this.form[\'arValues['+maxID+'][seo_path]\'], this.form[\'arValues['+maxID+'][value]\'].value, this.form.title.value);}">'+
                           '<input type="file" name="arValues['+maxID+'][image]" value=""/>'+ 
                           '<a class="right" href="javascript:void(0)" onclick="removeAttrVal(this);">'+
                           '<img src="images/admin/error.png"/></a>'+
                           '<img class="right" title="Нажмите и перетащите элемент на новое место в списке" src="images/sort.png"/>'+
                           '<div class="clear"></div>'+
                           '</li>';                           
                $('ul.sortable').append(html);  
                $('#attrValue').val('');
                $('#tip').text('');
                $('.sortable').find('input[type="text"]').mousedown(function(e){ e.stopPropagation(); });
            }
        }   
    }
       
    function removeAttrVal(item, removeAll) {
        if(typeof removeAll == 'undefined')
            $(item).parent().remove();
        else 
            $('ul.sortable').html('');
    }
</script>