<strong><{$option.title}></strong>: 
<{if $option.typename=="select"}>
<select id="options_<{$item.id}>_<{$option.id}>" name="options[<{$item.id}>][<{$option.id}>]" data-optionid="<{$option.id}>" onchange="Basket.changeOptions(<{$item.id}>, $(this).closest('.<{if $list}>product-item<{else}>details<{/if}>'), <{$list|intval}>, '<{$arCategory.module}>');">
<{foreach name=i from=$option.values key=valueID item=value}>
    <option value="<{$valueID}>" <{if $value.selected > 0}>selected<{/if}>><{$value.title}></option>
<{/foreach}>
</select>
<{elseif $option.typename=="radio"}>
<{foreach name=i from=$option.values key=valueID item=value}>
<input type="radio" name="options[<{$item.id}>][<{$option.id}>]" id="options_<{$item.id}>_<{$option.id}>_<{$valueID}>" value="<{$valueID}>" <{if $value.selected > 0}>checked<{/if}> data-optionid="<{$option.id}>" data-valueid="<{$valueID}>" onchange="Basket.changeOptions(<{$item.id}>, $(this).closest('.<{if $list}>product-item<{else}>details<{/if}>'), <{$list|intval}>, '<{$arCategory.module}>');"/> <{$value.title}>
<{/foreach}>
<{elseif $option.typename=="image"}>
<{foreach name=i from=$option.values key=valueID item=value}>
<div class="input-image">
    <img src="<{$value.image}>" alt=""/> 
    <input type="radio" name="options[<{$item.id}>][<{$option.id}>]" id="options_<{$item.id}>_<{$option.id}>_<{$valueID}>" value="<{$valueID}>" <{if $value.selected > 0}>checked<{/if}> data-optionid="<{$option.id}>" data-valueid="<{$valueID}>" onchange="Basket.changeOptions(<{$item.id}>, $(this).closest('.<{if $list}>product-item<{else}>details<{/if}>'), <{$list|intval}>, '<{$arCategory.module}>');"/>
</div>
<{/foreach}>
<{elseif $option.typename=="checkbox"}>
<{foreach name=i from=$option.values key=valueID item=value}>
<input type="checkbox" name="options[<{$item.id}>][<{$option.id}>][<{$valueID}>]" id="options_<{$item.id}>_<{$option.id}>_<{$valueID}>" value="<{$valueID}>" <{if $value.selected > 0}>checked<{/if}> data-optionid="<{$option.id}>" data-valueid="<{$valueID}>" onchange="Basket.changeOptions(<{$item.id}>, $(this).closest('.<{if $list}>product-item<{else}>details<{/if}>'), <{$list|intval}>, '<{$arCategory.module}>');"/> <{$value.title}>
<{/foreach}>
<{/if}>