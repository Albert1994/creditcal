$( document ).ready(function() {
    $("#credit").submit(function(e){

        e.preventDefault();

        var amount = $("#amount").val();
        var term = $("#term").val();
        var rate = $("#rate").val();
        var startmonth = $("#startmonth").val();
        var typecredit = $("#typecredit").val();
        var startyear = $("#startyear").val();
        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: { amount: amount, term: term, rate: rate, startmonth: startmonth, startyear: startyear,typecredit: typecredit,}
        })
            .done(function(json) {
                var obj = JSON.parse(json);
                $("#overpay").text(obj.overpay);
                $("#payment").text(obj.payment);
                $("#schedule").html(obj.schedule);
            });
    });
});