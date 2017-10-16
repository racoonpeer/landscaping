<{if !empty($items)}>
    <div class="dropDown">
        <table>
            <{section name=i loop=$items}>
                <tr <{if $smarty.section.i.first}>class="first"<{/if}>>
                    <td class="image">
                        <a href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                            <img style="height:40px;" src="<{$items[i].small_image}>" alt="<{$items[i].title}>" />
                        </a>
                    </td>
                    <td>
                        <a class="title" href="<{include file='core/href_item.tpl' arCategory=$items[i].arCategory arItem=$items[i]}>">
                            <{$items[i].title}>
                        </a>
                    </td>
                    <{if $items[i].new_price}>
                        <td class="price new">
                            <strong><{$items[i].new_price|number_format:0:'.':' '}></strong> <small>грн</small><br/>
                            <span class="old">
                                <strong><{$items[i].price|number_format:0:'.':' '}></strong> <small>грн</small>
                            </span>
                        </td>
                    <{else}>
                        <td class="price">
                            <strong><{$items[i].price|number_format:0:'.':' '}></strong> <small>грн</small>
                        </td>
                    <{/if}>
                </tr>
            <{/section}>
        </table>
        <a class="showAll" href="<{include file='core/href.tpl' arCategory=$arrModules.search params='stext='|cat:$searchtext}>" class="results">Все результаты поиска</a>
    </div>
<{/if}>