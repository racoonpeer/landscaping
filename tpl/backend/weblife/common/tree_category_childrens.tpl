<{* REQUIRE VARS: $arrChildrens=array()*}>

<ul>
<{section name=i loop=$arrChildrens}>
    <li class="<{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.pid==$arrChildrens[i].id)}> active<{/if}>">
        <a href="<{$arrPageData.admin_url|cat:"&pid="|cat:$arrChildrens[i].id}>">
            <{if $dependID==$arrChildrens[i].id OR (empty($dependID) && $arrPageData.pid==$arrChildrens[i].id)}>
                &nbsp;<img src="/images/admin/treeview/folder.png" /> 
            <{else}>
                &nbsp;<img src="/images/admin/treeview/folder-closed.png" /> 
            <{/if}>
            <{$arrChildrens[i].title}> 
            <{if $arrChildrens[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
        </a>
        <a href="<{$arrPageData.admin_url|cat:"&task=editItem"|cat:"&pid="|cat:$arrChildrens[i].pid|cat:"&itemID="|cat:$arrChildrens[i].id}>">
            <img src="/images/operation/edit.png" height="10"/>
        </a>
    
<{if !empty($arrChildrens[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_category_childrens.tpl' arrChildrens=$arrChildrens[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
    </li>
<{/section}>
</ul>