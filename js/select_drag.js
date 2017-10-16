
var jsSelectUtils = {
    initSelect: function(select_id) {
        return ((typeof(select_id) === 'string' || select_id instanceof String) ? document.getElementById(select_id) : select_id);
    },
    addNewOption: function(select_id, opt_value, opt_name, do_sort, check_unique) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            var n = oSelect.length;
            if (check_unique !== false) {
                for (var i = 0; i < n; i++)
                    if (oSelect[i].value == opt_value)
                        return;
            }
            var newoption = new Option(opt_name, opt_value, false, false);
            oSelect.options[n] = newoption;
        }
        if (do_sort === true)
            this.sortSelect(select_id);
    },
    deleteOption: function(select_id, opt_value) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            for (var i = 0; i < oSelect.length; i++) {
                if (oSelect[i].value == opt_value) {
                    oSelect.remove(i);
                    break;
                }
            }
        }
    },
    deleteSelectedOptions: function(select_id) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            var i = 0;
            while (i < oSelect.length){
                if (oSelect[i].selected) {
                    oSelect[i].selected = false;
                    oSelect.remove(i);
                } else i++;
            }
        }
    },
    deleteAllOptions: function(oSelect) {
        if (oSelect) {
            for (var i = oSelect.length - 1; i >= 0; i--)
                oSelect.remove(i);
        }
    },
    optionCompare: function(record1, record2) {
        var value1 = record1.optText.toLowerCase();
        var value2 = record2.optText.toLowerCase();
        if (value1 > value2)
            return(1);
        if (value1 < value2)
            return(-1);
        return(0);
    },
    sortSelect: function(select_id) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            var myOptions = [];
            var n = oSelect.options.length;
            for (var i = 0; i < n; i++) {
                myOptions[i] = {
                    optText: oSelect[i].text,
                    optValue: oSelect[i].value
                };
            }
            myOptions.sort(this.optionCompare);
            oSelect.length = 0;
            n = myOptions.length;
            for (var i = 0; i < n; i++) {
                var newoption = new Option(myOptions[i].optText, myOptions[i].optValue, false, false);
                oSelect[i] = newoption;
            }
        }
    },
    selectAllOptions: function(select_id) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            var n = oSelect.length;
            for (var i = 0; i < n; i++)
                oSelect[i].selected = true;
        }
    },
    selectOption: function(select_id, opt_value) {
        var oSelect = this.initSelect(select_id);
        if (oSelect) {
            var n = oSelect.length;
            for (var i = 0; i < n; i++)
                oSelect[i].selected = (oSelect[i].value == opt_value);
        }
    },
    addSelectedOptions: function(oSelect, to_select_id, max_length, check_unique, do_sort) {
        if (!oSelect)
            return;
        var n = oSelect.length;

        var ss = this.initSelect(to_select_id);
        var ln = ss ? ss.length : 0;

        var dels = new Array();
        for (var i = 0; i < n; i++) {
            if (max_length > 0 && ln >= max_length)
                break;
            if (oSelect[i].selected) {
                this.addNewOption(to_select_id, oSelect[i].value, oSelect[i].text, do_sort, check_unique);
                dels.push(i);
                ln++;
            }
        }
        for (var i = dels.length-1; i >= 0; i--) {
            oSelect.remove(dels[i]);
        }
    },
    moveOptionsUp: function(oSelect) {
        if (!oSelect)
            return;
        var n = oSelect.length;
        for (var i = 0; i < n; i++) {
            if (oSelect[i].selected && i > 0 && oSelect[i - 1].selected === false) {
                var option1 = new Option(oSelect[i].text, oSelect[i].value);
                var option2 = new Option(oSelect[i - 1].text, oSelect[i - 1].value);
                oSelect[i] = option2;
                oSelect[i].selected = false;
                oSelect[i - 1] = option1;
                oSelect[i - 1].selected = true;
            }
        }
    },
    moveOptionsDown: function(oSelect) {
        if (!oSelect)
            return;
        var n = oSelect.length;
        for (var i = n - 1; i >= 0; i--) {
            if (oSelect[i].selected && i < n - 1 && oSelect[i + 1].selected === false) {
                var option1 = new Option(oSelect[i].text, oSelect[i].value);
                var option2 = new Option(oSelect[i + 1].text, oSelect[i + 1].value);
                oSelect[i] = option2;
                oSelect[i].selected = false;
                oSelect[i + 1] = option1;
                oSelect[i + 1].selected = true;
            }
        }
    },
    addExtraOptionsParams: function(select, params) {
        var options = $(select).find("option");
        $.each(options, function (i, option) {
            for (var prop in params) {
                option[prop] = params[prop];
                $(option).attr(prop, params[prop]);
            }
        });
    }
};
