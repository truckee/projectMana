$(document).ready(function() {
    // for household members
    var memberList = $('#member-list');
    var newMemberWidget = memberList.attr('data-prototype');

    $('#add-member').click(function() {
        //allow for adding members in client edit
        var memberCount = $("ul[id^=member-]").length;
        memberWidget = newMemberWidget.replace(/__name__/g, memberCount);
        memberList.append($(memberWidget));
        return false;
    });

    // for household addresses
    var addressCount = $('ul#address-form').length;
    var addressList = $('#address-list');
    var newAddressWidget = addressList.attr('data-prototype');

    $('#add-address').click(function() {
        //allow for adding addresss in household edit
        addressCount = addressCount;
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
