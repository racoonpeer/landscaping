<div class="homeslider">
    <div class="swiper-wrapper">
<{foreach from=$items item=item}>
        <div class="swiper-slide"  style="background-image: url('<{$item.image}>');">
<{if !empty($item.url)}>
            <a href="<{$item.url}>" class="trigger"></a>
<{/if}>
            <div class='line'><{$item.title}></div>
        </div>
<{/foreach}>
    </div>
    <div class="swiper-pagination"></div>
</div>