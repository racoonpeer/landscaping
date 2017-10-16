<{if !empty($item)}>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2">
                <h1><{$item.title}></h1>
            </td>
        </tr>
        <tr valign="top">
            <td>
                <table width="100%" cellspacing="0" cellpadding="4">
                    <tr valign="top">
                        <td width="150">
                            <img src="<{$item.image}>" alt="<{$item.title}>" width="150" />
                        </td>
                        <td>
                            <{$item.fulldescr}>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="right">
<{section name=i loop=$item.arCategories}>
                            <a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$item params='cid='|cat:$item.arCategories[i].id}>" title="<{$item.arCategories[i].title}>"><{$item.arCategories[i].title}></a>
<{if !$smarty.section.i.last}>
                            <br />
<{/if}>
<{/section}>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="250">
                <{include file='ajax/minicart.tpl'}>
            </td>
        </tr>
    </table>
<{elseif !empty($items)}>
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="2">
                <h1><{$arCategory.title}></h1><br/>
            </td>
        </tr>
        <tr valign="top">
            <td>
                <table width="100%" cellspacing="0" cellpadding="4">
<{section name=i loop=$items}>
                    <tr valign="top">
                        <td width="150">
                            <a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}>" title="<{$items[i].title}>">
                                <img src="<{$items[i].image}>" alt="<{$items[i].title}>" width="150" />
                            </a>
                        </td>
                        <td>
                            <h2><{$items[i].title}></h2>
                            <{$items[i].descr|unScreenData}>
                            <a href="<{include file='core/href_item.tpl' arCategory=$arCategory arItem=$items[i]}>" title="<{$items[i].title}>">Подробнее</a>
                        </td>
                    </tr>
<{/section}>
                </table>
            </td>
            <td width="250">
                <{include file='ajax/minicart.tpl'}>
            </td>
        </tr>
    </table>
<{else}>
    <{include file='core/static.tpl'}>
<{/if}>