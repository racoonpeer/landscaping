<{*include file='menu/bottom.tpl'*}>
<ul class="footer-nav">
<{section name=i loop=$arItems}>
    <li>
        <a href="<{include file='core/href.tpl' arCategory=$arItems[i]}>"><{$arItems[i].title}></a>
    </li>
<{/section}>
</ul>