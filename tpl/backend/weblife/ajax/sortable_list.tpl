<{if $item.type=="attributes"}>
<{section name=i loop=$item.attributes}>
<li class="ui-state-default ui-state-disabled" data-gid="<{$item.attributes[i].gid}>">
    <input type="checkbox" name="attributes[]" onchange="toggleBoxState(this);" value="<{$item.attributes[i].id}>"/> <label title="<{$item.attributes[i].descr}>"><{$item.attributes[i].title}> (<{$item.attributes[i].gtitle}>)</label>
</li>
<{/section}>
<{elseif $item.type=="filters"}>
<{section name=i loop=$item.filters}>
<li class="ui-state-default ui-state-disabled" data-fid="<{$item.filters[i].id}>">
    <input type="checkbox" name="filters[<{$item.filters[i].type}>][]" onchange="toggleBoxState(this);" value="<{$item.filters[i].id}>"/> <label><{$item.filters[i].title}> <strong><{$item.filters[i].alias}></strong></label> 
<{if $item.filters[i].tid==1}>
    <a href="/admin.php?module=brands" target="_blank">
        <img src="/images/operation/edit.png" height="10">
    </a>
<{elseif $item.filters[i].tid!=1 AND !empty($item.filters[i].aid)}>
    <a href="/admin.php?module=attributes_values&task=editItem&itemID=<{$item.filters[i].aid}>&ajax=1" onclick="return hs.htmlExpand(this, {headingText:'<{$smarty.const.ATTRIBUTES}>: <{$item.filters[i].title}>', objectType:'iframe', preserveContent: false, width:910});">
        <img src="/images/operation/edit.png" height="10">
    </a>
<{/if}>
</li>
<{/section}>
<{/if}>