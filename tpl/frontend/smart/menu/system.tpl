<{if !empty($systemMenu)}>
    <ul>
<{section name=i loop=$systemMenu}>
        <li<{if $systemMenu[i].id==$arrPageData.catid}> class="active"<{/if}>><a href="<{include file='core/href.tpl' arCategory=$systemMenu[i]}>"><{$systemMenu[i].title}></a></li>
<{if $systemMenu}>
        <ul class="sublevel">
<{section name=isub loop=$systemMenu[i].subcategories}>
            <li<{if $systemMenu[i].subcategories[isub].id==$arrPageData.catid}> class="active"<{/if}><{if $smarty.section.isub.last}> class="sub_last"<{/if}>><a href="<{include file='core/href.tpl' arCategory=$systemMenu[i].subcategories[isub]}>"><{$systemMenu[i].subcategories[isub].title}></a></li>
<{/section}>
        </ul>
<{/if}>
<{/section}>
    </ul>
<{/if}>
