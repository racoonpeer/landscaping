<div class="main-box footer-box">
    <div class="midlle-container">
        <footer>
            <div class="left-section">
                <{include file='menu/bottom.tpl' arItems=$bottomMenu}>
                <p class="copyrighted">
                    <{$objSettingsInfo->copyright}>
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    &nbsp;
                    <{include file="core/weblife.tpl"}>
                </p>
            </div>
            <div class="midlle-section">
                <div class="soc">
                    <a href="//facebook.com/landscaping.kiev.ua" class="fb" target="_blank"></a>
                    <a href="//instagram.com/amazing_gardening_kiev" class="inst" target="_blank"></a>
                </div>
            </div>
            <div class="right-section"><{$objSettingsInfo->ownerAddress|unScreenData}></div>
        </footer>
    </div>
</div>
<button class="scroll-top"></button>