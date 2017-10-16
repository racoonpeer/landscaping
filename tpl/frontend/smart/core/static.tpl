<div class="main-box menu">
    <div class="midlle-container"></div>
</div>
<div class="main-box main-box-content">
    <div class="midlle-container">
        <div class="page-content">
            <h1><{$arCategory.title}></h1>
<{if !empty($arCategory.text)}>
            <{$arCategory.text}>
<{else}>
            <br/><br/><br/>
            <center><{$smarty.const.NO_CONTENT}></center>
            <br/><br/><br/>
<{/if}>
        </div>
    </div>
</div>