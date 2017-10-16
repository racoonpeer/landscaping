<div class="compare">
    <ul>
        <li class="first"><h2><{$arCategory.title}></h2></li>
        <{if !empty($arCats)}> 
            <{section loop=$arCats name=i}>
                <li class="cats<{if $smarty.section.i.first}> active<{/if}>" data-id="<{$arCats[i].id}>" onclick="showTab(this)"><{$arCats[i].title}> <span><{count($arCats[i].items)}></span></li>
            <{/section}>
        <{/if}>
    </ul>

    <{if !empty($arCats)}> 
    <{section loop=$arCats name=i}>     
            <table class="item tab_<{$arCats[i].id}><{if !$smarty.section.i.first}> hidden<{/if}>">
                <tr>
                    <td width="225" align="center">
                        <ul>
    <{if !empty($arCats[i].items) AND count($arCats[i].items) > 1}>
                            <li>
    <{if $showDiff}>
                                <a href="<{$arrPageData.current_url}>"><{$smarty.const.LABLE_SHOW_ALL}></a>
    <{else}>
                                <a href="<{$arrPageData.current_url|cat:'&show=different'}>"><{$smarty.const.LABLE_SHOW_DIFFERENT}></a>
    <{/if}>
                            </li>
    <{/if}>
                        </ul>
                    </td>
    <{section name=p loop=$arCats[i].items max=10}>
                    <td width="165" align="center">
                        <{include file='core/compare_product.tpl' item=$arCats[i].items[p]}>
                    </td>
    <{/section}>
                </tr>
    <{if !empty($arCats[i].attrGroups)}>
    <{foreach name=k from=$arCats[i].attrGroups key=gKey item=gVal}> 
    <{if !empty($gVal.attributes)}>
    <{foreach name=a from=$gVal.attributes key=aKey item=aVal}>
                <tr class="<{if ($smarty.foreach.k.iteration + $smarty.foreach.a.iteration) % 2}>odd<{else}>even<{/if}>">
                    <td class="title" align="right" width="225"><{$aVal.title}>:</td>
    <{section name=p loop=$arCats[i].items max=10}>
                    <td align="center" width="165">
                        <{$arCats[i].items[p].attrGroups[$gKey][$aKey]}>
                    </td>
    <{/section}>
                </tr>
    <{/foreach}>
    <{/if}>
    <{/foreach}>
    <{/if}>
            </table>
    <{/section}>
    <{else}>
         <span><b><{$smarty.const.SELECT_PRODUCTS_TO_COMPARE}></b></span>
    <{/if}>
</div>
<script type="text/javascript">
    function showTab(item) {
        if($(item).attr('data-id')>0) {
            
            $('li.cats').each(function(){ 
                $(this).removeClass('active');   
                $(this).find('.arrow').remove();
            });
            $(item).addClass('active');
        
            $('table.item').each(function(){
                if( $(this).hasClass('tab_'+$(item).attr('data-id')))  $(this).show();
                else $(this).hide();
            });
        }
    }
</script>