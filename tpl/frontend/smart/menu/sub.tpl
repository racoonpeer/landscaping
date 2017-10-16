<{if !empty($arItems)}>
<div class="main-box menu">
    <div class="midlle-container">
        <ul class="page-menu">
<{section name=i loop=$arItems}>
            <li class="<{if $arItems[i].id==$arrPageData.catid}>active<{/if}>">
                <a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>"><{$arItems[i].title}></a>
            </li>
<{/section}>
        </ul>
    </div>
</div>
<{/if}>