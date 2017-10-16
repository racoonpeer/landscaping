<div id="messages" class="<{if !empty($arrPageData.errors)}>error<{elseif !empty($arrPageData.messages)}>info<{else}>hidden_block<{/if}>">
<{if !empty($arrPageData.errors)}>
    <{$arrPageData.errors|@implode:'<br/>'}>
<{elseif !empty($arrPageData.messages)}>
    <{$arrPageData.messages|@implode:'<br/>'}>
<{/if}>
</div>
<form method="post" action="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=editItems"}>" name="editItems" id="ajaxEditForm"  enctype="multipart/form-data">
    <input type="hidden" id="x" name="coords[x]" />
    <input type="hidden" id="y" name="coords[y]" />
    <input type="hidden" id="w" name="coords[w]" />
    <input type="hidden" id="h" name="coords[h]" />
    
    <input type="hidden" id="bg_w" name="coords[bg_w]" />
    <input type="hidden" id="bg_h" name="coords[bg_h]" />
    <input type="hidden" id="crop" name="crop" value="">
    
    <input type="hidden" id="imagePath" name="coords[path]" value="<{if isset($arrPageData.image)}><{$arrPageData.image.path}><{/if}>"/>
    <input type="hidden" id="imageName" name="coords[name]" value="<{if isset($arrPageData.image)}><{$arrPageData.image.name}><{/if}>"/>
        
    <table width="100%" cellspacing="0" cellpadding="0" border="0" class="list">
        <tbody>
            <tr>
                <td valign="top" width="50%">
                    <table width="100%" cellspacing="1" cellpadding="0" border="0" class="list">
                        <tbody>
                            <tr>
                                <td>
                                    <input id="file" type="file" name="file" ><br/>
                                    <{if isset($arrPageData.image)}><strong>Выбрано:</strong> <{$arrPageData.image.name}>&nbsp;&nbsp; <label><input type="checkbox" value="1" name="allLangs" checked/> загрузить для всех языков</label><br/><{/if}>
                                    <{if !empty($items) && !$arItem.diffTables}>
                                        <strong>Загружено:</strong> 
                                        <{section name=i loop=$items}>
                                            <{if !empty($items[i].<{$arItem.column}>)}>
                                                <div style="border:1px solid #888; display: inline-block; padding:5px;">
                                                    <a href="<{$items[i].filename}>" onclick="return parent.hs.expand (this, { })" class="highslide">
                                                        <img src="<{$items[i].filename}>" align="center" style="max-width:60px; max-height:60px;"/>  
                                                    </a>
                                                </div>&nbsp;&nbsp;
                                                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('Вы уверены что хотите удалить файл?');" title="Delete!">
                                                   Удалить
                                                </a>
                                                <{if $items[i].allLangs}>
                                                    | <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&langs=all&itemID="|cat:$items[i].id}>" onclick="return confirm('Вы уверены что хотите удалить файл?');" title="Delete!">
                                                        Удалить со всех языков
                                                     </a>
                                                <{/if}>
                                            <{/if}>
                                        <{/section}>
                                    <{/if}>
                                </td>
                            </tr>

                            <tr><td>&nbsp;</td><tr>
                                
                            <tr class="invisible">
                                <td align="center">
                                    <div style="border:2px solid #888; width: 480px; height:480px;">
                                        <div id="preview" class="preview">
                                            <img src="<{if isset($arrPageData.image)}><{$arrPageData.image.path}><{/if}>" />
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" id="submitButton" style="<{if !isset($arrPageData.image)}>display:none<{/if}>">
                                    <input type="submit" value="<{if !$arItem.diffTables}>Запомнить координаты<{else}>Загрузить<{/if}>" class="buttons left" style="margin-left:20%; margin-right:20px;" onclick="return ImagesUpload.submitCrop();"/>
                                    <input  type="button"  value="Отменить" class="buttons left" onclick="$(ImagesUpload.old_image).show(); $(ImagesUpload.new_image).html(''); parent.$('#arCropImagesParams_<{$arItem.column}>').val(''); window.location.href = window.location.href;"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
<{if !empty($items) && $arItem.diffTables}>
                <td valign="top"  width="50%">                   
                    <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list" id="operationTbl">
                        <tr>
                            <td id="headb" align="center" style="width:15px;"></td>
                            <td id="headb" align="center" width="22">Глав</td>
                            <td id="headb" align="center" width="38"></td>
                            <td id="headb" align="center" width="38">Превью</td>
                            <td id="headb" align="left"><{$smarty.const.HEAD_NAME}></td>
                            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_SORT}></td>
                            <td id="headb" align="center" width="38"><{$smarty.const.HEAD_DELETE}></td>
                        </tr>
