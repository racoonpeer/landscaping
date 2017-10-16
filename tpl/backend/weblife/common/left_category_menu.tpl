<{if !empty($categoryTree)}>
<div id="left_block">
   <ul class="filetree category_tree">
       <li>
           <a href="<{$arrPageData.admin_url}>">
               &nbsp;<img src="/images/admin/treeview/folder.png" />&nbsp;<{$smarty.const.HEAD_ROOT_LEVEL}>
           </a>
<{if !empty($categoryTree)}>
            <ul>
<{section name=i loop=$categoryTree}>        
            <li class="<{if $arrPageData.pid==$categoryTree[i].id}> active collapsable<{/if}>">
                <{if !$categoryTree[i].module || (isset($arrPageData.allowedSubPageModules) && in_array($categoryTree[i].module, $arrPageData.allowedSubPageModules))}>
                    <a href="<{$arrPageData.admin_url|cat:"&pid="|cat:$categoryTree[i].id}>" class="<{if $arrPageData.pid==$categoryTree[i].id}>selected<{/if}>">
                        <{if $arrPageData.pid==$categoryTree[i].id}>
                            &nbsp;<img src="/images/admin/treeview/folder.png" />  &nbsp;
                        <{else}>
                            &nbsp;<img src="/images/admin/treeview/folder-closed.png" />  &nbsp;
                        <{/if}>
                       <{$categoryTree[i].title}> 
                       <{if $categoryTree[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
                    </a>
                <{else}>
                    <span class="<{if $arrPageData.pid==$categoryTree[i].id}>selected<{/if}>">
                        <{if $arrPageData.pid==$categoryTree[i].id}>
                            &nbsp;<img src="/images/admin/treeview/folder.png" />  &nbsp;
                        <{else}>
                            &nbsp;<img src="/images/admin/treeview/folder-closed.png" />  &nbsp;
                        <{/if}>
                        <{$categoryTree[i].title}> 
                        <{if $categoryTree[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
                    </span>
                <{/if}>
                &nbsp;<a href="<{$arrPageData.admin_url|cat:"&task=editItem&itemID="|cat:$categoryTree[i].id}>">
                    <img src="/images/operation/edit.png" height="10"/>
                </a>

<{if !empty($categoryTree[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_category_childrens.tpl' dependID=$categoryTree[i].id arrChildrens=$categoryTree[i].childrens}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
            </li>
<{/section}>
        </ul> 
<{/if}>
    </ul>
</div>
<{/if}>