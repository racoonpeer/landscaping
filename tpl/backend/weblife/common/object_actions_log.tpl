<{if !empty($arHistoryData.history)}>
    <input type="hidden" id="nextpage" value="<{$arHistoryData.page+1}>">
    <table border="0"  id="object_history" cellspacing="1" cellpadding="0" class="list ">
        <tr>
            <td colspan="3" valign="top" >
                <table border="0" cellspacing="1" cellpadding="0" class="list history ">  
                    <tr>
                        <td id="headb" align="center" width="55">Дата</td>
                        <td id="headb" align="center" width="80">IP</td>
                        <td id="headb" align="center" width="100">пользователь</td>
                        <td id="headb" align="left" width="110">Действие</td>
                        <td id="headb" align="left">Описание</td>
                        <td id="headb" width="4"></td>
                    </tr>

                    <{include file="ajax/object_actions_log_body.tpl" arHistoryData=$arHistoryData}>
                </table>
            </td>
        </tr>
    </table>
<{/if}>
    
<{if !empty($arHistoryData.total_pages) && $arHistoryData.total_pages>1}>
    <script type="text/javascript">
        $( window ).scroll(function() {
            if($('.tabsContainer').find('li a.active').attr('data-target') == 'history') {
                if(History.enabled) {
                    History.pushState(null, document.title, '');
                }
                var documentHeight = $(document).height();
                var scrollDifference = $(window).height() + $(window).scrollTop();
                if (documentHeight == scrollDifference){ 
                    showMore($('#nextpage').val());
                }
            }
        });

        function showMore(nextPage) {
            if(nextPage){
                $.ajax({
                    url: '/interactive/ajax.php?zone=admin&action=filterActionsLog'+'<{$arHistoryData.filtersUrl}>'+'&type=more&filters[page]='+nextPage,
                    type: 'GET',
                    dataType: 'json',
                    success: function(json) {
                        if(json) {
                            $('#object_history  tr:last').after(json.history);
                            $('#nextpage').val(json.page);
                        }
                    }
                });
            }
        }
    </script>
<{/if}>