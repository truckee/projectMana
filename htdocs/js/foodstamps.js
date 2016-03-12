$(document).ready(function() {
    var foodStampSelect = $("#household_foodstamp");
    var option = $("#household_foodstamp option:selected").val();
    foodStampShowHide(option);

    foodStampSelect.click(function() {
        var option = $("#household_foodstamp option:selected").val();
        foodStampShowHide(option);
    });    
});

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
//            $("label[for=household_notfoodstamp]").show();
//            $("select#household_notfoodstamp").show();
        }
        //No foodstamps: option === "1" 
        if (option === "1") {
            $("label[for=household_notfoodstamp]").show();
            $("select#household_notfoodstamp").show();
//            $("select#household_notfoodstamp").val("");
            $("label[for=household_fsamount]").hide();
            $("select#household_fsamount").hide();
            $("select#household_fsamount").val("");
//            $("label[for=household_fsamount]").show();
//            $("select#household_fsamount").show();
        }
        //Yes option === "2"
        if (option === "2") {
            $("label[for=household_fsamount]").show();
            $("select#household_fsamount").show();
//            $("select#household_fsamount").val("");
            $("label[for=household_notfoodstamp]").hide();
            $("select#household_notfoodstamp").hide();
            $("select#household_notfoodstamp").val("");
        }
}