$(document).ready(function () {
    var foodStampSelect = $("#household_foodstamp");

    foodStampShowHide($("#household_foodstamp option:selected").val());

    $("input[type=Submit]").click(function () {
        $("input").removeAttr("disabled");
        $("select").removeAttr("disabled");
    });

    if (0 < $("#household_options").length) {
        var house_options = JSON.parse($("#household_options").text());
        $.each(house_options, function (index, item) {
            $.each(item, function (k, v) {
                var formAttr = 'household_' + index + '_' + v.id;
                $("#" + formAttr).attr('disabled', 'disabled');
            });
        });
    }

    $("#dialog").dialog({
        autoOpen: false,
        modal: true
    });

    $("#memberEditDialog").dialog({
        autoOpen: false,
        resizable: true,
        modal: true,
        width: '80%'
    });

    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
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
            $("#memberOffenses").show();
        } else {
            $("select[name='member[include]']").show();
            $("label[for='member_include']").show();
            $("#memberOffenses").hide();
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

    $(document).on("click", "#selectAll", function () {
        if ($("#selectAll").prop("checked")) {
            $("input[type='checkbox']").prop("checked", true);
        } else {
            $("input[type='checkbox']").prop("checked", false);
        }
    });

    foodStampSelect.change(function () {
        var option = $("#household_foodstamp option:selected").val();
        foodStampShowHide(option);
    });

    //launch member edit form
    $(".btn-sm").click(function () {
        id = this.id;
        nowAt = $(location).attr('pathname');
        houseAt = nowAt.indexOf('/household');
        if (id.indexOf('memberId') === 0) {
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
                            type: "submit",
                            click: function () {
                                $("input").removeAttr("disabled");
                                $("select").removeAttr("disabled");
                                var formData = $("form").serialize();
                                $.post(url, formData, function (response) {
                                    //display form if validation errors
                                    if (response.indexOf('<form') === 0) {
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
                                            memberHTML = '<span id="include' + memberId + '"><b>Head</b></span>';
                                            $("#row" + memberId).css({"background-color": "lightyellow"});
                                            $('#household_heading').text(member.fname + ' ' + member.sname);
                                            if (member.id !== member.headId) {
                                                headHTML = '<span id="include' + member.headId + '"><b>Include: </b> Yes</span>';
                                                $("#include" + member.headId).html(headHTML);
                                                $("#row" + member.headId).css({"background-color": ""});
                                            }
                                        } else {
                                            memberHTML = '<span id="include' + memberId + '"><b>Include: </b> Yes</span>';
                                        }
                                        $("#include" + memberId).html(memberHTML);
                                    }
                                    $("#fname" + memberId).text(member.fname);
                                    $("#sname" + memberId).text(member.sname);
                                    $("#dob" + memberId).text(member.dob);
                                    $('#memberEditDialog').html(update);
                                    $("#submit").hide();
                                });
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
                    ]
                });

                $('#memberEditDialog').html(data);
                if ($("#member_isHead").prop("checked") === false) {
                    $("#memberOffenses").hide();
                } else {
                    $("#memberOffenses").show();
                }
                if (0 < $("#member_options").length) {
                    var member_options = JSON.parse($("#member_options").text());
                    $.each(member_options, function (index, item) {
                        $.each(item, function (k, v) {
                            var formAttr = 'member_' + index + '_' + v.id;
                            $("#" + formAttr).attr('disabled', 'disabled');
                        });
                    });
                }
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
                                    if (response.indexOf('<form') === 0) {
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
                                });
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
                    ]
                });
                $('#memberEditDialog').html(data);
                $("#memberOffenses").hide();
                $('#memberEditDialog').dialog('open');
            });
        }
    });
}
);

function foodStampShowHide(option) {
    //Blank option === ""
    if (option === "" || option > "2") {
        $("label[for=household_fsamount]").parent().hide();
        $("select#household_fsamount").val("");
        $("label[for=household_notfoodstamp]").parent().hide();
        $("select#household_notfoodstamp").val("");
    }
    //No foodstamps: option === "1"
    if (option === "1") {
        $("label[for=household_notfoodstamp]").parent().show();
        $("label[for=household_fsamount]").parent().hide();
        $("select#household_fsamount").val("");
    }
    //Yes option === "2"
    if (option === "2") {
        $("label[for=household_fsamount]").parent().show();
        $("label[for=household_notfoodstamp]").parent().hide();
        $("select#household_notfoodstamp").val("");
    }
}
