<{if !empty($rightMenu)}>
                            <div class="block block-270">
                                <div class="bt-outer"><div class="bt-inner">
                                    <div class="rel"><div class="t-text"><span><{$arrPageData.rightMenuTitle}></span></div></div>
                                </div></div>
                                <div class="bc-outer"><div class="bc-inner rmenu-block">
<{section name=i loop=$rightMenu}>
<{if ($rightMenu[i].id!=$arrModules.users.id) || ($objUserInfo->logined && in_array($objUserInfo->type, array('Administrator', 'User')))}>     
                                    <a class="menu rmenu<{if $rightMenu[i].opened}> active<{/if}>" href="<{include file='core/href.tpl' arCategory=$rightMenu[i]}>" title="<{$rightMenu[i].title}>"><{$rightMenu[i].title}></a>
<{/if}>
<{/section}>
                                </div></div>
                                <div class="bb-outer"><div class="bb-inner"></div></div>
                            </div>
<{/if}>

