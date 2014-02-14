$(document).ready(function() {
    showHideIncludeHead();
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
}
);

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