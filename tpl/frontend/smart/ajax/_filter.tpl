<{* include file="ajax/_filter.tpl" fid=$filterID aid=$arKey $value='id' item=$arItem*}>
<li>  
    <label class="<{if $item.selected}>checked<{else if $item.cnt==0}>disabled<{/if}>" >
        <input type="checkbox" 
               id="checkbox_<{$fid}>_<{$aid}>" 
               name="filters[<{$fid}>][<{$aid}>]" 
               value="<{$item.$value}>"
               data-url="<{$item.url}>"
               <{if $item.selected}>checked<{elseif $item.cnt==0}>disabled<{/if}> 
               onchange="location.href=$(this).data('url');return false; updateFiltersList(this);"/>&nbsp;&nbsp;<{$item.$title}> (<{$item.cnt}>) <{if $item.cnt_diff > 0}>+<{$item.cnt_diff}><{elseif $item.cnt_diff < 0}><{$item.cnt_diff}><{/if}>
    </label>
</li>