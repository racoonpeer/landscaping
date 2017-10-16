<{* REQUIRE VARS: $displayType=string[ajax|postform], showTitle=bool[0|1] ----------------- *}>
<{* Start Currencies Menu. Copy only one below line and uncoment it --- *}>
<{*include file='menu/currencies.tpl' displayType='ajax' showTitle=1*}>

<{if $Currencies->getCountCurrencies()>1}>
<{if $showTitle}>
                    <span class="t"><{$smarty.const.CURRENCY}>:</span>
<{/if}>
<{* Show Ajax Currency Template --------------------------------------- *}>
<{if $displayType=='ajax'}>
        <script type="text/javascript">
            <!--
            function changeCurrency(id){
                $.getJSON( // update Currency
                    "/interactive/ajax.php",
                    {zone: "site", action: "ajaxChangeCurrency", cid: id},
                    function(data, textStatus){
                        if(textStatus=='success'){
                           if(data.result) location.reload();
                           else alert('<{$smarty.const.CURRENCY_CHANGE_ERROR}>');
                        }
                    }
                );
            }
            //-->
        </script>
<{foreach from=$Currencies->getItems() key=cKey item=arCItem name=i}>
        <a href="javascript:void(0);" title="<{$arCItem.title}>"<{if $cKey==$Currencies->getCurrentId()}> class="active" onclick="return false;"<{else}> onclick="changeCurrency(<{$cKey}>);"<{/if}>><span><{$arCItem.code}></span></a>
<{if !$smarty.foreach.i.last}>
        <span class="menu-top-sep"></span>
<{/if}>
<{/foreach}>

<{else}>
<{* Show Post Form Currency Template ---------------------------------- *}>
        <form action="" method="post" name="currencyForm">
            <select name="currency_id" onchange="this.form.submit()">
        <{foreach from=$Currencies->getItems() key=cKey item=arCItem name=i}>
                <option value="<{$cKey}>"<{if $cKey==$Currencies->getCurrentId()}>  selected<{/if}> title="<{$arCItem.title}>"><{$arCItem.name}></option>
        <{/foreach}>
            </select>
        </form>

<{/if}>
<{/if}>

