<div class="addNew" <{if isset($shortcut) && $shortcut}>style="width:auto;"<{/if}>>
    <{if $arrPageData.task!='addItem' && $arrPageData.task!='editItem'}>
        <a  <{if isset($shortcut) && $shortcut}>style="padding-right:5px;"<{/if}> href="<{$arrPageData.current_url|cat:"&task=addItem"}>"><{$title}></a>
    <{/if}>
</div>
    
<{if isset($shortcut) && $shortcut}>
    <div class="addNew" style="width:auto; padding-right:5px">
        <a <{if isset($shortcut) && $shortcut}>style="padding-right:5px;"<{/if}> href="/admin.php?module=shortcuts<{if isset($arrPageData.cid) && $arrPageData.cid>0}>&cid=<{$arrPageData.cid}><{/if}>&task=addItem&object_module=<{$arrPageData.module}>">ÿנכûך</a>
    </div>
<{/if}>