<{if !empty($categoryTree)}>
    <{if !isset($admin_url)}>
        <{assign var='admin_url' value=$arrPageData.admin_url}>
    <{/if}>
<div id="left_block">
   <ul class="filetree category_tree">
       <li>
           <a href="<{$admin_url}>">
               &nbsp;<img src="/images/admin/treeview/folder.png" /> &nbsp;
               <{if $arrPageData.module=='catalog'}><{$smarty.const.CATALOG}>
               <{else}><{$smarty.const.HEAD_ROOT_LEVEL}><{/if}>
           </a>
<{if !empty($categoryTree)}>
            <ul>
<{section name=i loop=$categoryTree}>        
                <li class="<{if $arrPageData.cid==$categoryTree[i].id}> active collapsable<{/if}>">
                    <a href="<{$admin_url|cat:"&cid="|cat:$categoryTree[i].id}>" class="<{if $arrPageData.cid==$categoryTree[i].id}>selected<{/if}>">
                      &nbsp; <img src="/images/admin/treeview/folder-closed.png" />  &nbsp;
                        <{$categoryTree[i].title}> 
                       [<{count($categoryTree[i].items)}>] 
                       <{if $categoryTree[i].active==0}>(<{$smarty.const.HEAD_INACTIVE}>)<{/if}>
                    </a>
<{if !empty($categoryTree[i].items)}>
                    <ul>
<{section name=j loop=$categoryTree[i].items}>
                        <li class="<{if $arrPageData.cid==$categoryTree[i].id}> active collapsable<{/if}>">
                           &nbsp; <a href="<{$admin_url|cat:"&task=editItem&itemID="|cat:$categoryTree[i].items[j].id}>" class="<{if $arrPageData.cid==$categoryTree[i].items[j].id}>selected<{/if}>">
                                <{$categoryTree[i].items[j].title}> 
                            </a>
                        </li>
<{/section}>
                    </ul>
<{/if}>

<{if !empty($categoryTree[i].childrens)}>
<!-- ++++++++++ Start Tree Childrens +++++++++++++++++++++++++++++++++++++++ -->
<{include file='common/tree_childrens.tpl' dependID=$dependID arrChildrens=$categoryTree[i].childrens islist=$islist}>
<!-- ++++++++++ End Tree Childrens +++++++++++++++++++++++++++++++++++++++++ -->
<{/if}>
                </li>
<{/section}>
            </ul> 
<{/if}>
        </li>
    </ul>
</div>
<{/if}>
