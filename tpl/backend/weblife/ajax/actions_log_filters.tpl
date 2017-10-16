<h3>Фильтрация &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="updateHistory(false, 'refresh');">сбросить</a></h3>

<hr/>
<{if !empty($arFilters)}>
    <ul>  
    <li><strong>Дата</strong><br/>
        <ul>
            <li>
                <label><input type="radio" name="filters[time]" <{if (isset($selectedFilters.time) && $selectedFilters.time==1) || !isset($selectedFilters.time) }>checked<{/if}>  onchange="toogleDateTime(this);" value="1" class="datetime"/> за сегодня</label>
            </li>
            <li>
                <label><input type="radio" name="filters[time]"  <{if (isset($selectedFilters.time) && $selectedFilters.time==2) }>checked<{/if}> onchange="toogleDateTime(this);" value="2" class="datetime show" /> выбрать</label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" style="font-size:11px;" onclick="clearDate();">очистить</a>
                <div id="datetime" class=" <{if !isset($selectedFilters.time) || (isset($selectedFilters.time) && $selectedFilters.time==1) }>hidden_block<{/if}>">
                    <br/>с &nbsp;&nbsp; <input type="text" 
                           id="date_from" 
                           size="22" 
                           value="<{if isset($selectedFilters.from) }><{$selectedFilters.from|date_format:"%Y-%m-%d"}><{/if}>" 
                           name="filters[from]" /><br/><br/>
                    по&nbsp; <input type="text" 
                              id="date_to" 
                              size="22" 
                              value="<{if isset($selectedFilters.to) }><{$selectedFilters.to|date_format:"%Y-%m-%d"}><{/if}>" 
                              name="filters[to]" />
                </div>
            </li>
        </ul>
    </li>    
    <{if !empty($arFilters.modules)}>
        <li><strong>Модули</strong>
        <ul>
            <{section name=i loop=$arFilters.modules}>
                <li>
                    <label>
                        <input type="checkbox" onchange="updateHistory(this);" data-type="modules" name="filters[modules][<{$smarty.section.i.iteration}>]" value="<{$arFilters.modules[i]}>"
                               <{if isset($selectedFilters.modules) && in_array($arFilters.modules[i], $selectedFilters.modules)}>checked<{/if}> >  
                        <{assign var=Titles value=ActionsLog::getModulesTitle()}>
                        <{if isset($Titles[<{$arFilters.modules[i]}>])}><{$Titles[<{$arFilters.modules[i]}>]}><{/if}> (<{$arFilters.modules[i]}>)
                    </label>
                </li>
            <{/section}>
        </ul>
        </li>
    <{/if}>
    <{if !empty($arFilters.actions)}>
        <{if isset($arFilters.actions.main)}>
        <li><strong>Действия</strong>
        <ul>
            <{section name=i loop=$arFilters.actions.main}>
                <li>
                    <label>
                        <input type="checkbox"  onchange="updateHistory(this);" data-type="actions" name="filters[actions][<{$smarty.section.i.iteration}>]" value="<{$arFilters.actions.main[i]}>"
                               <{if isset($selectedFilters.actions) && in_array($arFilters.actions.main[i], $selectedFilters.actions)}>checked<{/if}>>  
                        <{assign var=Titles value=ActionsLog::getActionsTitle()}>
                        <{if isset($Titles[<{$arFilters.actions.main[i]}>])}><{$Titles[<{$arFilters.actions.main[i]}>]}><{/if}>
                    </label>
                </li>
            <{/section}>
        </ul>
        </li>
        <{/if}>
        <{if isset($arFilters.actions.order)}>
        <li><strong>Редактирование заказов</strong>
        <ul>
            <{section name=i loop=$arFilters.actions.order}>
                <li>
                    <label>
                        <input type="checkbox"  onchange="updateHistory(this);" data-type="actions" name="filters[actions][<{$smarty.section.i.iteration}>]" value="<{$arFilters.actions.order[i]}>"
                               <{if isset($selectedFilters.actions) && in_array($arFilters.actions.order[i], $selectedFilters.actions)}>checked<{/if}>>  
                        <{assign var=Titles value=ActionsLog::getActionsTitle()}>
                        <{if isset($Titles[<{$arFilters.actions.order[i]}>])}><{$Titles[<{$arFilters.actions.order[i]}>]}><{/if}>
                    </label>
                </li>
            <{/section}>
        </ul>
        </li>
        <{/if}>
    <{/if}>
    <{if !empty($arFilters.langs)}>
        <{assign var=Langs value=SystemComponent::getAcceptLangs()}>
        <li><strong>Языки</strong>
            <ul>
                <{section name=i loop=$arFilters.langs}>
                    <{if array_key_exists($arFilters.langs[i], $Langs)}>
                        <li>
                            <label><input type="checkbox" onchange="updateHistory(this);" data-type="langs" name="filters[langs][<{$smarty.section.i.iteration}>]" value="<{$arFilters.langs[i]}>"
                                <{if isset($selectedFilters.langs) && in_array($arFilters.langs[i], $selectedFilters.langs)}>checked<{/if}>>      
                                <{$Langs[<{$arFilters.langs[i]}>].name}>
                            </label>
                        </li>
                    <{/if}>
                <{/section}>
            </ul>
        </li>
    <{/if}>
    <{if !empty($arFilters.users)}>
        <li><strong>Пользователи</strong>
            <ul><li>
                <select name="filters[user]" style="width:160px;" onchange="updateHistory();" >
                     <option value="0" >не выбран</option>
                     <{section name=i loop=$arFilters.users}>
                         <option value="<{$arFilters.users[i].id}>" <{if isset($selectedFilters.user) && $selectedFilters.user==$arFilters.users[i].id}>selected<{/if}>>
                             <{if $arFilters.users[i].id==-1}>Система<{else}><{$arFilters.users[i].name}><{/if}>
                         </option>
                     <{/section}>
                 </select>
            </li></ul>
        </li>
    <{/if}>
    </ul>
<{/if}>
        
    
<script type="text/javascript">
    $(document).ready(function() {
        var dateFrom = $('#date_from');
        var dateTo = $('#date_to');

        $(dateFrom).datepicker({ 
            dateFormat: "yy-mm-dd", 
            onClose: function() {
                updateHistory();
            }
        });

        $(dateTo).datepicker({ 
            dateFormat: "yy-mm-dd", 
            onClose: function() {
                updateHistory();
            }
        });

        $(dateFrom).on('change', function(){ 
            $(dateTo).datepicker('option', 'minDate', $(dateFrom).val()); 
        });
    });
</script>