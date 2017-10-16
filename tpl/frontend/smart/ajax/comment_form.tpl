<form id="commentForm" method="POST" action="">
    <input type="hidden" name="comment[cid]" value="<{if isset($item.comment.cid)}><{$item.comment.cid}><{else}>0<{/if}>" />
    <textarea name="comment[descr]" cols="50" rows="4" placeholder="ваш комментарий"><{if isset($item.comment.descr)}><{$item.comment.descr}><{/if}></textarea>
    <br />
<{if $objUserInfo->logined}>
    Разместить комментарий от имени: <strong><{$objUserInfo->firstname|cat:" "|cat:$objUserInfo->surname}></strong> 
    <img src="<{$smarty.const.UPLOAD_URL_DIR|cat:'users/small_'|cat:$objUserInfo->image}>" alt="" align="top" height="40" />
<{/if}>    
    <input name="comment[title]" value="<{if $objUserInfo->logined}><{$objUserInfo->firstname|cat:" "|cat:$objUserInfo->surname}><{/if}>" type="hidden"/>
    <br />
<{if $objUserInfo->logined}>
        <input name="comment[uid]" value="<{$objUserInfo->id}>" type="hidden"/>
        <button type="submit">Разместить комментарий</button>
<{else}>
    Войти на сайт с помощью: 
    <a href="http://www.facebook.com/dialog/oauth/?client_id=<{$OAuth->fb_appID}>&redirect_uri=<{$OAuth->getFBurl()|cat:"&option=token"|urlencode}>&response_type=token&scope=email,user_location" onclick="OAuth.dialog(this, 'fb'); return false;" target="_self">Facebook</a> 
    или 
    <a href="https://oauth.vk.com/authorize?client_id=<{$OAuth->vk_appID}>&redirect_uri=<{$OAuth->getVKurl()|cat:"&option=token"|urlencode}>&response_type=token&scope=offline" onclick="OAuth.dialog(this, 'vk'); return false;" target="_self">VKontakte</a>
<{/if}>
</form>
