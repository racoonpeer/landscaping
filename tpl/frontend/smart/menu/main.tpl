<{* REQUIRE VARS: $arItems=array(), $marginLevel=int <{include file='menu/main.tpl' arItems=$mainMenu marginLevel=0}>  *}>
<{if !$marginLevel}>
<nav class="main-nav">
<{/if}>
    <ul>
<{section name=i loop=$arItems}>
        <li class="<{if $arItems[i].opened}>active<{/if}>">
            <a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>"><{$arItems[i].title}></a>
        </li>
<{/section}>
    </ul>
<{if !$marginLevel}>
</nav>
<{/if}>