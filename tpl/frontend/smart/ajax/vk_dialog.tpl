<{if $arrPageData.result=="success"}>
<script type="text/javascript">
    Modal.close();
    OAuth.submit();
</script>
<{else}>
<div class="body">
    <h3>Вы вошли как <{if isset($item.firstname) AND isset($item.surname)}><{$item.firstname|cat:" "|cat:$item.surname}><{/if}></h3>
<{if !empty($arrPageData.errors)}>
    <p style="color: red;">
        <{$arrPageData.errors|@implode:"<br>"}>
    </p>
<{/if}>
    <form method="POST" onsubmit="formDialog(this); return false;">
        <h4>Введите свою электронную почту</h4>
        <input type="text" name="email" value="<{if !empty($item.email)}><{$item.email}><{/if}>"/><br/>
        <button type="submit">Отправить</button>
    </form>
    <a class="close">&times;</a>
    <script type="text/javascript">
        function formDialog(form){
            var arData = {};
            var inputs = $(form).find('input');
            $(inputs).each(function(i, input){
                arData[$(input).attr('name')] = $(input).val();
            });
            $.ajax({
                url: '<{$OAuth->getVKurl()|cat:"&option=register"}>',
                type: 'post',
                dataType: 'json',
                data: arData,
                success: function(json){
                    if (json.output) {
                        Modal.replace(json.output);
                    }
                }
            });
            return false;
        }
    </script>
</div>
<{/if}>