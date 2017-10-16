<!-- ++++++++++++++ Start Quick Search Form Wrapper ++++++++++++++++++++++++ -->
<{*include file='core/search-form.tpl'*}>
<div class="search">
    <form id="searchForm" method="get" action="<{include file='core/href.tpl' arCategory=$arrModules.search}>" name="qSearchForm">
        <input class="input" type="text" id="qSearchFormText"  name="stext" placeholder="Поиск" value="<{if !empty($arrPageData.stext)}><{$arrPageData.stext}><{/if}>"/>
        <button id="qSearchFormSubmit" onclick="if(document.qSearchForm.stext.value!='') $('#searchForm').submit(); else return false;">Искать</button>
    </form>
</div>
<div class="search-example">
    <{$smarty.const.LABEL_EXAMPLE}>: <a href="#" onclick="document.getElementById('qSearchFormText').value='<{$smarty.const.LABEL_SEARCH_EXAMPLE}>'; return false;"><{$smarty.const.LABEL_SEARCH_EXAMPLE}></a>
</div>
<!-- ++++++++++++++ End Quick Search Form Wrapper ++++++++++++++++++++++++++ -->
