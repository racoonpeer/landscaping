<div class="m-sidebar">
  <div class="m-r">
    <div class="m-l">
      <div class="m-m"><span>Каталог</span></div>
    </div>
  </div>
</div>
<div class="clear"></div>
<div class="m-catalog">
  <ul>
<{section name=i loop=$catalogMenu}>
    <li>
      <a class="cmenu<{if $catalogMenu[i].id==$arrPageData.catid}> active<{/if}>" href="<{include file='core/href.tpl' arCategory=$catalogMenu[i]}>" title="<{$catalogMenu[i].title}>"><{$catalogMenu[i].title}></a>
<{*if $catalogMenu[i].opened==1 && !empty($catalogMenu[i].subcategories)*}>
<{if !empty($catalogMenu[i].subcategories)}>
        <ul class="m2">
<{section name=j loop=$catalogMenu[i].subcategories}>
          <li><a class="cmenu submenu<{if $catalogMenu[i].subcategories[j].id==$arrPageData.catid}> active<{/if}>" href="<{include file='core/href.tpl' arCategory=$catalogMenu[i].subcategories[j]}>" title="<{$catalogMenu[i].subcategories[j].title}>"><{$catalogMenu[i].subcategories[j].title}></a></li>
<{/section}>
        </ul>
<{/if}>
    </li>
<{/section}>
  </ul>
</div>