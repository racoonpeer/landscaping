<{* REQUIRE VARS: $arrBreadCrumb=array()*}>
<{section name=i loop=$arrBreadCrumb}>
    <{if !$smarty.section.i.last}>
        <{$arrPageData.path_arrow}>
        <{if $arrPageData.module=='main'}>
            <a href="<{$arrPageData.admin_url|cat:'&pid='|cat:$arrBreadCrumb[i].id|cat:$arrPageData.filter_url}>" class="bc_path"><{$arrBreadCrumb[i].title}></a>
        <{else}>
            <a href="<{$arrPageData.admin_url|cat:'&cid='|cat:$arrBreadCrumb[i].id|cat:$arrPageData.filter_url}>" class="bc_path"><{$arrBreadCrumb[i].title}></a>  
        <{/if}>
    <{/if}>
<{/section}>

