<!-- Items Start -->
<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
    <div><{$item.created|date_format:"%d.%m.%Y"}></div>
    <h2><{$item.title}></h2>
    <{$item.fulldescr}>
    <div id="countdown" style="padding: 20px; font-size: 24px; font-weight: bold;"></div>
<{if !empty($item.related)}>
    <h3>Товары участвующие в акции</h3>
    <div class="related">
        <{include file='ajax/products.tpl' items=$item.related}>
    </div>
<{/if}>
    <a href="<{$arrPageData.backurl}>"><{$smarty.const.URL_BACK_TO_LIST}></a>
    <script type="text/javascript">
        var timeend= new Date(<{$item.date_end|date_format:"%Y"}>, <{$item.date_end|date_format:"%m"}>-1, <{$item.date_end|date_format:"%d"}>);
        // IE и FF по разному отрабатывают getYear()
        // для задания обратного отсчета до определенной даты укажите дату в формате:
        // timeend= new Date(ГОД, МЕСЯЦ-1, ДЕНЬ);
        // Для задания даты с точностью до времени укажите дату в формате:
        // timeend= new Date(ГОД, МЕСЯЦ-1, ДЕНЬ, ЧАСЫ-1, МИНУТЫ);
        function countdown () {
            var today = new Date();
            today = Math.floor((timeend-today)/1000);
            var tsec=today%60; today=Math.floor(today/60); if(tsec<10)tsec='0'+tsec;
            var tmin=today%60; today=Math.floor(today/60); if(tmin<10)tmin='0'+tmin;
            var thour=today%24; today=Math.floor(today/24);
            var timestr= "До конца акции осталось "+today +" дней "+ thour+" часов "+tmin+" минут "+tsec+" секунд";
            document.getElementById('countdown').innerHTML=timestr;
            window.setTimeout("countdown()",1000);
        }
        countdown();
    </script>
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{elseif !empty($items)}>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tbody>
<{section name=i loop=$items}>
            <tr>
                <td>
                    <div><{$items[i].created|date_format:"%d.%m.%Y"}></div>
                    <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                        <h3><{$items[i].title}></h3>
                    </a>
                    <{$items[i].descr}>
                    <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                        <{$smarty.const.BUTTON_MORE}>
                    </a>
                </td>
            </tr>
<{/section}>
        </tbody>
    </table>
<{if $arrPageData.total_pages>1}>
    <!-- ++++++++++ Start PAGER ++++++++++++++++++++++++++++++++++++++++++++++++ -->
    <{include file='core/pager.tpl' arrPager=$arrPageData.pager page=$arrPageData.page showTitle=0 showFirstLast=0 showPrevNext=1 showAll=1}>
    <!-- ++++++++++ End PAGER ++++++++++++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>

<{* DISPLAY CATEGORY INFO *}>
<{else}>
<{include file='core/static.tpl'}>
<{/if}>
<!-- Items end-->
