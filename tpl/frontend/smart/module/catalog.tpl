<{* DISPLAY ITEM FIRST IF NOT EMPTY *}>
<{if !empty($item)}>
        <div>
<{if !empty($item.defaultImage)}>
            <img src="<{$item.defaultImage.big_image}>" alt="" valign="top"/>
<{/if}>
<{if $item.isdiscount}>
            <div>Распродажа</div>
<{else if $item.isnewest}>
            <div>Новинка</div>
<{/if}>
        </div>
        <div>
            <ul>
<{if !empty($item.defaultImage)}>
                <li class="active">
                    <a href="javascript:void(0);" rel="<{$item.defaultImage.big_image}>">
                        <img src="<{$item.defaultImage.middle_image}>" alt="" valign="top"/>
                    </a>
                </li>
<{/if}>
<{if !empty($item.images)}>
    <{section name=i loop=$item.images}>
                <li>
                    <a href="javascript:void(0);" rel="<{$item.images[i].big_image}>">
                        <img src="<{$item.images[i].middle_image}>" alt="" valign="top"/>
                    </a>
                </li>
    <{/section}>
<{/if}>
            </ul>
            <{if $item.commentsCount>0}><{$item.commentsCount}> отзывов<{else}>нет отзывов<{/if}>
        </div>
        <div>
            <{$item.fulldescr}>
        </div>
<{if !empty($item.arKits)}>
        <div>
            <h3>Комплекты:</h3>
            <{include file='core/kit.tpl' item=$item arKits=$item.arKits}>
        </div>
<{/if}>
<{if !empty($item.alsoView)}>
            <div>
                <h3>К <{$item.title}> также подходят</h3>
                <{include file='ajax/products.tpl' items=$item.alsoView}>
            </div>
<{/if}>
            <div class="comments" id="reviews">
                <h3>Отзывы</h3>
                <{include file='ajax/comment_form.tpl' item=$item}>
<{section name=i loop=$item.comments}>
                <div class="list">
                    <ul>
                        <li>
                            <table>
                                <tr>
                                    <td>
                                        <div class="head">
                                            <a href="javascript:void(0);"><{$item.comments[i].title}></a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="date"><{$item.comments[i].created|date_format:"%d.%m.%Y"}></span>
                                        </div>
                                        <div class="body"><{$item.comments[i].descr|unScreenData}></div>
<{if $objUserInfo->logined AND $item.comments[i].uid!=$objUserInfo->id}>
                                        <div class="foot">
                                            <a href="javascript:void(0)" onclick="replyComment(<{$item.comments[i].id}>)">Ответить</a>
                                        </div>
<{/if}>

<{if !empty($item.comments[i].children)}>
                                        <ul>
                                            <li>
                                                <table>
<{section name=j loop=$item.comments[i].children}>
                                                    <tr>
                                                        <td>
                                                            <div class="head">
                                                                <a href="javascript:void(0);"><{$item.comments[i].children[j].title}></a>&nbsp;&nbsp;&nbsp;&nbsp;<span class="date"><{$item.comments[i].created|date_format:"%d.%m.%Y"}></span>
                                                            </div>
                                                            <div class="body"><{$item.comments[i].children[j].descr|unScreenData}></div>
                                                        </td>
                                                    </tr>
<{/section}>
                                                </table>
                                            </li>
                                        </ul>
<{/if}>
                                    </td>
                                </tr>
                            </table>
                        </li>
                    </ul>
                </div>
<{/section}>
            </div>
            <div class="details">
<{include file="core/product_price.tpl"}>
                <{include file="core/buy_button.tpl" list=false}>
<{if !empty($item.options)}>
                <div class="options">
                    <form>
<{foreach from=$item.options key=optionID item=option}>
                        <{include file="core/_option.tpl" list=false}>
<{/foreach}>
                    </form>
                </div>
<{/if}>
            