<{section name=i loop=$items}>
                         <tr>
                            <td align="center">
                                <input type="checkbox" class="checkboxes" name="arItems[<{$items[i].id}>]" onchange="SelectCheckBox(this)" value="1" />
                            </td>
                            <td align="center">
                                <input type="hidden" name="arItemsId[<{$items[i].id}>]" value="<{$items[i].id}>" />
                                <input type="radio" name="default" value="<{$items[i].id}>" <{if $items[i].isdefault==1}>checked<{/if}> />
                            </td>
                            <td align="center">
<{if $items[i].active==1}>
                                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=0&itemID="|cat:$items[i].id}>" title="Publication">
                                    <img src="<{$arrPageData.system_images}>check.png" alt="Publication" title="Publication" />
                                </a>
<{else}>
                                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=publishItem&status=1&itemID="|cat:$items[i].id}>" title="No Publication">
                                    <img src="<{$arrPageData.system_images}>un_check.png" alt="No Publication" title="No Publication" />
                                </a>
<{/if}>
                            </td>
                            <td align="center" valign="center">
                                <a href="<{$items[i].filename}>" onclick="return parent.hs.expand (this, { })" class="highslide">
                                    <img src="<{$items[i].filename}>" align="center" style="max-width:40px; max-height:40px; border:none !important;"/>
                                </a>
                            </td>
                            <td>
                                <input type="text" size="45" class="field" name="arTitle[<{$items[i].id}>]" value="<{$items[i].title}>" />
                            </td>
                            <td align="center">
                                <input class="field_smal" name="arOrder[<{$items[i].id}>]" type="text" id="order" value="<{$items[i].fileorder}>" style="width:27px;padding-left:0px;text-align:center;" maxlength="4" />
                            </td>
                            <td align="center">
                                <a href="<{$arrPageData.current_url|cat:$arrPageData.filter_url|cat:"&task=deleteItem&itemID="|cat:$items[i].id}>" onclick="return confirm('Вы уверены что хотите удалить файл?');" title="Delete!">
                                   <img src="<{$arrPageData.system_images}>delete.png" alt="Delete!" title="Delete!" />
                                </a>
                            </td>
                        </tr>
