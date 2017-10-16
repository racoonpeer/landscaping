<{if !empty($arrPageData.selectedFilters)}>
    <div class="filter selected">
        <div class="title"><{$smarty.const.SELECTED_FILTERS}>:</div>

        <{foreach name=i from=$arrPageData.filters key=filterID item=filter}>
            <{if array_key_exists($filterID, $arrPageData.selectedFilters)}>
                <div class="filter <{if $smarty.foreach.i.iteration}>last<{/if}>">
                <{if !empty($filter.children)}>
                    <b><{$filter.title}></b>

                    <{*brand filter type*}>
                    <{if $filter.type=='brand' OR $filter.type=='category'}>
                        <{foreach name=j from=$filter.children key=arKey item=arItem}>
                            <{if $arItem.selected}>
                                <div><a data-name="filters[<{$filterID}>][<{$arKey}>]" data-value="<{$arItem.id}>" href="<{$arItem.url}>" onclick="return true;updateFiltersList(this, 0, '<{$filterID}>', '<{$arKey}>');"><span><{$arItem.title}></span></a></div><br/>
                            <{/if}>
                        <{/foreach}>

                    <{*fixed price filter type*}>
                    <{elseif $filter.type=='price'}>
                        <{if $filter.children.selected.min && $filter.children.selected.max}>
                            <div class="filter">
                                <a href="<{$filter.children.selected.url}>" onclick="return true;updateFiltersList(this, 0, '<{$filterID}>');">от <{$filter.children.selected.min}> до <{$filter.children.selected.max}> грн</a>
                            </div>
                        <{/if}>

                    <{*range filter type*}>
                    <{elseif $filter.type=='range'}>
                        <{foreach name=j from=$filter.children key=arKey item=arItem}>
                            <{if $arItem.selected}>
                                <div><a data-name="filters[<{$filterID}>][<{$arKey}>]" data-value="<{$arItem.alias}>" href="<{$arItem.url}>" onclick="return true;updateFiltersList(this, 0, '<{$filterID}>', '<{$arKey}>');"><span><{$arItem.title}></span></a></div><br/>
                            <{/if}>
                        <{/foreach}>

                    <{*simple attribute filter type*}>
                    <{else}>
                        <{foreach name=j from=$filter.children key=arKey item=arItem}>
                            <{if $arItem.selected}>
                                <div><a data-name="filters[<{$filterID}>][<{$arKey}>]" data-value="<{$arItem.alias}>" href="<{$arItem.url}>" onclick="return true;updateFiltersList(this, 0, '<{$filterID}>', '<{$arKey}>');"><span><{$arItem.value}></span></a></div><br/>
                            <{/if}>
                        <{/foreach}>
                    <{/if}>
                <{/if}>
                </div>
            <{/if}>
        <{/foreach}>

        <div><a class="all" href="<{$UrlWL->copy()->resetPage()->resetFilters()->buildUrl()}>" onclick="return true;updateFiltersList(this, 1);"><span><{$smarty.const.REMOVE_ALL_FILTERS}></span></a></div><br/>
    </div>
<{/if}>