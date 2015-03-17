var memberCount = $('#member-form').length;
$(document).ready(function() {
    // for household members
    var memberList = $('#member-list');
    var newMemberWidget = memberList.attr('data-prototype');

    $('#menuToggle').click(function() {
        if ( $("#menuToggle").text() === 'Printable view')  {  
            $("#menuToggle").text('Menu');
            $(".menu").hide();
            $("html>body #content").css('margin-left', 10+'px');
        } else {
            $("#menuToggle").text('Printable view');
            $(".menu").show();
            $("html>body #content").css('margin-left', 180+'px');
           
        }
    });

    $('#add-member').click(function() {
        //allow for adding members in client edit
        var includeCount = $('#member-list li#included').length;
        var excludeCount = $('#member-list li#excluded').length;
        newMember = memberCount + includeCount + excludeCount;
        //remove existing members correction
        memberWidget = newMemberWidget.replace(/__name__/g, newMember);
        memberList.append($(memberWidget));
        return false;
    });

    // for househnold addresses
    var addressCount = $('#address-form').length;
    var addressList = $('#address-list');
    var newAddressWidget = addressList.attr('data-prototype');

    $('#add-address').click(function() {
        //allow for adding addresss in household edit
        addressCount = addressCount + 1;
        addressWidget = newAddressWidget.replace(/__address__/g, addressCount);
        addressList.append($(addressWidget));
        return false;
    });

    //statistics criteria
    $('#report_criteria_county_id').change(function() {
        var me = $(this).val();
        if (me !== "") {
            $('#center_select').hide();
        } else {
            $('#center_select').show();
        }
    });
    $('#report_criteria_center_id').change(function() {
        var me = $(this).val();
        if (me !== "") {
            $('#county_select').hide();
        } else {
            $('#county_select').show();
        }
    });
}
);

function removeMember(r) {
    $("div.border").last().remove();
}
function removeAddress(r) {
    $("ul#address-form").last().remove();
}
