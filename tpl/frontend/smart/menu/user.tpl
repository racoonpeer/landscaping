<{section name=i loop=$userMenu}>
    <a class="underline" href="<{include file='core/href.tpl' arCategory=$userMenu[i]}>" 
       title="<{$userMenu[i].title}>">
        <{$userMenu[i].title}>
    </a>
    <{if !$smarty.section.i.last}> | <{/if}>
<{/section}>

