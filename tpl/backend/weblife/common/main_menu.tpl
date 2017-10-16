<{if !empty($arrPageData.main_menu)}>
    <ul>
    <{section name=i loop=$arrPageData.main_menu}>
        <li class="menu_item<{if $arrPageData.module==$arrPageData.main_menu[i].module || ($arrPageData.main_menu[i].module=='attribute_groups' && $arrPageData.module=='attributes' )}> active<{/if}>">
            <a href="/admin.php?module=<{$arrPageData.main_menu[i].module}>"><{$arrPageData.main_menu[i].title}></a>
        </li>
        <{if $smarty.section.i.iteration%8==0}></ul><ul><{/if}>
    <{/section}>
    </ul>   
<{/if}>
