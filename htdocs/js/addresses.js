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

    physical.change(function() {
        if ("1" === this.value) {
            $("#physical").show();
        } else {
            $("#physical").hide();
        }
    });

    mailing.change(function() {
        if ("1" === this.value) {
            $("#mailing").show();
        } else {
            $("#mailing").hide();
        }
    });

})
;

