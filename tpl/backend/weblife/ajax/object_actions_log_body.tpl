<{section name=i loop=$arHistoryData.history}>
    <tr>
        <td align="center"><{$arHistoryData.history[i].ts|date_format:"%d.%m.%Y %H:%M:%S"}></td>
        <td align="center"><{$arHistoryData.history[i].ip}></td>
        <td align="center">
            <{if $arHistoryData.history[i].uid!=-1}>
            <a target="_blank" href="/admin.php?module=users&task=viewItem&itemID=<{$arHistoryData.history[i].uid}>">
                <{$arHistoryData.history[i].user}>
            </a> [<{$arHistoryData.history[i].uid}>]
            <{else}>Система
            <{/if}>
        </td>
        <td align="left">
            <{assign var=Titles value=ActionsLog::getActionsTitle()}>
            <{if isset($Titles[<{$arHistoryData.history[i].action}>])}>
            <{$Titles[<{$arHistoryData.history[i].action}>]}>
            <{/if}>
        </td>
        <td><{$arHistoryData.history[i].comment}></td>
        <td align="center">                
            <{if $arHistoryData.history[i].message}>
                <a href="javascript:void(0)"  onclick="return parent.hs.htmlExpand (this, {contentId: 'my-content<{$smarty.section.i.index}>' })" class="highslide">
                    <img width="15" style="border:none;" src="/images/operation/find.png" />
                </a>
                <div class="highslide-html-content" id="my-content<{$smarty.section.i.index}>" style="padding:10px;">
                    <a href="#" style="text-align:right; display: block;" onclick="hs.close(this)">X</a>
                    <div class="highslide-body">
                         <{$arHistoryData.history[i].message}>
                    </div>
                </div>
            <{/if}>
        </td>
    </tr>
<{/section}>
