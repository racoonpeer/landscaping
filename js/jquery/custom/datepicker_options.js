
var datepickerOptoins = {
    dateFormat:'dd.mm.yy', //Date.format = 'dd.mm.yyyy';
    showOn: 'both', 
    firstDay: 1, 
    buttonImage: '/js/jquery/custom/datepicker.png', 
    buttonImageOnly: true
};

$(function() {
    try {
        
        $("#datepicker1").datepicker(datepickerOptoins);
        $("#datepicker2").datepicker(datepickerOptoins);
        $("#datepicker3").datepicker(datepickerOptoins);
        
//        $("#datepicker1").datepicker( 'setDate', 'Now');
//        $("#datepicker2").datepicker({dateFormat:'dd.mm.yyyy', showOn: 'button', firstDay: dayFirst, dayNamesMin: dayMin, buttonImage: '/datepicker/calendar.gif', buttonImageOnly: true});
//        $("#datepicker2").datepicker( 'setDate', +1);

    } catch(e) {}
});