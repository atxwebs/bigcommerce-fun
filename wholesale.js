<!-- Script to detect Retail/Wholesale Customer .. Performs some action -->
<!-- Must create category called WholesaleJS and assign to appropriate customer group -->
<!-- This one for example would be added to the product.html, it is css div specific. -->
<script>
$(document).ready(function() {
    var wholesaler = false;
    $('div.SideCategoryListFlyout li').each(function(index) {
        var str1 = $(this).text();
        var str2 = "WholesaleJS";
        if(str1.indexOf(str2) != -1) {
            wholesaler = true;
        };
    });      
    $("li:contains('WholesaleJS')").css("display","none");

    if(wholesaler == false) {
        //Hide pricing and add to cart functions here. Varies based on theme. Example:
        //var price = document.querySelector("ENTER ITS DIV CLASS HERE");
        //var cart = document.querySelector("ENTER ITS DIV CLASS HERE");
        //price.style.display = "none";
        //cart.style.display = "none";

    };
});
</script>
