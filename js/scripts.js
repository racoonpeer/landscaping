$(document).ready(function(){ 
    
    // dropdown menu
    $('#myslidemenu > ul.sf-menu').superfish({
        delay:       1000,                            // one second delay on mouseout
        animation:   {
            opacity:'show',
            height:'show'
        },  // fade-in and slide-down animation
        speed:       'fast',                          // faster animation speed
        autoArrows:  true                            // disable generation of arrow mark-up
    }).supersubs({
        minWidth:    10,   // minimum width of submenus in em units
        maxWidth:    30,   // maximum width of submenus in em units
        extraWidth:  1     // extra width can ensure lines don't sometimes turn over
                               // due to slight rounding differences and font-family
    });
    
    // search autocomplete
    $('#qSearchFormText').autocomplete({
        source: function(request, response) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'liveSearch',
                    swhere: 'catalog',
                    stext: request.term
                }, 
                success: function(json) {
                    $('.dropDown').remove();
                    $('#searchForm').after(json.output);
                    $('.dropDown').hover(function(){
                        $(this).addClass('hovered');
                    }, function(){
                        $(this).removeClass('hovered');
                    });
                    $('body').click(function(){
                        if(!$('.dropDown').hasClass('hovered')) {
                            $('.dropDown').remove();
                        }
                    });
                }
            });
        },
        select: function(event, ui) {
        },
        minLength: 2
    });
});

var OAuth = {
    dialog: function(a, soc) {
        var href = a.href||null;
        soc = soc||null;
        if(!empty(href) && !empty(soc)) {
            if(soc=="fb") {
                 window.open(href,'dial','toolbar=0,status=0,width=626,height=436,top=200,left=200');
            } else if(soc=="vk") {
                window.open(href,'dial','toolbar=0,status=0,width=626,height=436,top=200,left=200');
            }
        }
        return false;
    },
    submit: function(){
        var form = $(document).find("#commentForm");
        $.ajax({
            url: "/interactive/ajax.php",
            type: "GET",
            dataType: "json",
            data: {
                zone: "site",
                action: "getCommentForm"
            }, 
            success: function(json){
                if(json.output) {
                    var txt = $(form).find('textarea').val();
                    $(form).replaceWith(json.output);
                    setTimeout(function(){
                        $(document).find("#commentForm").find('textarea').val(txt);
                    }, 30);
                }
            }
        });
    }
}

var Modal = {
    opened: false,
    container: '.modal-container',
    inner: '.modal-container > .modal-inner',
    body: '.modal-container > .modal-inner > .body',
    expand: function(link){
        var _self = this;
        if (typeof(link) != 'undefined' && link.length > 0) {
            $.ajax({
                url: link,
                type: 'GET',
                dataType: 'json',
                success: function(json) {
                    if (json.output) {
                        if (_self.setUp()) {
                            _self.open(json.output);
                        }
                    }
                }
            });
        }
        return false;
    },
    replace: function(data) {
        var _self = this;
        $(_self.inner).html(data);
        _self.adjust();
    },
    open: function(data) {
        var _self = this;
        $(_self.inner).html(data);
        _self.adjust();
        _self.opened = true;
    },
    setUp: function() {
        var _self = this;
        $('body').addClass('noscroll').prepend('<div class="modal-container"><div class="modal-inner"></div></div>');
        _self.bindEvents();
        return true;
    },
    adjust: function() {
        var _self = this;
        var iW = $(_self.inner).width();
        var iH = $(_self.inner).height() + 100;
        var wH = document.documentElement.clientHeight;
        if(wH < iH) {
            $(_self.inner).stop().animate({
                'top'           : 0,
                'margin-left'   : '-' + Math.floor(iW / 2) + 'px',
                'margin-top'    : '-' + 40 + 'px'
            }, 200);
        } else {
            $(_self.inner).stop().animate({
                'top'           : '50%',
                'margin-left'   : '-' + Math.floor(iW / 2) + 'px',
                'margin-top'    : '-' + Math.floor(iH / 2) + 'px'
            }, 200);
        }
        _self.bindEvents();
    },
    bindEvents: function() {
        var _self = this;
        $(_self.inner).hover(function() {
            $(this).addClass('hovered');
        }, function() {
            $(this).removeClass('hovered');
        });
        $(_self.container).click(function() {
            if (!$(_self.inner).hasClass('hovered')) {
                _self.close();
            }
        });

        $(window).bind('resize', function(){
            var wH = document.documentElement.clientHeight;
            var iH = $(_self.inner).height() + 100;
            if(wH < iH) {
                $(_self.inner).css({
                    'top'       : 0,
                    'margin-top': '-' + 40 + 'px'
                });
            } else {
                $(_self.inner).css({
                    'top'       : '50%',
                    'margin-top': '-' + Math.floor(iH / 2) + 'px'
                });
            }
        });
    },
    close: function() {
        var _self = this;
        $('body').removeClass('noscroll');
        $(_self.container).remove();
        _self.opened = false;
    }
}

