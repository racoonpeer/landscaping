<{* REQUIRE VARS: $position=int[1|2|3|4], $maxitems=mixed[int|bool]; <{include file='core/banners.tpl' position=2 maxitems=false}> *}>
<{assign var='arrItems' value=$Banners->getBanners($position, $maxitems)}>

<{if !empty($arrItems) && $arrItems|count>0}>
<{if $position==4}>
        <table cellspacing="0" cellpadding="0" border="0">
            <tr>
<{section name=i loop=$arrItems}>
                <td>
                    <div class="item">
                        <table width="100%" cellspacing="0" cellpadding="0" border="0"><tbody>
                            <tr><td colspan="2"><a class="title" href="<{$arrItems[i].link}>"<{if !empty($arrItems[i].target)}> target="<{$arrItems[i].target}>"<{/if}>><{$arrItems[i].title}></a></td></tr>
                            <tr>
                                <td width="126" class="image-wrapper"><a href="<{$arrItems[i].link}>"<{if !empty($arrItems[i].target)}> target="<{$arrItems[i].target}>"<{/if}>><img src="<{$arrItems[i].image}>" alt="" /></a></td>
                                <td>
                                    <div class="text-wrapper">
                                        <{$arrItems[i].content}>
                                    </div>
                                    <a class="more" href="<{$arrItems[i].link}>"<{if !empty($arrItems[i].target)}> target="<{$arrItems[i].target}>"<{/if}>><{$smarty.const.BUTTON_MORE}></a>
                                </td>
                            </tr>
                        </tbody></table>
                    </div>
                </td>
<{if $arrItems|@count==2 && $smarty.section.i.first}>
                <td><div class="item-sep">&nbsp;</div></td>
<{/if}>
<{/section}>
            </tr>
        </table>

<{else}>
<{section name=i loop=$arrItems}>
        <div class="item">
            <{$arrItems[i].content}>
        </div>
<{/section}>
<{/if}>

<{/if}>

