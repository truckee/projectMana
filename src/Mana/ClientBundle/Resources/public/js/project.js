var memberCount = $('#member-form').length;
$(document).ready(function() {
    // for household members
    var memberList = $('#member-list');
    var newMemberWidget = memberList.attr('data-prototype');
    // for household addresses
    var addressCount = $('#address-form').length;
    var addressList = $('#address-list');
    var newAddressWidget = addressList.attr('data-prototype');
    // for contacts
    var contactForm = $('#contact_form');
    var env = $('#env').text();
    var foodStampSelect = $("select#household_foodStamps");
    showHideIncludeHead();

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

  $('input#isHead').click(function() {
      var hohId = $("input[id=headId]").val();
      var checkedId = $("input:radio:checked").val();
      var dateAdded = $("#dateAdded").text();
      var v1 = $.trim(dateAdded);
        if (hohId !== checkedId && v1 === "") {
            alert("REMINDER:\nSelecting new head of household copies\ndob, etc. to head and removes member")
        }
    showHideIncludeHead();
    });
    
    $('#add-address').click(function() {
        //allow for adding addresss in household edit
        addressCount = addressCount + 1;
        addressWidget = newAddressWidget.replace(/__address__/g, addressCount);
        addressList.append($(addressWidget));
        return false;
    });

    //statistics criteria
    $('#report_criteria_county').change(function() {
        var me = $(this).val();
        if (me !== "") {
            $('#center_select').hide();
        } else {
            $('#center_select').show();
        }
    });
    $('#report_criteria_center').change(function() {
        var me = $(this).val();
        if (me !== "") {
            $('#county_select').hide();
        } else {
            $('#county_select').show();
        }
    });

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

    var foodStampSelect = $("#household_foodstamp");
    var option = $("#household_foodstamp option:selected").val();
    foodStampShowHide(option);

    foodStampSelect.click(function() {
        var option = $("#household_foodstamp option:selected").val();
        foodStampShowHide(option);
    });        
    
}
);

function removeMember(r) {
    $("div.border").last().remove();
}

function removeAddress(r) {
    $("ul#address-form").last().remove();
}

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

function foodStampShowHide(option) {
        //Blank option === ""
        if (option === "" || option > "2") {
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
            $("label[for=household_notfoodstamp]").hide();
            $("select#household_notfoodstamp").hide();
            $("select#household_notfoodstamp").val("");
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
        }
        //No foodstamps: option === "1" 
        if (option === "1") {
            $("label[for=household_notfoodstamp]").show();
            $("select#household_notfoodstamp").show();
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
        }
        //Yes option === "2"
        if (option === "2") {
            $("label[for=household_fsamount]").show();
            $("select#household_fsamount").show();
            $("label[for=household_notfoodstamp]").hide();
            $("select#household_notfoodstamp").hide();
            $("select#household_notfoodstamp").val("");
        }
}
/**
* sets visibility of isHead radio & include 
if isHead=1, then include is hidden, other rows are shown
if include=0 or No, then isHead is hidden, else shown
head of household last name input is disabled
 * @returns {Boolean} */
function showHideIncludeHead() {
    $('.border ul').each(function() {
        headInput = $(this).find("input[name='household[isHead]']").prop("checked");
        showBlock = $(this).find("li#headShow")
        headShow = showBlock.length
        if (headInput) {
            $(this).find("li#included").hide();
            $(this).find("li:contains('Criminal history')").show();
            $(this).find("input[name$='[sname]']").prop('readonly', 'readonly');
            $(this).parent().addClass("head");
        } else {
            $(this).find("li#included").show();
            $(this).find("li:contains('Criminal history')").hide();
            $(this).parent().removeClass("head")
        }
        if (headShow) {
            $(this).parent().addClass("head");
        }
        
//        var excluded=$(this).find('input[type=hidden]').val();
        var exclude=$(this).find("select[name$='[include]']").val();
        if (exclude === "0") {
            $(this).find("li#radioHead").hide();
        } else {
            $(this).find("li#radioHead").show();
        }
    });
    return true;
}