var Basket = {
    minicart: 'minicart', // Minicart widget ID
    basket: 'basketLayout', // Basket module layout ID
    optionsIndicator: "|",
    optionsSeparator: "/",
    valuesSeparator: "=",
    deleteItem: function(ID, QTY){
        var _self = this;
        ID = !empty(ID)? ID: false;
        QTY = parseInt(QTY)||1;
        if (ID) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'basket',
                    option: 'remove',
                    itemID: ID,
                    qty: QTY
                },
                success: function(json){
                    _self.update();
                    if(json.output && json.output.isEmpty)
                       window.location = '';
                },
                complete: function(){},
                beforeSend: function(){}
            });
        }
    },
    add: function(ID, QTY, setNewQty, list, classParams) {
        var _self = this;
        ID = !empty(ID)? ID :false;
        QTY = parseInt(QTY)||1;
        setNewQty = (typeof setNewQty != 'undefined') ? 1 : 0;
        classParams = classParams||{};
        var optionsInputs = $(classParams.item).find("select[id^='options_" + ID + "'], input[id^='options_" + ID + "']"),
            idKey = ID;
        if (optionsInputs.length) {
            idKey += "|";
            $.each(optionsInputs, function (i, input) {
                if ((input.type=="radio" || input.type=="checkbox") && !input.checked) return;
                var optionID = $(input).data("optionid"),
                    similar  = $(classParams.item).find("input[data-optionid='" + optionID + "']"),
                    arValues = new Array();
                if (similar.length) {
                    $.each(similar, function (j, cb) {
                        if (cb.checked) arValues.push(cb.value);
                    });
                } else {
                    arValues.push(input.value);
                }
                idKey += $(input).data("optionid") + "=" + implode(",", arValues) + "/";
            });
        }
        if (ID) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'basket',
                    option: 'add',
                    itemID: idKey,
                    qty: QTY,
                    setNewQty: setNewQty,
                    list: list
                },
                success: function () {
                    _self.update();
                },
                complete: function () {
                    if (!empty(classParams)) {
                        if (classParams.target) {
                            $(classParams.target).addClass('disabled');
                        }
                        if (classParams.link) {
                            $(classParams.target).attr('href', classParams.link).removeAttr('onclick');
                        }
                        if (classParams.text) {
                            $.each($(classParams.target), function() {
                                $(this).text(classParams.text);
                                $(this).removeAttr('class');
                                if(classParams.class) $(this).addClass(classParams.class);
                                else $(this).addClass('add-to-cart f-right inCart f-right disabled');
                                $(this).attr('href', classParams.link);
                                $(this).removeAttr('onclick');
                            });
                        }
                        if (classParams.related) {
                            $(classParams.related).text(classParams.text);
                            $(classParams.related).attr('href', classParams.link);
                            $(classParams.related).removeAttr('onclick');
                        }
                    }
                },
                beforeSend: function (){}
            });
        }
    },
    update: function(){
        var _self = this;
        $.ajax({
            url: '/interactive/ajax.php',
            type: 'GET',
            dataType: 'json',
            data: {
                zone: 'site',
                action: 'basket',
                option: 'update'
            }, 
            success: function(json) {
                if(json.output) {
                    if(!empty(json.output.basket) && !empty($('#'+_self.basket))) {
                        $('#'+_self.basket).html(json.output.basket);
                    }
                    if(!empty(json.output.minicart) && !empty($('#'+_self.minicart))) {
                        $('#'+_self.minicart).html(json.output.minicart);
                    }
                }
            }
        });
    },
    clear: function(){
        var _self = this;
        $.ajax({
            url: '/interactive/ajax.php',
            type: 'GET',
            dataType: 'json',
            data: {
                zone: 'site',
                action: 'basket',
                option: 'clear'
            }, 
            success: function() {
                _self.update();
            }
        });
    },
    changeOptions: function (ID, item, list, module) {
        var optionsInputs = $(item).find("select[id^='options_" + ID + "'], input[id^='options_" + ID + "']"),
            idKey = ID;
        if (optionsInputs.length) {
            idKey += "|";
            $.each(optionsInputs, function (i, input) {
                if ((input.type=="radio" || input.type=="checkbox") && !input.checked) return;
                var optionID = $(input).data("optionid"),
                    similar  = $(item).find("input[data-optionid='" + optionID + "']"),
                    arValues = new Array();
                if (similar.length) {
                    $.each(similar, function (j, cb) {
                        if (cb.checked) arValues.push(cb.value);
                    });
                } else {
                    arValues.push(input.value);
                }
                idKey += $(input).data("optionid") + "=" + implode(",", arValues) + "/";
            });
        }
        $.ajax({
            url: '/interactive/ajax.php',
            type: 'GET',
            dataType: 'json',
            data: {
                zone: 'site',
                action: 'AjaxChangeProductOptions',
                idKey: idKey,
                list: list,
                module: module
            },
            success: function (json) {
                if (json.button) {
                    $(item).find(".add-to-cart").replaceWith(json.button);
                }
                if (json.price) {
                    $(item).find(".price").replaceWith(json.price);
                }
            }
        });
    },
    replaceProductItem: function (ID, item) {
        ID  = ID||false;
        var CID = parseInt($(item).data("cid"))||false;
        if (ID && CID) {
            $.ajax({
                url     : "/interactive/ajax.php",
                type    : "GET",
                dataType: "json",
                data    : {
                    zone    : "site",
                    action  : "AjaxGetProductItem",
                    itemID  : ID,
                    cid     : CID,
                    list    : 1
                },
                success : function (json) {
                    if (json.output) {
                        $(item).replaceWith(json.output);
                    }
                }
            });
        }
    },
    replaceProductInfo: function (ID, item) {
        ID  = ID||false;
        var CID = parseInt($(item).data("cid"))||false;
        if (ID && CID) {
            $.ajax({
                url     : "/interactive/ajax.php",
                type    : "GET",
                dataType: "json",
                data    : {
                    zone    : "site",
                    action  : "AjaxGetProductItem",
                    itemID  : ID,
                    cid     : CID,
                    list    : 0
                },
                success : function (json) {
                    if (json.output) {
                        $(item).replaceWith(json.output);
                    }
                }
            });
        }
    }
};