<{if !empty($item.arOptions)}>
                <div class="option">
    <{section name=i loop=$item.arOptions}>
        <{if $item.arOptions[i].basket}>
                    <label><input type="checkbox" class="add_option" <{if $Basket->isSetKey($item.id) && $Basket->getItemOption($item.id)>0}>checked<{/if}> value="<{$item.arOptions[i].id}>"> <strong><{$item.arOptions[i].title}> +<{$item.arOptions[i].price}></strong> грн</label>
        <{/if}>
    <{/section}>
                </div>
<{/if}>     
            
                <div class="descr">
                    <{$item.descr}>
                </div>
            
                <div id="buyinfo_block" class="buttons">
                    <a href="javascript:void(0);" class="addToCart" <{if !$Basket->isSetKey($item.id)}>onclick="Basket.add(<{$item.id}>, 1, 0, true, {class:'addToCart',target: $(this), link: '<{include file="core/href.tpl" arCategory=$arrModules.checkout}>', text: 'Добавлен в корзину'}, $('.add_option:checked').val());"<{/if}>><{if $Basket->isSetKey($item.id)}>Добавлен в корзину<{else}>Добавить в корзину<{/if}></a>
                    <a href="javascript:void(0);" class="wishList in <{if in_array($item.id, $arrPageData.wishlist)}> disabled<{/if}>" data-alt="<{if !in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" title="<{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}>" data-itemid="<{$item.id}>" onclick="<{if !in_array($item.id, $arrPageData.wishlist)}>WishList.add(this);<{else}>WishList.delete(this);<{/if}>"><span><{if in_array($item.id, $arrPageData.wishlist)}><{$smarty.const.IN_WISHLIST}><{else}><{$smarty.const.ADD_TO_WISHLIST}><{/if}></span></a>
                </div>
            
                <table class="attributes">
<{if !empty($item.arBrand)}>
                    <tr>
                        <td class="name"><span>Производитель</span></td>
                        <td><a href="<{include file='core/href_item.tpl' arCategory=$arrModules.brands arItem=$item.arBrand}>"><{$item.arBrand.title}></a><br/></td>
                    </tr>
<{/if}>

<{if !empty($item.attrGroups)}>
    <{section name=i loop=$item.attrGroups}>
        <{if !empty($item.attrGroups[i].attributes)}>
            <{foreach name=j from=$item.attrGroups[i].attributes  item=arItem}>
                    <tr>
                        <td class="name"><span><{$arItem.title}></span></td>
                        <td><{$arItem.values|@implode:' | '}></td>
                    </tr>
            <{/foreach}>
        <{/if}>
    <{/section}>   
<{/if}>
                </table>

<{if !empty($item.arOptions)}>
                <ul class="options">
<{section name=i loop=$item.arOptions}>
<{if !$item.arOptions[i].basket}>
                    <li><{$item.arOptions[i].title}></li>
<{/if}>
<{/section}>
            </ul>
<{/if}>            
        </div>

<{assign var=watched value=$HTMLHelper->getLastWatched($UrlWL)}>
<{if !empty($watched)}>
        <div class="feed">
            <h3>Просмотренные товары</h3>
            <ul>
    <{section name=i loop=$watched}>
                <li><a href="<{include file='core/href_item.tpl' arCategory=$watched[i].arCategory arItem=$watched[i]}>"><{$watched[i].title}></a></li>
    <{/section}>
            </ul>
        </div>
<{/if}>
    
<{if !empty($item.alsoBuy)}>
        <div class="similar">
            <h3>Вместе с <{$item.title}> также покупают</h3>
            <{include file='ajax/products.tpl' items=$item.alsoBuy}>
        </div>
<{/if}>
        
    <div class="categoryDescr">
        <{$arCategory.text}>
    </div>
    
    <script type="text/javascript">
        function replyComment(id) {
            id = parseInt(id)||0;
            var Form = document.getElementById('commentForm');
            $(Form).find('input[name="comment[cid]"]').val(id);
            $(Form).find('textarea').focus();
        }
    </script>

    
