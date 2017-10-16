<{if !empty($leftMenu)}>
                            <div class="m-sidebar">
                                <div class="m-r">
                                    <div class="m-l">
                                        <div class="m-m"><span>Услуги</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="clear"></div>
                            <div class="m-catalog m-catalog-service">
                                <ul>
<{section name=i loop=$leftMenu}>
                                    <li>
                                        <div class="ms<{$smarty.section.i.iteration}>-sf">
                                            <a class="ms<{$smarty.section.i.iteration}>-slink" href="<{include file='core/href.tpl' arCategory=$leftMenu[i]}>">
                                                <img alt="" src="<{$arrPageData.images_dir}>service<{$smarty.section.i.iteration}>_header.png" />
                                            </a>
                                            <div class="ms<{$smarty.section.i.iteration}>-sh">
<{if $leftMenu[i].subcategories}>
                                                <ul class="m2 m2-s<{$smarty.section.i.iteration}>">
<{section name=j loop=$leftMenu[i].subcategories}>
                                                    <li><a href="<{include file='core/href.tpl' arCategory=$leftMenu[i].subcategories[j]}>"><{$leftMenu[i].subcategories[j].title}></a></li>
<{/section}>
                                                </ul>
<{/if}>
                                            </div>
                                        </div>
                                    </li>
<{/section}>
                                </ul>
                            </div>
<{else}>
<{include file='menu/catalog.tpl'}>
<{/if}>


