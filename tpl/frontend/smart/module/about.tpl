<div class="main-box menu">
    <div class="midlle-container"></div>
</div>
<div class="main-box main-box-content">
    <div class="midlle-container about">
        <div class="left-side"><{$arCategory.text}></div>
        <div class="right-side">
            <div class="main-staff">
<{section name=i loop=$items}>
                <div class="staff-box">
                    <div class="foto">
                        <img src="<{$items[i].image}>" alt=""/>
                    </div>
                    <address>
                        <h2><{$items[i].title}></h2>
                        <p class="job"><{$items[i].descr}></p>
                        <span>Email:</span>
                        <span class="greentext"><{$items[i].email}></span>
                    </address>
                </div>
<{/section}>
            </div>
        </div>
    </div>
</div>