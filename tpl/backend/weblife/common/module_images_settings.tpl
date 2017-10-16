<div class="image">
    <div class="right add_image">
        <input class="image_title" type="text" value="<{if isset($arImages.title)}><{$arImages.title}><{/if}>" name="arModulesImg[<{$module}>][<{$index}>][title]" placeholder="краткое название"/><br/><br/>
        <input class="column_name" type="text" value="<{if isset($arImages.column)}><{$arImages.column}><{/if}>" name="arModulesImg[<{$module}>][<{$index}>][column]" placeholder="колонка"/><br/><br/>
        <input class="parent_table" type="text" value="<{if isset($arImages.ptable)}><{$arImages.ptable}><{/if}>" name="arModulesImg[<{$module}>][<{$index}>][ptable]" placeholder="родительская таблица (имя константы)"/><br/><br/>
        <input class="files_table"  type="text" value="<{if isset($arImages.ftable)}><{$arImages.ftable}><{/if}>" name="arModulesImg[<{$module}>][<{$index}>][ftable]" placeholder="таблица для файлов (имя константы)"/><br/><br/>
        <input type="button" class="buttons right" value="Удалить изображение" onclick="removeImage(this);"/><br/>
    </div>

    <div class="left">
        <table border="1" cellspacing="1" cellpadding="1" style="width:auto" class="list user"> 
            <tr>
                <td id="headb" width="31"  align="center">Вкл</td>
                <td id="headb" width="40"  align="left">Размер</td>
                <td id="headb" width="100" align="center">Ширина</td>
                <td id="headb" width="100" align="center">Высота</td>
                <td id="headb" width="100" align="center">Заливка</td>
            </tr>

            <tr>
                <td width="31" ></td>
                <td width="130">параметры сжатия</td>
                <td width="100"  align="center">
                    <input type="text" class="nosize_field" 
                           name="arModulesImg[<{$module}>][<{$index}>][max_width]" size="10" value="<{if isset($arImages.max_width)}><{$arImages.max_width}><{/if}>" /> px
                </td>
                <td width="100"  align="center">
                    <input type="text" class="nosize_field" 
                           name="arModulesImg[<{$module}>][<{$index}>][max_height]" size="10" value="<{if isset($arImages.max_height)}><{$arImages.max_height}><{/if}>"/> px
                </td>
                <td width="100"  align="center" ></td>
            </tr>
            
            <tr> 
                <td width="31" align="center"></td>
                <td width="40"> параметры обрезки</td>
                <td width="100" align="center">
                    <input type="text" class="nosize_field crop width" size="10" value="<{if isset($arImages.crop_width)}><{$arImages.crop_width}><{/if}>"
                           name="arModulesImg[<{$module}>][<{$index}>][crop_width]"/> px 
                </td>
                <td width="100" align="center">
                    <input type="text" class="nosize_field crop height" size="10" value="<{if isset($arImages.crop_height)}><{$arImages.crop_height}><{/if}>"
                           name="arModulesImg[<{$module}>][<{$index}>][crop_height]"/> px 
                </td>
                <td width="100" align="center">
                        #<input type="text" size="10" class="nosize_field" value="<{if isset($arImages.crop_color)}><{$arImages.crop_color}><{/if}>"
                            name="arModulesImg[<{$module}>][<{$index}>][crop_color]" />
                </td>
            </tr>
            <{section loop=$aliases name=i}>
                <tr> 
                    <td width="31" align="center">
                        <input type="checkbox" <{if isset($arImages.aliases) && array_key_exists($aliases[i], $arImages.aliases)}>checked<{/if}> class="nosize_field disable_params" onclick="toogleParams(this)"/>
                    </td>
                    <td width="40">
                        <{$aliases[i]}>
                    </td>
                    <td width="100" align="center">
                        <input type="text" class="nosize_field param width" size="10"  value="<{if isset($arImages.aliases) &&  array_key_exists($aliases[i], $arImages.aliases)}><{$arImages.aliases.<{$aliases[i]}>.width}><{/if}>"
                               name="arModulesImg[<{$module}>][<{$index}>][arMiniatures][<{$aliases[i]}>][width]"/> px 
                    </td>
                    <td width="100" align="center">
                        <input type="text" class="nosize_field param height" size="10" value="<{if isset($arImages.aliases) &&  array_key_exists($aliases[i], $arImages.aliases)}><{$arImages.aliases.<{$aliases[i]}>.height}><{/if}>"
                               name="arModulesImg[<{$module}>][<{$index}>][arMiniatures][<{$aliases[i]}>][height]"/> px 
                    </td>
                    <td width="100" align="center"></td>
                </tr>
            <{/section}>
        </table>
    </div>
    <div class="clear"></div>
</div>

<script type="text/javascript">
    $.each($('.tabsContainer').find('.disable_params'), function() {
        toogleParams(this);
    });
    
    $.each($('.crop'), function() {
        $(this).on('keyup', function() {
            $.each($(this).closest('table').find('.param'), function() {
                recalcRatio($(this));
            });
        });
    });       
    
    $.each($('.param'), function() {
        $(this).on('keyup', function() {
            recalcRatio($(this));
        });
    });
    
    function toogleParams(cb){
        if(!cb.checked) {
            $(cb).closest('tr').find('.param').prop('disabled', true);
        } else {
            $(cb).closest('tr').find('.param').prop('disabled', false);
        }
    }
</script>