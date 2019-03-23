/*
 * This file is part of the Truckee\Projectmana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function () {
// for household addresses
    var physical = $("input[type=radio][name='household[physicalAddress][physical]']");
    var mailing = $("input[name='household[mailingAddress][mailing]']");
    $("#physical").hide();
    $("#mailing").hide();
    physical.change(function () {
        if ("1" === this.value) {
            $("#physical").show();
        } else {
            $("#physical").hide();
        }
    });
    mailing.change(function () {
        if ("1" === this.value) {
            $("#mailing").show();
        } else {
            $("#mailing").hide();
        }
    });
    // turn on foodstamps queation if SNAP unchecked
    snap = $(document).find('label:contains("SNAP")').prev();
    if (snap.is(':checked')) {
        $('.notfoodstamp').parent().hide();
    } else {
        $('.notfoodstamp').parent().show();
    }

// change state of foodtamp question based on whether SNAP state changes
    $(document).on('change', snap, function () {
        if (snap.is(':checked')) {
            $('.notfoodstamp').parent().hide();
        } else {
            $('.notfoodstamp').parent().show();
        }
    });
    // set state of service & org inputs based on state of Other checkbox
    others = $('label:contains("Other")').prev();
    $.each(others, function () {
        if ($(this).attr('id').indexOf('household_assistances') === 0) {
            if ($(this).is(':checked')) {
                $('#seeking').show();
            } else {
                $('#seeking').hide();
            }
        }
        if ($(this).attr('id').indexOf('household_organizations') === 0) {
            if ($(this).is(':checked')) {
                $('#receiving').show();
            } else {
                $('#receiving').hide();
            }
        }
    });
    // collect service or org data if Other is checked
    $(document).on('change', others, function () {
        others.each(function () {
            if ($(this).attr('id').indexOf('household_assistances') === 0) {
                seekingValue = $('#household_seeking').val();
                if ($(this).is(':checked')) {
                    $('#seeking').show();
                    $('#household_seeking').val(seekingValue);
                } else {
                    $('#seeking').hide();
                    $('#household_seeking').val('');
                }
            }
            if ($(this).attr('id').indexOf('household_organizations') === 0) {
                receivingValue = $('#household_receiving').val();
                if ($(this).is(':checked')) {
                    $('#receiving').show();
                    $('#household_receiving').val(receivingValue);
                } else {
                    $('#receiving').hide();
                    $('#household_receiving').val('');
                }
            }
        });
    });
    // assure that household disabled options are preserved on persist
    if (0 < $("#household_options").length) {
        var house_options = JSON.parse($("#household_options").text());
        $.each(house_options, function (index, item) {
            $.each(item, function (k, v) {
                var formAttr = 'household_' + index + '_' + v.id;
                $("#" + formAttr).attr('disabled', 'disabled');
            });
        });
    }
//member edit form
    $(document).on("change", "select[name='member[include]']", function () {
        if ($("select[name='member[include]']").val() === '1') {
            $("input[name='member[isHead]']").show();
            $("label[for='member_isHead']").show();
        } else {
            $("label[for='member_isHead']").hide();
        }
    });
    
    var viewportWidth = window.innerWidth - 20;
    var viewportHeight = window.innerHeight - 20;
    if (viewportWidth > 1000)
        viewportWidth = 1000;
    if (viewportHeight > 500)
        viewportHeight = 500;

    $("#memberEditDialog").dialog({
        height: viewportHeight,
        width: viewportWidth,
        autoOpen: false,
        modal: false,
        resizable: true
        , position: {
            my: "top center",
            at: "top center",
            of: window,
            collision: "none"
        }
        , create: function (event, ui) {
            $(event.target).parent().css('position', 'fixed');
        }
    });

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
            $("input[name='member[isHead]']").hide();
        }
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
                $('#memberEditDialog').html(data);``
                if (0 < $("#member_options").length) {
                    var member_options = JSON.parse($("#member_options").text());
                    $.each(member_options, function (index, item) {
                        $.each(item, function (k, v) {
                            var formAttr = 'member_' + index + '_' + v.id;
                            $("#" + formAttr).attr('disabled', 'disabled');
                        });
                    });
                }
            }
            );
            $('#memberEditDialog').dialog('open');
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
                $('#memberEditDialog').dialog('open');
            });
        }

    }
    );
});
