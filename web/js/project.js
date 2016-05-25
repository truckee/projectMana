var memberCount = $('#member-form').length;
$(document).ready(function () {
    // for contacts
    var contactForm = $('#contact_form');
    var foodStampSelect = $("select#household_foodStamps");
    $("#dialog").dialog({
        autoOpen: false,
    });
    $("#memberEditDialog").dialog({
        autoOpen: false,
        resizable: true,
        modal: true,
        width: '80%',
    });

//for printable reports
    $('#menuToggle').click(function () {
        if ($("#menuToggle").text() === 'Printable view') {
            $("#menuToggle").text('Menu');
            $(".menu").hide();
            $("html>body #content").css('margin-left', 10 + 'px');
        } else {
            $("#menuToggle").text('Printable view');
            $(".menu").show();
            $("html>body #content").css('margin-left', 180 + 'px');
        }
    });

    //member edit form
    $(document).on("click", "#member_isHead", function () {
        if ($("input[name='member[isHead]']").prop('checked') === true) {
            $("select[name='member[include]']").hide();
            $("label[for='member_include']").hide();
        } else {
            $("select[name='member[include]']").show();
            $("label[for='member_include']").show();
        }
    });
    $(document).on("change", "select[name='member[include]']", function () {
        if ($("select[name='member[include]']").val() === '1') {
            $("input[name='member[isHead]']").show();
            $("label[for='member_isHead']").show();
        } else {
            $("label[for='member_isHead']").hide();
        }
    });

    // for household addresses
    var addressForm = $('#addressForm');
    var addressWidget = $('#addressWidget').attr('data-prototype');
    $('#add-address').click(function () {
        addressForm.append($(addressWidget));
        return false;
    });

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

    $("#contact_household_button").click(function () {
        var houseId = $("#contact_householdId").val();
        if (houseId !== "") {
            // make sure household not already listed
            var present = false;
            var houseCol = $("td#idCol");
            $.each(houseCol, function () {
                if (this.textContent === houseId) {
                    present = true;
                }
            })
            if (!present) {
                var where = $(location).attr('pathname');
                var url = where.replace('contact/addContacts', 'household/contact/ + houseId');
                $.get(url, function (data) {
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

    $(document).on("click", "#selectAll", function () {
        if ($("#selectAll").prop("checked")) {
            $("input[type='checkbox']").prop("checked", true);
        } else {
            $("input[type='checkbox']").prop("checked", false);
        }
    });

    $("#contact_center").change(function () {
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
            var where = $(location).attr('pathname');
            var url = where.replace('contact/addContacts', 'contact/latest');
            var jqxhr = $.get(url, function (data) {
                $("#contact_store").data(data);
            })
                    .done(function () {
                        html = "";
                        found = false;
                        $.each($("#contact_store").data(), function (key, val) {
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

    foodStampSelect.click(function () {
        var option = $("#household_foodstamp option:selected").val();
        foodStampShowHide(option);
    });

    //launch member edit form
    $(".btn-sm").click(function () {
        id = this.id;
        nowAt = $(location).attr('pathname');
        houseAt = nowAt.indexOf('/household');
        if (id.startsWith('memberId')) {
            memberId = id.replace('memberId', '');
            url = nowAt.slice(0, houseAt) + '/member/edit/' + memberId;
            $.get(url, function (data) {
                $('#memberEditDialog').dialog({
                    title: 'Edit household member',
                    buttons: [
                        {
                            text: "Submit",
                            id: "submit",
                            class: "btn-xs btn-primary",
                            click: function () {
                                var formData = $("form").serialize();
                                $.post(url, formData, function (response) {
                                    //display form if validation errors
                                    if (response.startsWith('<form')) {
                                        $('#memberEditDialog').html(response);
                                        return;
                                    }
                                    member = $.parseJSON(response);
                                    update = member.fname + ' ' + member.sname + ' has been updated';
                                    //use jquery to make display match members entities
                                    if (member.excludeDate !== null) {
                                        $("a#memberId" + memberId).hide();
                                        $("#include" + memberId).html('<b>Excluded:</b> ' + member.excludeDate);
                                    } else {
                                        if (member.isHead) {
                                            $("#include" + memberId).html('<b>Head</b>');
                                            if (member.id !== member.headId) {
                                                $("#include" + member.headId).html('<b>Include: </b> Yes');
                                            }
                                        } else {
                                            $("#include" + memberId).html('<b>Include: </b> Yes');
                                        }
                                    }
                                    $("#fname" + memberId).text(member.fname);
                                    $("#sname" + memberId).text(member.sname);
                                    $("#dob" + memberId).text(member.dob);
                                    $('#memberEditDialog').html(update);
                                    $("#submit").hide();
                                })
                            }
                        },
                        {
                            text: 'Close',
                            id: "close",
                            class: "btn-xs btn-primary",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ],
                });
                $('#memberEditDialog').html(data);

                $('#memberEditDialog').dialog('open');
            });
        }

        if (id === 'addMember') {
            nowAt = $(location).attr('pathname');
            n = (nowAt.match(/\//g) || []).length;
            urlArray = nowAt.split("/");
            houseId = parseInt(urlArray[n - 1], 10);
            url = nowAt.slice(0, houseAt) + '/member/add/' + houseId;
            $.get(url, function (data) {
                $('#memberEditDialog').dialog({
                    title: 'Add household member',
                    buttons: [
                        {
                            text: "Submit",
                            id: "submit",
                            class: "btn-xs btn-primary",
                            click: function () {
                                var formData = $("form").serialize();
                                $.post(url, formData, function (response) {
                                    //display form if validation errors
                                    if (response.startsWith('<form')) {
                                        $('#memberEditDialog').html(response);
                                        return;
                                    }
                                    reply = $.parseJSON(response);
                                    name = reply.name;
                                    update = name + ' has been added';
                                    //use jquery to make display match members entities
                                    $("#members").append(reply.view);
                                    $('#memberEditDialog').html(update);
                                    $("#submit").hide();
                                })
                            }
                        },
                        {
                            text: 'Close',
                            id: "close",
                            class: "btn-xs btn-primary",
                            click: function () {
                                $(this).dialog("close");
                            }
                        }
                    ],
                });
                $('#memberEditDialog').html(data);

                $('#memberEditDialog').dialog('open');
            });
        }
    });
}
);

function removeAddress(me) {
    $(me).parents().eq(2).remove();
}

function tableSort() {
    //preserves first row
    var $tbody = $('table#contact_form tbody');
    $tbody.find('tr').sort(function (a, b) {
        var tda = $(a).find('td:eq(2)').text(); // can replace 1 with the column you want to sort on
        var tdb = $(b).find('td:eq(2)').text(); // this will sort on the second column
        // if a < b return 1
        return tda > tdb ? 1
                // else if a > b return -1
                : tda < tdb ? -1
                // else they are equal - return 0    
                : 0;
    }).appendTo($tbody);
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
