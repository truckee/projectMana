$(document).ready(function () {
$('#report_criteria_county').val('');
$('#report_criteria_center').val('');

//statistics criteria
    $('#report_criteria_county').change(function () {
        var me = $(this).val();
        if (me !== "") {
            $('#center_select').hide();
        } else {
            $('#center_select').show();
        }
    });

    $('#report_criteria_center').change(function () {
        var me = $(this).val();
        if (me !== "") {
            $('#county_select').hide();
        } else {
            $('#county_select').show();
        }
    });

});