<{* REQUIRE VARS: itemID=int dependID=int $arrChildrens=array() *}>
                    <optgroup label="">
<{section name=i loop=$arrChildrens}>
                    <option value="<{$arrChildrens[i].id}>"<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.pid==$arrChildrens[i].id)}>  selected<{/if}><{if $arrChildrens[i].id==$itemID}> disabled<{/if}>><{$arrChildrens[i].margin}><{$arrChildrens[i].title}> &nbsp; ( <{if $arrChildrens[i].active==0}><{$smarty.const.HEAD_INACTIVE}>, <{/if}><{$arrChildrens[i].menutitle}> ) &nbsp; </option>
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/depends_tree_childrens.tpl' itemID=$itemID dependID=$dependID arrChildrens=$arrChildrens[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
                    </optgroup>