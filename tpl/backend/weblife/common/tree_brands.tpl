<{* REQUIRE VARS: dependID=int $arrChildrens=array() *}>
<{section name=i loop=$arrChildrens}>
            <option value="<{$arrChildrens[i].id}>"<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.cid==$arrChildrens[i].id)}>  selected<{/if}>><{$arrChildrens[i].margin}><{$arrChildrens[i].title}> &nbsp; <{if $arrChildrens[i].active==0}>( неактивен ) &nbsp; <{/if}></option>
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_brands.tpl' dependID=$dependID arrChildrens=$arrChildrens[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
<{/section}>

