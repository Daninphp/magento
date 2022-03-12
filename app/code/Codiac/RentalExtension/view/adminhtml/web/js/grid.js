require(["jquery"], function ($) {
    $(document).ready(function () {
        var i = setInterval(function () {
            if ($('.order_extension').length) {
                $('.order_extension').each(function (i, obj) {
                    var existingElemeng = $(this).children().text().length
                    if (existingElemeng > 0) {
                        $(this).children().css('background', '#F7EB37');
                        $(this).children().css('padding', '4px');
                    }

                });
             clearInterval(i);
            }
        }, 1000);
    });
});