<{/section}>
                </table>
                    
                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                    <tr>
                        <td align="left" width="117">
                            <input type="checkbox" value="0" style="margin-left:6px;" class="checkboxes check_all" onchange="SelectCheckBox(this);"/> Отметить все &nbsp;
                        </td>
                        <td width="155">
                            <div class="dropDown" style="display:none;">
                                C отмеченными
                                <ul>
                                    <li data-val="publish" onclick="checkBoxOperations('publishItems', '1')">
                                        <img src="/images/operation/check.png"/>&nbsp;&nbsp;опубликовать
                                    </li>
                                    <li data-val="unpublish" onclick="checkBoxOperations('publishItems', '0')">
                                        <img src="/images/operation/un_check.png"/>&nbsp;&nbsp;не публиковать
                                    </li>
                                    <li data-val="delete" onclick="checkBoxOperations('deleteItems', '1')">
                                        <img src="/images/operation/delete.png"/>&nbsp;&nbsp;удалить
                                    </li>
                                </ul>
                                <input type="hidden" name="allitems" value=""/>
                            </div>
                        </td>
                        <td align="right">
                            <input name="submit_order" class="buttons" type="submit" value="<{$smarty.const.BUTTON_APPLY}>" />
                        </td>
                    </tr>
                </table>
                <table width="100%" border="0" cellspacing="1" cellpadding="0">
                    <tr>
                       <td align="center" width="70%">
                            <{if $arrPageData.total_pages>1}>
                                <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
                                <{include file='common/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
                                <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
                            <{/if}>
                        </td>
                        <td align="right">
                            Окно:&nbsp;
                            <a href="javascript:void(0)" onclick="window.location.reload();" title="<{$smarty.const.BUTTON_RELOAD}>">Обновить</a>&nbsp;
                            <a href="javascript:void(0)" onclick="parent.window.hs.close();" title="<{$smarty.const.BUTTON_EXIT}>">Закрыть</a>
                        </td>
                    </tr>
                </table>
            </td>
<{/if}>
        </tr>
    </tbody>
</table>
</form>

<script src="/js/libs/jCrop/jquery.Jcrop.js"></script> 
<script src="/js/libs/imagesloaded/imagesloaded.js"></script>
<link href="/js/libs/jCrop/jquery.Jcrop.css" rel="stylesheet" type="text/css" />
<script type="text/javascript">   
    $(document).ready(function(){ 
        <{if !$arItem.diffTables}>
            <{if empty($items)}>$(ImagesUpload.old_image).html('');<{/if}>
            var data = {};
            if(parent.$('#arCropImagesParams_<{$arItem.column}>').val().length > 0) {
                data = JSON.parse(parent.$('#arCropImagesParams_<{$arItem.column}>').val());
                $('#preview').find('img').attr('src', data.path);
                $('#imagePath').val(data.path);
                $('#imageName').val(data.name);         
                $('#submitButton').show();
            }
        <{else}>
            var img_count = $('#operationTbl').find('tr').length-1;
            if(img_count<0) img_count = 0;
            $(ImagesUpload.imagesCount).html('загружено изображений: <b>'+img_count+'</b>');
        <{/if}>
        
        if($('#preview').find('img').attr('src').length>0) {          
            $('#preview').find('img').imagesLoaded( function() {
                if(parent.window.hs.getExpander()) parent.window.hs.getExpander().reflow();
                ImagesUpload.prepareImage();
            });
        }
                
        $("#file").change(function() {
            parent.$('#<{$arItem.column}>_data').html('');
            <{if !$arItem.diffTables}>parent.$('#arCropImagesParams_<{$arItem.column}>').val('');<{/if}>
            $('#ajaxEditForm').submit();
        });
    });
    
    ImagesUpload = {
        preview: $('#preview'),
        image: $('#preview').find('img'),
        coeff: 1,
        crop_w: <{if !empty($arItem.crop_width)}><{$arItem.crop_width}><{else}>0<{/if}>,
        crop_h: <{if !empty($arItem.crop_height)}><{$arItem.crop_height}><{else}>0<{/if}>,
        diffImages: <{if !empty($arItem.diffTables)}><{$arItem.diffTables}><{else}>false<{/if}>,
        new_image: parent.$('#<{$arItem.column}>_new')||false,
        old_image: parent.$('#<{$arItem.column}>_old')||false,
        imagesCount: parent.$('#<{$arItem.column}>_count')||false,
        
        bgW: $('#bg_w'),
        bgH: $('#bg_h'),
        x: $('#x'),
        y: $('#y'),
        w: $('#w'),
        h: $('#h'),
        
        checkCoords: function() {
            var _self = this;
            
            var w = parseInt($(_self.w).val());
            if (w || ($(_self.image).attr('src').length>0 && !w)) 
                return true;
            alert('Вы не выбрали изображение!');
            return false;
        },
            
        updateCoords: function(c) {
            $(ImagesUpload.bgW).val(ImagesUpload.coeff*parseInt($(ImagesUpload.preview).width()));
            $(ImagesUpload.bgH).val(ImagesUpload.coeff*parseInt($(ImagesUpload.preview).height()));

            $(ImagesUpload.x).val(ImagesUpload.coeff*c.x);
            $(ImagesUpload.y).val(ImagesUpload.coeff*c.y);
            $(ImagesUpload.w).val(ImagesUpload.coeff*c.w);
            $(ImagesUpload.h).val(ImagesUpload.coeff*c.h);
        },
        
        prepareImage: function() {
            var _self = this;

            var aspectRatio = _self.crop_w/_self.crop_h; 

            if(_self.crop_w>0 && _self.crop_h>0){
                
                // в сколько раз уменьшилось изображение
                _self.coeff= $(_self.image)[0].naturalWidth/$(_self.image).width();     
                var imgAspectRatio = $(_self.image).width()/$(_self.image).height();

                //в столько же раз уменьшаем рамку
                _self.crop_w = _self.crop_w/_self.coeff;
                _self.crop_h = _self.crop_h/_self.coeff;

                // проверяем влезает ли рамка в блок превью
                if(_self.crop_w>$(_self.preview).width() || _self.crop_h>$(_self.preview).height()) { 
                    // если рамка не влезает то уменьшаем и получаем коеф
                    var crop_ratio;
                    if(_self.crop_w>_self.crop_h) {
                        crop_ratio = _self.crop_w/$(_self.preview).width();       
                        _self.crop_w = $(_self.preview).width();
                        _self.crop_h = _self.crop_h/crop_ratio;
                    } else {
                        crop_ratio = _self.crop_h/$(_self.preview).height();
                        _self.crop_h = $(_self.preview).height();
                        _self.crop_w = _self.crop_w/crop_ratio;
                    }
                    
                    //уменьшаем изображение в столько же раз сколько и рамка уменьшилась
                    $(_self.image).width($(_self.image).width()/crop_ratio);    
                    _self.coeff = _self.coeff*crop_ratio;            
                }
                
                var r = 1; 
                // если ширина больше чем ширина кропинга, то получаем коефф на солько больше
                if($(_self.image).width() > _self.crop_w) {
                    r = $(_self.image).width()/_self.crop_w;
                   $(_self.image).width(_self.crop_w);
                } 
                // если  ширина больше чем ширина кропинга, то получаем коефф на солько больше
                else if($(_self.image).height() > _self.crop_h) {
                    r = $(_self.image).height()/_self.crop_h;
                   $(_self.image).height(_self.crop_h);
                }    
                _self.coeff = _self.coeff*r;
                
                //в столько же раз уменьшаем рамку
                _self.crop_w = _self.crop_w/r;
                _self.crop_h = _self.crop_h/r;
                
                // если пропорции изображения совпадают с пропорциями рамки, то устанавливаем target на изображение
                var setSelect = [];
                if(parent.$('#arCropImagesParams_<{$arItem.column}>').length>0 && parent.$('#arCropImagesParams_<{$arItem.column}>').val().length > 0) {
                    var data = JSON.parse(parent.$('#arCropImagesParams_<{$arItem.column}>').val());
                    if(data) {
                        setSelect = [(data.x/_self.coeff), (data.y/_self.coeff), (data.x/_self.coeff)+(data.w/_self.coeff), (data.y/_self.coeff)+(data.h/_self.coeff)];
                    }
                } else {
                    if(aspectRatio == imgAspectRatio && $(_self.image).width() == $(_self.preview).width()) {
                        setSelect = [0, 0, $(_self.image).width(), $(_self.image).height()];
                    } else {      
                        var x = ($(_self.preview).width()/2) - (_self.crop_w/2);
                        var y = ($(_self.preview).height()/2) - (_self.crop_h/2);
                        setSelect = [x, y, x+_self.crop_w, y+_self.crop_h];
                    }
                }
                
                // инитим кропинг
                $(_self.preview).Jcrop({
                    bgColor: 'white',
                    bgOpacity: .3,
                    aspectRatio: aspectRatio,
                    minSize: [_self.crop_w, _self.crop_h],
                    setSelect: setSelect,
                    onSelect: _self.updateCoords
                });
            }
            // опускаем изображение, если его высота меньше высоты блока превью
            if($(_self.image).height() < $(_self.preview).height()){
               $(_self.image).css('margin-top', ($(_self.preview).height()/2)-($(_self.image).height()/2));
            } 
            $(_self.preview).closest('tr').removeClass('invisible');
        },
                
        submitCrop: function() {
            if(ImagesUpload.checkCoords()) {
                if(!ImagesUpload.diffImages) {
                    var result = {
                        x : $(ImagesUpload.x).val(),
                        y : $(ImagesUpload.y).val(),
                        w : $(ImagesUpload.w).val(),
                        h : $(ImagesUpload.h).val(),

                        path : $('#imagePath').val(),
                        name : $('#imageName').val(),

                        bg_w : $(ImagesUpload.bgW).val(),
                        bg_h : $(ImagesUpload.bgH).val(),

                    };
                    
                    $(ImagesUpload.new_image).html('&nbsp;&nbsp;<a class="highslide" onclick="return parent.hs.expand (this, { })" href="'+result.path+'"><img src="'+result.path+'" height="30" class="left"/></a>&nbsp;&nbsp;'+result.name+' &nbsp;(<b>несохранено</b>) <span style="font-size:14px; cursor:pointer;" onclick="$(this).parent().hide(); $(\'#<{$arItem.column}>_old\').show(); $(\'#arCropImagesParams_<{$arItem.column}>\').val(\'\');"><b>X</b></span>');
                    $(ImagesUpload.new_image).show();
                    $(ImagesUpload.old_image).hide();
                    parent.$('#arCropImagesParams_<{$arItem.column}>').val(JSON.stringify(result));
                    parent.window.hs.close();
                } else {
                    $('#crop').val('1');
                    return true;
                }
            }
            return false;
        }
        
    }
   
    function checkBoxOperations(task, value){
         var inputs = $('#operationTbl').find("input[type=checkbox]:checked:not(#checkAll)"); 
         if(inputs.length > 0) {
             var data = '';
             $.each( inputs, function() {
                 data += '&'+[$(this).attr("name")]+'='+value;
             });
             window.location = "<{$arrPageData.current_url}>"+"<{$arrPageData.filter_url}>"+"&task="+task+data;
         }
     }
</script>