<{* DISPLAY ITEMS LIST IF NOT EMPTY *}>
<{elseif !empty($items) || !empty($arrPageData.filters) || !empty($arrPageData.selectedFilters)}>
    
    <div  class="left" style="width: 195px;">
        <div id="selectedFilters">
            <{include file='ajax/selected_filters.tpl'}>
        </div>
        <div id="filtersForm">
            <{include file='ajax/filter.tpl'}>
        </div>
    </div>
        
    <div class="right" style="width:800px;">
<{if !empty($arrPageData.arSorting)}>
        Сортировка: 
        <select onchange="window.location.assign(this.value);">
<{foreach name=i from=$arrPageData.arSorting key=sortID item=sorting}>
            <option value="<{$sorting.url}>"<{if $sorting.active}> selected<{/if}>><{$sorting.title}></option>
<{/foreach}>
        </select>
<{/if}>
        <div id="products">
            <{include file='ajax/products.tpl' items=$items}>
        </div>
    </div>
    
    <div class="clear"></div>
    <{$arCategory.text}>
    <script type="text/javascript">
        var filterTimeout;
        function updateFiltersList(el, removeAll, filterID, attrID) {
            var Form = document.getElementById('filtersForm');
            var Products = document.getElementById('products');
            var Selected = document.getElementById('selectedFilters');            
            var Params = {cid: parseInt(<{$arCategory.id}>), sort:"<{$arrPageData.sort}>"};
            if (typeof filterID !== "undefined") {
                Params.delFilter = {filterID: filterID};
                if(typeof attrID !== "undefined") {
                    Params.delFilter.attrID = attrID;
                }
            } else if (typeof removeAll !== "undefined"){
                Params.delFilter = {filterAll:1};
            }             
            $.each($(Form).find(':checked'), function (i, input) {
                Params[$(input).attr('name')] = $(input).val();
            });
            if(parseInt($('.price_slider').attr('data-selected')) == 1) {
                $.each($(Form).find('input[type="text"]'), function (i, input) {
                    if($(input).val().length) {
                        Params[$(input).attr('name')] = $(input).val();
                    }
                });
            }
            /*
            if(typeof Params.delFilter != "undefined" || ($(el).is(':checkbox') && !$(el).is(':checked'))) {
                $(".filter_animation").addClass('deleted');
            } else {
                $(".filter_animation").removeClass('deleted');
            }*/
            if(filterTimeout) {
                clearTimeout(filterTimeout);
                $(".filter_animation").fadeOut();
            }
            $(".filter_animation").fadeIn();
            filterTimeout = setTimeout('$(".filter_animation").fadeOut();', 1500);  
            $.ajax({
                url: '/interactive/filter.php',
                type: 'GET',
                dataType: 'json',
                data: Params,
                success: function (json) {
                    if(json.output) {
                        $(Form).html(json.output.filters);
                        $(Products).html(json.output.products);
                        $(Selected).html(json.output.selected);
                        if ($('.filter').length) {
                            $('.show-more').each(function () {
                                $(this).on('click', function () {
                                    var title = $(this).attr('data-text');
                                    var filterID = $(this).attr('data-id');
                                    $(this).attr('data-text', $(this).text());
                                    $(this).text(title);
                                    if (!$(this).hasClass('disabled')) {
                                        $('#' + filterID + ' .hide').show();
                                        $(this).addClass('disabled');
                                    } else {
                                        $('#' + filterID + ' .hide').hide();
                                        $(this).removeClass('disabled');
                                    }
                                });
                            }); 
                        }
                        if (History.enabled) {
                            History.pushState(null, document.title, '<{include file='core/href.tpl' arCategory=$arCategory}>' + json.url);
                        }                        
                    }
                }
            });
        }
    </script>
    
<{* DISPLAY CATEGORY INFO *}>
<{else}>
<{include file='core/static.tpl'}>
<{/if}>