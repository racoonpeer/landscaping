<tr>
    <td colspan="2">
        <strong><{$smarty.const.HEAD_META_DATA}></strong><br/><br/>
        <div class="inline"><{$smarty.const.HEAD_KEYWORDS}></div>
        <input type="text" name="meta_key" id="meta_key" size="94" value="<{$item.meta_key}>" /><br/><br/>
        <div class="inline"><{$smarty.const.HEAD_DESCRIPTION}> </div>
        <input type="text" name="meta_descr" id="meta_descr" size="94" value="<{$item.meta_descr}>" /><br/><br/>
        <div class="inline"><{$smarty.const.HEAD_ROBOTS}></div>
        <select name="meta_robots">
            <option value=""> &nbsp; <{$smarty.const.HEAD_NOT_SELECT}> &nbsp; </option>
<{section name=i loop=$arrPageData.robots}>
            <option value="<{$arrPageData.robots[i]}>"<{if $item.meta_robots==$arrPageData.robots[i]}> selected<{/if}>> &nbsp; <{$arrPageData.robots[i]}> &nbsp; </option>
<{/section}>
        </select>
    </td>
    <td class="buttons_row" valign="top" width="145" align="center">
        <!-- ++++++++++ Start Buttons ++++++++++++++++++++++++++++++++++++++++++++++ -->
        <{include file='common/buttons.tpl' itemID=$item.id task=$arrPageData.task deleteIDLimit=0}>
        <!-- ++++++++++ End Buttons ++++++++++++++++++++++++++++++++++++++++++++++++ -->
    </td>
</tr>
<tr>
    <td colspan="2">
        <strong><{$smarty.const.HEAD_SEO_DATA}></strong><br/><br/>
        <div class="inline"><{$smarty.const.HEAD_SEO_TITLE}></div>
        <input type="text" name="seo_title" id="seo_title" size="94" value="<{$item.seo_title}>"/><br/><br/>
        <div class="inline"><{$smarty.const.HEAD_SEO_PATH}></div>
        <input type="text" size="48" name="seo_path" id="seo_path" value="<{$item.seo_path}>"/>
        <input type="button" value="<{$smarty.const.HEAD_GENERATE}>" style="float: right;  margin: 0 35px 0 0;" class="buttons" onclick="if(this.form.title.value.length==0){ alert('<{$smarty.const.ALERT_EMPTY_PAGE_TITLE}>'); this.form.title.focus(); return false; } else{ generateSeoPath(this.form.seo_path, this.form.title.value, '<{$arrPageData.module}>'); }" />
    </td>
    <td class="buttons_row">&nbsp;</td>
</tr>
<{if isset($item.seo_text)}>
<tr>
    <td colspan="2">
        <strong><{$smarty.const.HEAD_SEO_TEXT}></strong><br/><br/>
        <textarea name="seo_text" id="seoText" style="width: 100%; height: 250px;"><{$item.seo_text}></textarea>
    </td>
    <td class="buttons_row">&nbsp;</td>
</tr>
<{/if}>