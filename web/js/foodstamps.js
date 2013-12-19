$(document).ready(function() {
    var foodStampSelect = $("select#household_foodStamps");
    foodStampShowHide(foodStampSelect.val());

    foodStampSelect.click(function() {
        foodStampShowHide(foodStampSelect.val());
    });    
});

function foodStampShowHide(option) {
        if (option === "1") {
            $("label[for=household_notfoodstamp]").hide();
            $("select#household_notfoodstamp").hide();
            $("select#household_notfoodstamp").val("");
            $("label[for=household_fsamount]").show();
            $("select#household_fsamount").show();
        }
        if (option === "0") {
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
            $("label[for=household_notfoodstamp]").show();
            $("select#household_notfoodstamp").show();
        }
        if (option === "2" || option === "") {
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
            $("label[for=household_notfoodstamp]").hide();
            $("select#household_notfoodstamp").hide();
            $("select#household_notfoodstamp").val("");
        }
}