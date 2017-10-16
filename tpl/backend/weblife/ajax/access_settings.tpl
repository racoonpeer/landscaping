
<ul>
    <li>
        <label>
            <input type="checkbox" value="auth" <{if in_array('auth', $availableModules)}>checked<{/if}> 
                   class="checkboxes auth" onchange="SelectCheckBox(this, '#group_<{$gid}>');"/> авторизация
        </label>
    </li>
</ul>
        
<{if !empty($arModules)}>
    <ul>
        <{foreach from=$arModules name=m item=arItem}>
            <li>
                <label>
                    <input type="checkbox" value="<{$arItem.module}>" 
                           <{if !in_array('auth', $availableModules)}>disabled<{else if in_array($arItem.module, $availableModules)}>checked<{/if}> 
                           class="checkboxes" onchange="SelectCheckBox(this, '#group_<{$gid}>');"/> <{$arItem.title}>
                </label>
            </li>
            <{if $smarty.foreach.m.iteration%3==0}></ul><ul><{/if}>
        <{/foreach}>
    </ul>
    <div class="clear"></div>
<{/if}>

<{if isset($uid) && !empty($uid)}>
    <input type="button" class="buttons left" style="margin-right:10px;" data-gid="<{$gid}>" data-action='reset' 
           onclick="getAccessSettings(this);" value="Восстановить значения по умолчанию"/>
<{/if}>
<input type="button" class="buttons" data-gid="<{$gid}>" data-action='save' onclick="getAccessSettings(this);" value="Сохранить"/>