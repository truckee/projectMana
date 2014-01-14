$(document).ready(function() {
    // for contacts
    var contactForm = $('#contact_form');
    var env = $('#env').text();

    $("#contact_household_button").click(function() {
        var houseId = $("#contact_householdId").val();
        if (houseId !== "") {
            // make sure household not already listed
            var present = false;
            var houseCol = $("td#idCol");
            $.each(houseCol, function() {
                if (this.textContent === houseId) {
                    present = true;
                }
            })
            if (!present) {
                if (env !== 'dev') {
                    url = "/household/contact/" + houseId;
                }
                else {
                    url = "/app_dev.php/household/contact/" + houseId;
                }

                $.get(url, function(data) {
                    //make sure household exists
                    if (data !== 0) {
                        $("#contacts p").text("Center's contacts")
                        $("#household_store").data(data);
                        var household = $("#household_store");
                        var head = household.data('head');
                        var html = "";
                        html += '<tr id="latest"><td><input type="checkbox" checked="checked" name="contact_household[' + houseId + ']" value="' + houseId + '">';
                        html += '<td id="idCol">' + houseId + "<td>" + head;
                        $("#contact_form").append(html);
                        $("tr#latestHead").remove();
                        $("tr#latestFooter").remove();
                        tableSort();
                        $(headerHtml).insertBefore("#contact_form tr:eq(0)");
                        $("#contact_form").append(footerHtml);
                        var high = $("tr#latest").height() * $("tr#latest").length + 250;
                        $("#contacts").height(high);
                    } else {
                        alert('Household does not exist');
                    }
                })
            } else {
                alert("Household already listed")
            }
            $("#contact_householdId").val("");
        }
    });

    $(document).on("click", "#selectAll", function() {
        if ($("#selectAll").prop("checked")) {
            $("input[type='checkbox']").prop("checked", true);
        } else {
            $("input[type='checkbox']").prop("checked", false);
        }
    });

    $("#contact_center").change(function() {
        $("tr#latest").remove();
        $("tr#latestHead").remove();
        $("tr#latestFooter").remove();
        var center = $("#contact_center").val();
        if (center === "") {
            $("#contacts p").text("")
            $("#householdById").hide();
            $("#submitButton").hide();
        } else {
            $("#householdById").show();
            $("#submitButton").show();
            $("#contacts p").text("Center's contacts")
            $('#dialog').dialog('open');
            $('#dialog').dialog("widget")            // get the dialog widget element
                    .find(".ui-dialog-titlebar-close") // find the close button for this dialog
                    .hide();
            if (env !== 'dev') {
                url = "/contact/latest";
            }
            else {
                url = "/app_dev.php/contact/latest";
            }

            var jqxhr = $.get(url, function(data) {
                $("#contact_store").data(data);
            })
                    .done(function() {
                        html = "";
                        found = false;
                        $.each($("#contact_store").data(), function(key, val) {
                            if (val.centerId === parseInt(center)) {
                                html += '<tr id="latest"><td><input type="checkbox" id="idSelect" name="contact_household[' + val.id + ']" value="' + val.id + '">';
                                html += '<td id="idCol">' + val.id + "<td>" + val.head;
                                html += "<td>" + val.type + "<td>" + val.date
                                found = true;
                            }
                        })
                        if (found) {
                            $("#contacts p").text("Center's recent contacts")
                            $("#contact_form").append(html);
                            tableSort();
                            $("#contact_form").append(footerHtml);
                            $(headerHtml).insertBefore("#contact_form tr:eq(0)")
                            var high = $("tr#latest").height() * $("tr#latest").length + 200
                            $("#contacts").height(high);
                            $('#dialog').dialog('close');
                        } else {
                            $('#dialog').dialog('close');
                            alert('No data found');
                        }
                    });


        }

    })

    //get latest contact data if required
    if (contactForm.length > 0) {
        var headerHtml = '<tr id="latestHead"><td><input type="checkbox" id="selectAll"></td><td>ID</td><td>Head</td><td>Distribution</td><td>Date</td></tr>';
        var footerHtml = '<tr id="latestFooter"><td colspan="5"><input id="submitButton" class="smallbutton" type="submit" name="submit" value="Submit contacts"></td></tr>';
        $("#householdById").hide();
        $("#submitButton").hide();

        var centerSelect = $("select#contact_center");
        centerSelect[0].selectedIndex = 0;
        var typeSelect = $("select#contact_contactDesc");
        typeSelect[0].selectedIndex = 0;
        $("#dialog").dialog({
            autoOpen: false,
            height: 100,
            width: 200,
            modal: true,
        });
    }

});

function addLatest() {
    alert("Here now!");
    $("#latest").remove();
    var centerData = $("data-store").data();
    var center = $("#center_select").val();
    var html = "";
    $.each(centerData, function(key, val) {
        if (val.id == center) {
            html += '<tr id="latest"><td><input type="checkbox" name="contact_household" value="' + val.id + '">';
            html += "<td>" + val.id + "<td>" + val.head;
            html += "<td>" + val.type + "<td>" + val.date
        }
    })
    $("#contact-form").append(html);
}

function tableSort() {
    //preserves first row


    var $tbody = $('table#contact_form tbody');
    $tbody.find('tr').sort(function(a, b) {
        var tda = $(a).find('td:eq(2)').text(); // can replace 1 with the column you want to sort on
        var tdb = $(b).find('td:eq(2)').text(); // this will sort on the second column
        // if a < b return 1
        return tda > tdb ? 1
                // else if a > b return -1
                : tda < tdb ? -1
                // else they are equal - return 0    
                : 0;
    }).appendTo($tbody);
//    $("<tr>" + tds).insertBefore("#contact_form tr:eq(0)")
}

function showContactSubmitButton() {
    if ($("input:checked").length === 0) {
        $("#submitButton").hide();
    } else {
        $("#submitButton").show();
    }
}

function col2Reset() {
    $("#column2").height(400);
}
function submitTest() {

$(document).keypress(function(e) {
  if(e.which === 13) {
    alert('Enter pressed');
  }
});

    
}