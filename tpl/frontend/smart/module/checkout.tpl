<div class="left" style="width:800px;">
    <div class="basket" id="basketLayout">
        <{include file="ajax/basket.tpl"}>
    </div>
</div>

<{if $Basket->getTotalAmount()>0}>
    <div class="right">
        <form class="orderForm ajaxForm" action="" id="orderForm" method="POST" onsubmit="return formCheck(this);">
            <span class="hint"><{if isset($arrPageData.errors.firstname)}><{$arrPageData.errors.firstname}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.firstname)}>error<{/if}>" 
                   name="firstname" value="<{if isset($item.firstname)}><{$item.firstname}><{/if}>" 
                   placeholder="Имя"/>
            <br/>
            <span class="hint"><{if isset($arrPageData.errors.surname)}><{$arrPageData.errors.surname}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.surname)}>error<{/if}>" 
                   name="surname" value="<{if isset($item.surname)}><{$item.surname}><{/if}>" 
                   placeholder="Фамилия"/>
            <br/>
            <span class="hint"><{if isset($arrPageData.errors.phone)}><{$arrPageData.errors.phone}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.phone)}>error<{/if}>" 
                   name="phone" value="<{if isset($item.phone)}><{$item.phone}><{/if}>" 
                   placeholder="+380 __ ___ __ __" id="phone"/>
            <br/>
            <span class="hint"><{if isset($arrPageData.errors.city)}><{$arrPageData.errors.city}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.city)}>error<{/if}>" 
                   name="city" value="<{if isset($item.city)}><{$item.city}><{/if}>" 
                   placeholder="Город"/>
            <br/>
            <span class="hint"><{if isset($arrPageData.errors.address)}><{$arrPageData.errors.address}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.address)}>error<{/if}>" 
                   name="address" value="<{if isset($item.address)}><{$item.address}><{/if}>" 
                   placeholder="Адрес доставки"/>
            <br/>
            <span class="hint"><{if isset($arrPageData.errors.email)}><{$arrPageData.errors.email}><{/if}></span>
            <input type="text" class="required <{if isset($arrPageData.errors.email)}>error<{/if}>" 
                   name="email"  value="<{if isset($item.email)}><{$item.email}><{/if}>" 
                   placeholder="Эл. почта для получения копии заказа" id="email"/>
            <br/>
            <select name="shipping">
<{section name=i loop=$arrPageData.arShipping}>
                <option value="<{$arrPageData.arShipping[i].id}>" <{if isset($item.shipping) AND $arrPageData.arShipping[i].id==$item.shipping}>selected<{/if}>><{$arrPageData.arShipping[i].title}></option>
<{/section}>
            </select>
            <br/>
            <select name="payment">
<{section name=i loop=$arrPageData.arPayment}>
                <option value="<{$arrPageData.arPayment[i].id}>" <{if isset($item.payment) AND $arrPageData.arPayment[i].id==$item.payment}>selected<{/if}>><{$arrPageData.arPayment[i].title}></option>
<{/section}>
            </select>
            <br/>
            <textarea name="descr" placeholder="Комментарий"></textarea>
            <br/>
            <button type="submit" class="btn btn-big btn-red">ОТПРАВИТЬ ЗАКАЗ</button>
        </form>
    </div>
<{/if}>

<script type="text/javascript">
    $(document).ready(function(){
    
        $(":input:text").blur(function() {
            if($(this).hasClass('required')) checkField($(this));
        });  
        $(":input:text").focusin(function() {
            if(!$(this).parent().hasClass('focus')) {
                $(this).parent().addClass('focus');
            }
        });
        
        <{if isset($arrPageData.errors)}>
            <{foreach item=item key=field from=$arrPageData.errors}>
                    checkField($('#orderForm').find('input[name="<{$field}>"]'));
            <{/foreach}>
        <{/if}>
    });
      
    function checkField(input){
        var regExpEmail = new RegExp("^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,4})$");
   //    var regExpPhone = new RegExp("^\+\d{2}\s\d{3}\d{3}-\d{2}-\d{2}$");
        var errors = 0;
        
        if ( 
            $(input).val().length==0 ||
            ($(input).attr('name') == 'email' && $(input).val().match(regExpEmail) == null)  
        ) {

             errors++;
            $(input).removeClass('valid');
            $(input).addClass('error');
        //    if( $(input).parent().find('.tip').length == 0)
        //        $(input).parent().append('<span class="tip error">-</span>');
        } else {
            $(input).removeClass('error');
            $(input).addClass('valid');
       //     if($(input).parent().find('.error:not(.tip)').length == 0)
       //         $(input).parent().find('.tip').remove();
        }
        return errors;
    }

    function formCheck(form){
        var errors = 0;

        $.each($(form).find('.required'), function(i, input) {
            errors += checkField($(input));
        });

        if(errors==0) return true;
        return false;
    }
</script>