var Compare = {
    add: function(link) {
        var ID = parseInt($(link).data('itemid'))||false;
        var Text = $(link).text();
        var altText = $(link).data('alt')||$(link).text();
        if(ID && !$(link).hasClass('disabled')) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'Compare',
                    option: 'add',
                    itemID: ID
                }, 
                success: function(json) {
                    if(json.items) {
                        if(in_array(ID, json.items)) {
                            $(link).addClass('disabled');
                            $(link).data('alt', Text);
                            $(link).attr('title', altText);
                            $(link).text(altText);
                            $(link).attr('onclick', 'Compare.delete(this);');
                        }
                        var items =[];
                        $.each(json.items, function(key, val) {
                            items.push(val);
                        });

                        $('#compare_list').find('a').attr('href', $('#compare_list').find('a').attr('data-href')+'?compare='+items.join(','));
                        $('#compare_list').find('a').removeAttr('class');
                        $('#compare_list').find('span').text(items.length);
                    }
                }
            });
        }
    },
    delete: function(link){
        var ID = parseInt($(link).data('itemid'))||false;
        var Text = $(link).text();
        var altText = $(link).data('alt')||$(link).text();
        if(ID && $(link).hasClass('disabled')) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'Compare',
                    option: 'delete',
                    itemID: ID
                }, 
                success: function(json) {
                    if(!empty(json.items)) {                    
                        var items =[];
                        $.each(json.items, function(key, val) {
                            items.push(val);
                        });
                        
                        $('#compare_list').find('a').attr('href', $('#compare_list').find('a').attr('data-href')+'?compare='+items.join(','));
                        $('#compare_list').find('a').removeAttr('class');
                        $('#compare_list').find('span').text(items.length);
                    } else {
                        $('#compare_list').find('span').text("0");
                        $('#compare_list').find('a').addClass('disabled');
                        $('#compare_list').find('a').removeAttr('href');
                    }
                    $(link).data('alt', Text);
                    $(link).attr({
                        'title' : altText,
                        'onclick' : 'Compare.add(this);'
                    });
                    $(link).text(altText);
                    $(link).removeClass('disabled');
                }
            });
        }
    }
};
var WishList = {
    add: function(link) {
        var ID = parseInt($(link).data('itemid'))||false;
        var Text = $(link).text();
        var altText = $(link).data('alt')||$(link).text();
        if(ID && !$(link).hasClass('disabled')) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'WishList',
                    option: 'add',
                    itemID: ID
                }, 
                success: function(json) {
                    if(json.items) {
                        if(in_array(ID, json.items)) {
                            $(link).addClass('disabled');
                            $(link).data('alt', Text);
                            $(link).attr({
                               'title' : altText,
                               'onclick' : 'WishList.delete(this);'
                            });
                            $(link).find('span').text(altText);
                        }
                    }
                    // возвращает разные типы json.items, не всегда срабатывает json.items.length,
                    // надо формировать массив ручками =)
                    var items =[];
                    $.each(json.items, function(key, val) {
                        items.push(val);
                    });
                    
                    if(items.length>0) {
                        $.each($('.wrapper_wishList'), function() {
                            $(this).find('a').text(items.length);
                            $(this).show();
                        });
                    } else {
                        $.each($('.wrapper_wishList'), function() {
                            $(this).find('a').text('');
                            $(this).hide();
                        });
                    }
                }
            });
        }
    },
    delete: function(link) {
        var ID = parseInt($(link).data('itemid'))||false;
        var Text = $(link).text();
        var altText = $(link).data('alt')||$(link).text();
        if(ID && $(link).hasClass('disabled')) {
            $.ajax({
                url: '/interactive/ajax.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    zone: 'site',
                    action: 'WishList',
                    option: 'delete',
                    itemID: ID
                }, 
                success: function(json) {        

                    $(link).removeClass('disabled');
                    $(link).data('alt', Text);
                    $(link).attr({
                       'title' : altText,
                       'onclick' : 'WishList.add(this);'
                    });
                    $(link).find('span').text(altText);

                    // возвращает разные типы json.items, не всегда срабатывает json.items.length,
                    // надо формировать массив ручками =)
                    var items =[];
                    $.each(json.items, function(key, val) {
                        items.push(val);
                    });
                    if(items.length>0) {
                        $.each($('.wrapper_wishList'), function() {
                            $(this).find('a').text(items.length);
                            $(this).show();
                        });
                    } else {
                        $.each($('.wrapper_wishList'), function() {
                            $(this).find('a').text('');
                            $(this).hide();
                        });
                    }
                }
            });
        }
    }
};

function empty(mixed_var) {
    return (mixed_var==="" || mixed_var===0 || mixed_var==="0" || mixed_var===null || mixed_var===false || mixed_var==='undefined' || typeof(mixed_var)==='undefined' || (is_array(mixed_var) && mixed_var.length===0));
}

function is_array(mixed_var) {
    return (mixed_var instanceof Array);
}

function in_array(needle, haystack, strict) {
    var found = false, key, strict = !!strict;
    for (key in haystack) {
        if ((strict && haystack[key]===needle) || (!strict && haystack[key]==needle)) {
            found = true;
            break;
        }
    }
    return found;
}

function implode(glue, pieces) {
    return ((pieces instanceof Array) ? pieces.join(glue) : pieces);
}
