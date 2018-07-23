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

    snap = $(document).find('label:contains("SNAP")');
    if (snap.find('input:checkbox').prop('checked')) {
        $('.notfoodstamp').parent().hide();
    } else {
        $('.notfoodstamp').parent().show();
    }

    $(document).on('change', 'label:contains("SNAP")', function () {
        var cb = $(this).find('input:checkbox');
        if (cb.is(':checked')) {
            $('.notfoodstamp').parent().hide();
        } else {
            $('.notfoodstamp').parent().show();
        }
    });
    
    others = $(document).find('label:contains("Other")');
    $.each(others, function () {
        if (~$(this).attr('for').indexOf('household_assistances')) {
            var cb = $(this).find('input:checkbox');
            if (cb.is(':checked')) {
                $('#seeking').show();
            } else {
                $('#seeking').hide();
            }
        }
        if (~$(this).attr('for').indexOf('household_organizations')) {
            var cb = $(this).find('input:checkbox');
            if (cb.is(':checked')) {
                $('#receiving').show();
            } else {
                $('#receiving').hide();
            }
        }
    });

    $(document).on('change', 'label:contains("Other")', function () {
        if (~$(this).attr('for').indexOf('household_assistances')) {
            var cb = $(this).find('input:checkbox');
            seekingValue = $('#household_seeking').val();
            if (cb.is(':checked')) {
                $('#seeking').show();
                $('#household_seeking').val(seekingValue);
            } else {
                $('#seeking').hide();
                $('#household_seeking').val('');
            }
        }
        if (~$(this).attr('for').indexOf('household_organizations')) {
            var cb = $(this).find('input:checkbox');
            receivingValue = $('#household_receiving').val();
            if (cb.is(':checked')) {
                $('#receiving').show();
                $('#household_receiving').val(receivingValue);
            } else {
                $('#receiving').hide();
                $('#household_receiving').val('');
            }
        }
    });
})
