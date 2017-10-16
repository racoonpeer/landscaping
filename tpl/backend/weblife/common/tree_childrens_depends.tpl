<{* REQUIRE VARS: dependID=int $arrChildrens=array() *}>
        <optgroup label="">
<{section name=i loop=$arrChildrens}>
            <option value="<{$arrChildrens[i].id}>"<{if in_array($arrChildrens[i].id,$dependIDS)}>  selected<{/if}>><{$arrChildrens[i].margin}><{$arrChildrens[i].title}> &nbsp; ( <{if $arrChildrens[i].active==0}><{$smarty.const.HEAD_INACTIVE}>, <{/if}><{$arrChildrens[i].menutitle|lower}> ) &nbsp; </option>
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens_multiselect.tpl' dependIDS=$dependIDS arrChildrens=$arrChildrens[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>
        </optgroup>

