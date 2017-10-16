<tr>
    <td id="headb" align="left"><{$smarty.const.HEAD_PAGE_IMAGE}></td>
    <td > 
        <table border="0" cellspacing="1" cellpadding="0" class="list">
            <tr onclick="toggleBox($(this).find('a.expand_link'), 'toogleImageBox');">
                <td  align="center" valign="middle">
                    <a class="expand_link up" title="<{$smarty.const.HEAD_SHOW_HIDE}>"  href="javascript:void(0);"></a>
                </td>
            </tr>
        </table>

        <table border="0" cellspacing="1" cellpadding="0" class="list" id="toogleImageBox">
            <tr>
                <td id="head" width="70%"><{$smarty.const.HEAD_IMAGE}></td>
                <td id="head" align="center"><{$smarty.const.HEAD_PAGE_IMAGE_PREVIEW}></td>
                <td id="head" align="center"><{$smarty.const.HEAD_WIDTH}></td>
                <td id="head" align="center"><{$smarty.const.HEAD_HEIGHT}></td>
                <td id="head" align="center"><{$smarty.const.HEAD_DELETE}></td>
            </tr>
            <tr id="toogleImageBox">
                <td width="70%">
                    <input name="image" type="file" <{if !empty($item.image)}> onchange="if(this.value.length){this.form.image_delete.disabled=true;}else{this.form.image_delete.disabled=false;}; this.form.image_w.value='';this.form.image_h.value='';"<{/if}> />
                    <{if !empty($item.image)}><br/><{$smarty.const.HEAD_FILE}>: 
                        <a href="javascript:popUp('<{$arrPageData.files_url|cat:$item.image}>')">
                            <{$item.image}>
                        </a>
                    <{/if}>
                </td>
                <td  align="center">
                    <{if !empty($item.image)}>
                    <a href="javascript:popUp('<{$arrPageData.files_url|cat:$item.image}>')">
                        <img align="center" src="<{$arrPageData.files_url|cat:$item.image}>" style="width: 100px;"/>
                    </a>
                    <{else}>
                        -
                    <{/if}>
                </td>
                <td align="center">
                    <input class="field" name="image_w" id="image_w" type="text" value="<{if !empty($item.image)}><{$item.arImageData.w}><{else}><{$arrPageData.def_img_param.w}><{/if}>" size="2" />
                </td>
                <td align="center">
                    <input class="field" name="image_h" id="image_h" type="text" value="<{if !empty($item.image)}><{$item.arImageData.h}><{else}><{$arrPageData.def_img_param.h}><{/if}>" size="2" />
                </td>
                <td align="center">
                    <input id="image_delete" name="image_delete" type="checkbox" value="1" <{if empty($item.image)}> disabled<{/if}> />
                </td>
                <td align="center">&nbsp;</td>
            </tr>
        </table> 
     </td>
     <td class="buttons_row"></td>
</tr>

             