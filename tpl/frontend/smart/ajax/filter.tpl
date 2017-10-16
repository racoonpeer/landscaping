<section class="filters">
    <ul>
<{foreach name=i from=$arrPageData.filters key=filterID item=filter}>
<{if !empty($filter.children)}>
        <li id="filter_<{$filterID }>">
            <strong><{$filter.title}></strong>
            <ul>
<{*brand filter type*}>
<{if $filter.type=='brand' OR $filter.type=='category'}>
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='id' title='title' item=$arItem}>
<{/foreach}>

<{*fixed price filter type*}>
<{elseif $filter.type=='price'}>
                <div class="price_slider" data-url="<{$filter.children.url}>" data-masks="<{$filter.children.masks|escape}>" data-selected="<{if $filter.children.selected.min || $filter.children.selected.max}>1<{else}>0<{/if}>">
                    <input class="price_min_<{$filterID}>" placeholder="min" name="filters[<{$filterID}>][min]" type="text"
                           value="<{if $filter.children.selected.min}><{$filter.children.selected.min|round}><{else}><{$filter.children.min|round}><{/if}>" size="5" /> &mdash;
                    <input class="price_max_<{$filterID}>" placeholder="max" name="filters[<{$filterID}>][max]" type="text"
                           value="<{if $filter.children.selected.max}><{$filter.children.selected.max|round}><{else}><{$filter.children.max|round}><{/if}>" size="5" /> <span>грн</span>
                    <input type="button" value="OK" onclick="$('.price_slider').attr('data-selected',1);location.href=createPriceUrl(this); return false; updateFiltersList(this);"/>
                    <div class="divider"></div>
                    <div class="slider_<{$filterID}> range_slider"></div>
                </div>
                <script type="text/javascript">
                    function createPriceUrl(obj){
                        var parent = $(obj).parent();
                        var masks = $(parent).data('masks');
                        var url = decodeURIComponent($(parent).data('url'));
                        var min = $('.price_min_<{$filterID}>', parent).val();
                        var max = $('.price_max_<{$filterID}>', parent).val();
                        url = url.replace(masks['<{UrlFiltersRange::KEY_MIN}>'], min);
                        if((max*1) == 0){
                            url = url.replace(masks['<{UrlFiltersRange::KEY_SEP_MAX}>'], '');
                        }
                        url = url.replace(masks['<{UrlFiltersRange::KEY_MAX}>'], max);
                        return url;
                    }
                    $('.slider_<{$filterID}>').slider({
                        values:[
                            <{if $filter.children.selected.min}><{$filter.children.selected.min|round}><{else}><{$filter.children.min|round}><{/if}>, 
                            <{if $filter.children.selected.max}><{$filter.children.selected.max|round}><{else}><{$filter.children.max|round}><{/if}>
                        ],
                        min: <{$filter.children.min|round}>,
                        max: <{$filter.children.max|round}>,
                        range:true,
                        step: 1,
                        slide: function(event, ui ) {
                            $('.price_min_<{$filterID}>').val(ui.values[0]);
                            $('.price_max_<{$filterID}>').val(ui.values[1]);
                        }
                    });
                </script>
<{*range filter type*}>
<{elseif $filter.type=='range'}>   
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='alias' title='title' item=$arItem}> 
<{/foreach}>

<{*simple attribute filter type*}>
<{else}>
<{foreach name=j from=$filter.children key=arKey item=arItem}>
                <{include file="ajax/_filter.tpl" fid=$filterID aid=$arKey value='alias' title='value' item=$arItem}>
<{/foreach}>
<{/if}>
            </ul>
        </li>
<{/if}> 
<{/foreach}>
    </ul>
</section>

