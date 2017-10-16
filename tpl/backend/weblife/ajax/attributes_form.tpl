<tr id="attrGroup_<{$item.attrGroup.id}>" data-title="<{$item.attrGroup.title}> <{if $item.attrGroup.descr}>(<{$item.attrGroup.descr}>)<{/if}>" data-gid="<{$item.attrGroup.id}>">
    <td>
        <strong><{$item.attrGroup.title}> <{if $item.attrGroup.descr}>(<{$item.attrGroup.descr}>)<{/if}></strong>
        <a href="javascript:void(0);" data-gid="<{$item.attrGroup.id}>" class="del" onclick="removeAttributeRow(this);">Удалить</a>
        <div style="clear: both; margin-bottom: 10px;"></div>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
<{section name=j loop=$item.attrGroup.attributes}>
            <tr>
                <td style="min-width:135px;" <{if $item.attrGroup.attributes[j].descr}>title="<{$item.attrGroup.attributes[j].descr}>"<{/if}>>
                    <{$item.attrGroup.attributes[j].title}>
                </td>
                <td style="padding-bottom:10px;">                  
<{assign var=attValues value=$item.attrGroup.attributes[j]}>
<{if !empty($attValues.values)}>
                    <div class="attributes">
                        <input type="text" placeholder="введите значение для поиска" class="searchAttrValue" data-aid="<{$attValues.id}>" value="" size="80"/>
                        <input class="arAttrValues" type="hidden" name="attributes[<{$attValues.id}>]" value=""/>
                        <div class="selectedAttr"></div>
                    </div>
<{else}>
                    <input size="80" name="attributes[<{$attValues.id}>]" placeholder="<{if $attValues.tid==2}>число<{else}>текст<{/if}>" type="text" value="" />
<{/if}>
                </td>
            </tr>       
<{/section}>
        </table>
    </td>
</tr>
