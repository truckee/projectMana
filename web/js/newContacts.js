/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function () {
    var contactForm = $('#contact_form');
    $("#contact_center").val("");

    $("#contact_household_button").click(function () {
        var houseId = $("#contact_householdId").val();
        $(".alert").html("");
        $(".alert").removeClass('alert-warning');
        if (houseId !== "") {
            // make sure household not already listed
            var present = false;
            var houseCol = $(".text-right");
            $.each(houseCol, function () {
                if (this.textContent === houseId) {
                    present = true;
                    return;
                }
            });
            if (!present) {
                var where = $(location).attr('pathname');
                var source = where.substring(where.indexOf("addContacts")+11);
                var loc = where.replace(source, '');
                var url = loc.replace('contact/addContacts', 'household/contact/' + houseId);
                $.get(url, function (data) {
                    //make sure household exists
                    if (data !== '') {
                        $("#latestContacts").prepend(data);
                        alert('Household added');
                    } else {
                        alert('Household does not exist');
                    }
                });
            } else {
                $("#idSelect" + houseId).prop('checked', true);
                alert("Listed household now included");
            }
            $("#contact_householdId").val("");
        }
    });

    $("#contact_center").change(function () {
        center = $("#contact_center").val();
        contactForm.html("");
        $(".alert").html("");
        $(".alert").removeClass('alert-warning');
        if (center === "") {
            $("#householdById").hide();
        } else {
            $("#householdById").show();
            $("#dialog").dialog('open');
            $("#dialog").dialog("widget")            // get the dialog widget element
                    .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                    .hide();
            var where = $(location).attr('pathname');
            source = where.split('/').reverse()[0];
            var url = where.replace('contact/addContacts/' + source, 'contact/latest/' + center + '/' + source);
            var jqxhr = $.get(url, function (data) {
                if (data.length > 0) {
                    contactForm.html(data);
                    $("#dialog").dialog('close');
                } else {
                    $("#dialog").dialog('close');
                    alert('No data found');
                }
            })
        }
    });

    if ($("#householdById").length > 0) {
        $("#householdById").hide();
    }
    if ($("ul.help-block").text() === 'Type must be selected') {
        $("#contact_center").val("");
    }

    if ($(".alert").text().indexOf('contacts added') > 0) {
        $("#contact_center").val("");
        $("#contact_contactDesc").val("");
    }
})
