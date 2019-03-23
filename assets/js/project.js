/*
 * This file is part of the Truckee\ProjectMana package.
 *
 * (c) George W. Brooks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function () {
    // assure that disabled options are preserved on persist
    $("input[type=Submit]").click(function () {
        $("input").removeAttr("disabled");
        $("select").removeAttr("disabled");
    });

    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });

    $(document).on("click", "#selectAll", function () {
        if ($("#selectAll").prop("checked")) {
            $("input[type='checkbox']").prop("checked", true);
        } else {
            $("input[type='checkbox']").prop("checked", false);
        }
    });
    
}
);

