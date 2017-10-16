<{* REQUIRE VARS: $arItems=array(), $selID=int, $marginLevel=int <{include file='menu/tree_redirects.tpl' arItems=$arItems selID=0 marginLevel=0}>  *}>
<{section name=i loop=$arItems}>
                        <option value="<{$arItems[i].id}>"<{if $selID==$arItems[i].id}> selected<{elseif $arItems[i].disabled}> disabled<{/if}>> &nbsp;&nbsp;&nbsp;<{'&nbsp;&nbsp;&nbsp;'|str_repeat:$marginLevel}>L... <{$arItems[i].title}> &nbsp; </option>
<{if !empty($arItems[i].subcategories)}><{include file='common/tree_redirects.tpl' arItems=$arItems[i].subcategories selID=$selID marginLevel=$marginLevel+2}><{/if}>
<{/section}>