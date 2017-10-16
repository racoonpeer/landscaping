<{if isset($attachFile) && $attachFile}>
<tr>
    <td id="headb" align="left"><{$smarty.const.HEAD_ATTACH_FILE}></td>
    <td> 
        <table border="0" cellspacing="0" cellpadding="0" class="list">
            <tr onclick="toggleBox($(this).find('a.expand_link'), 'toogleFileBox');">
                <td align="center" valign="middle">
                    <a class="expand_link up" title="<{$smarty.const.HEAD_SHOW_HIDE}>"  href="javascript:void(0);"></a>
                </td>
            </tr>
        </table>

        <table style="margin-bottom:5px;" width="100%" border="0" cellspacing="0" cellpadding="0" class="list" id="toogleFileBox">
            <tr>
                <td width="85%">
                    <input name="filename" type="file"<{if !empty($item.filename)}> onchange="if(this.value.length){ $('#filename_delete').attr({checked:'true', readonly:'true'});};"<{/if}> />
                    <{if !empty($item.filename)}><{$smarty.const.HEAD_FILE}>: <a href="<{$arrPageData.files_url|cat:$item.filename}>" target="_blank"><{$item.filename}></a><{/if}>
                </td>
                <td align="center">
                    <input id="filename_delete" name="filename_delete" type="checkbox" value="1"<{if empty($item.filename)}> disabled<{/if}> onclick="if($(this).attr('readonly')){return false;}" /> <{$smarty.const.HEAD_DELETE}>
                </td>
                <td align="center">&nbsp;</td>
            </tr>
        </table>
    </td>
    <td class="buttons_row"></td>
</tr>
<{/if}>

<{if isset($attachVideo) && $attachVideo}>
<tr>
    <td id="headb" align="left"><{$smarty.const.HEAD_ATTACH_FILE}></td>
    <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0" class="list" id="toogleFileBox">
            <tr>
                <td id="head"><{$smarty.const.HEAD_ATTACH_FILE}></td>
                <td id="head" align="center"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
            <tr>
                <td width="95%">
                    <input name="filename" type="file"<{if !empty($item.filename)}> onchange="if(this.value.length){ $('#filename_delete').attr({checked:'true', readonly:'true'});};"<{/if}> />
                    <{if !empty($item.filename)}><{$smarty.const.HEAD_FILE}>: 
                    <a href="/flash/JWPlayer/player.swf" onclick="return hs.htmlExpand(this,
                    {
                        useBox:'true',
<{if !empty($item.fileinfo.highslide)}>
                        width: <{$item.fileinfo.highslide.width}>,
                        height: <{$item.fileinfo.highslide.height}>,
                        objectWidth: <{$item.fileinfo.highslide.resolution_x}>,
                        objectHeight: <{$item.fileinfo.highslide.resolution_y}>,
<{else}>
                        objectWidth: 640,
                        objectHeight: 480,    
<{/if}>
                        objectType: 'swf',
                        swfOptions: {
                            version: '9.0.0',
                            expressInstallSwfurl: '/flash/expressInstall.swf',
                            flashvars: { file: '&file=<{$arrPageData.files_url|cat:$item.filename}>&backcolor=000000&frontcolor=ffffff&lightcolor=555555&screencolor=000000&screencolor=000000', stretching:'fill',  autostart:<{if !empty($item.image)}>'false', image:'<{$arrPageData.files_url|cat:$item.image}>'<{else}>'true'<{/if}> },
                            params: { allowscriptaccess: 'always', allowfullscreen: 'true', quality: 'high' },
                            attributes: { id:'player<{$item.id}>', name:'player<{$item.id}>' }
                        },
                        dimmingOpacity: 0.75,
                        dimmingDuration: 75,
                        padToMinWidth: 'false',
                        preserveContent: 'false',
                        allowSizeReduction: 'false',
                        maincontentText: '<{$smarty.const.LABEL_UPGRADE_FLASH}>'
                    } );"  class="highslide" title="<{$item.title}>"><{$item.filename}></a><{/if}>
            </td>
                <td  align="center">
                    <input id="filename_delete" name="filename_delete" type="checkbox" value="1"<{if empty($item.filename)}> disabled<{/if}> onclick="if($(this).attr('readonly')){return false;}" />
                </td>
                <td  align="center">&nbsp;</td>
            </tr>
        </table>
    </td>
    <td class="buttons_row"></td>
</tr> 
<{/if}>

<{if ($arrPageData.task=='editItem' || $arrPageData.task=='addItem') && isset($attachImages) && $attachImages && !empty($item.arImagesSettings)}>
    <tr id="imageBlock">
        <td id="headb" align="left"><{if $arrPageData.module=='catalog'}>Фото товара<{else}>Изображение<{/if}></td>
        <td>
            <{section loop=$item.arImagesSettings name=s}>
                <{if ($arrPageData.task=='addItem' && !$item.arImagesSettings[s].ftable || $item.arImagesSettings[s].ftable==$item.arImagesSettings[s].ptable) || $arrPageData.task=='editItem'}>
                <a class="buttons left" 
                   href="/admin.php?module=images_uploadify&ajax=1&pmodule=<{$arrPageData.module}>&pid=<{$item.id}>&type=<{$item.arImagesSettings[s].column}>" 
                   onclick="return hs.htmlExpand(this, { headingText:'Управление файлами', objectType:'iframe', preserveContent: false, width:<{if !empty($item.arImagesSettings[s].ftable)}>1024<{else}>550<{/if}> } );">
                    <{$item.arImagesSettings[s].title}>
                </a>
                <{if empty($item.arImagesSettings[s].ftable)}>
                    <input type="hidden" id="arCropImagesParams_<{$item.arImagesSettings[s].column}>" name="imagesParams[<{$item.arImagesSettings[s].column}>]" value=""/>
                    <div class="left" id="<{$item.arImagesSettings[s].column}>_new" style="margin-left:10px; margin-top:4px; line-height:30px"></div>
                    <div class="left" id="<{$item.arImagesSettings[s].column}>_old" style="margin-left:10px; margin-top:4px; line-height:30px">
                        <{if !empty($item.<{$item.arImagesSettings[s].column}>)}>
                            &nbsp;&nbsp;
                            <a class="highslide" onclick="return parent.hs.expand (this, { })" href="<{$arrPageData.files_url}><{$item.<{$item.arImagesSettings[s].column}>}>">
                                <img src="<{$arrPageData.files_url}><{$item.<{$item.arImagesSettings[s].column}>}>" height="30" class="left"/>
                            </a>
                            &nbsp;&nbsp;<{$item.<{$item.arImagesSettings[s].column}>}>
                        <{/if}>
                    </div>
                <{else}>
                    <div class="left" id="<{$item.arImagesSettings[s].column}>_count" style="margin-left:10px; margin-top:4px; line-height:30px">загружено изображений: <b><{$item.imagesCount}></b> </div>
                <{/if}>
                <div class="clear"></div>
                <{/if}>
            <{/section}>
        </td>
        <td class="buttons_row"></td>
    </tr>
<{/if}>