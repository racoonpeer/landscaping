<{if !empty($arHistoryData.history)}>
    <table border="0" cellspacing="1" cellpadding="0" class="list history ">  
        <tr>
            <td id="headb" align="center" width="55">Дата</td>
            <td id="headb" align="center" >IP</td>
            <td id="headb" align="center" width="80">пользователь</td>
            <td id="headb" align="left">Действие</td>
            <td id="headb" align="left" width="223">Описание</td>
            <td id="headb" align="center" >Модуль</td>
            <td id="headb" align="center" width="100">Объект</td>
            <td id="headb" align="center">Язык</td>
            <td id="headb" align="center" width="4"></td>
        </tr>

        <{section name=i loop=$arHistoryData.history}>
            <tr>
                <td align="center"><{$arHistoryData.history[i].ts|date_format:"%d.%m.%Y %H:%M:%S"}></td>
                <td align="center"><{$arHistoryData.history[i].ip}></td>
                <td align="center">
                    <{if $arHistoryData.history[i].uid!=-1}>
                        <a target="_blank" href="/admin.php?module=users&task=viewItem&itemID=<{$arHistoryData.history[i].uid}>">
                            <{$arHistoryData.history[i].user}>
                        </a> [<{$arHistoryData.history[i].uid}>]
                    <{else}>
                        Система
                    <{/if}>
                </td>
                <td align="left">
                    <{assign var=Titles value=ActionsLog::getActionsTitle()}>
                    <{if isset($Titles[<{$arHistoryData.history[i].action}>])}><{$Titles[<{$arHistoryData.history[i].action}>]}><{/if}>
                </td>  
                <td align="left"><{$arHistoryData.history[i].comment}></td>
                <td align="center">
                    <{if !empty($arHistoryData.history[i].module)}><{$arHistoryData.history[i].module}><{else}>--<{/if}>
                </td>
                <td align="center">
                    <{if !empty($arHistoryData.history[i].object)}>
                        <{if !empty($arHistoryData.history[i].oid)}>
                            <a target="_blank" href="/admin.php?module=<{$arHistoryData.history[i].module}>&task=editItem&itemID=<{$arHistoryData.history[i].oid}>">
                                <{$arHistoryData.history[i].object}>
                            </a> 
                            [<{$arHistoryData.history[i].oid}>]
                        <{else}>
                            <{$arHistoryData.history[i].object}>
                        <{/if}>
                    <{else}>--<{/if}>
                </td>
                <td align="center"><{$arHistoryData.history[i].lang}></td>
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
    </table>
    <hr style="border-bottom: 1px solid #B6B6B6;"/>
    <br/><center>
        <{if $arHistoryData.total_pages>1}>
            <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
            <{include file='common/actions_log_pager.tpl' arrPager=$arHistoryData.pager page=$arHistoryData.page showTitle=0 showFirstLast=0 showPrevNext=0}>
            <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
        <{/if}>
    </center>
<{else}>
    <center><strong>Записей не найдено!</strong></center>
<{/if}>