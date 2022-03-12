require(['jquery', 'jquery/ui'], function ($) {
    var baseUrl = window.baseUrl;
    var url = baseUrl + '/search/search/container';
    var url = url.replace("#", "");
    var productWarningMessage = '<div class="url-warning-message"><p>Bitte zuerst Container auswählen!</p></div>';

    // jQuery('.container-type-version-name').on('click', function (e) {
    jQuery('.containers-front').on('click', function (e) {
        if (jQuery(event.target).text() == 'i') {
            return;
        }
        // var productUrl = jQuery(this).parents().parents().attr('data-source');
        var productUrl = jQuery(this).parent().attr('data-source');
        jQuery('.containers-front').removeClass('set-active');
        jQuery('.container-size-h').removeClass('set-color');
        // jQuery('.separator-border').css('display','none');
        jQuery('.container-type-version-name').removeClass('set-active');
        jQuery(this).addClass('set-active');
        jQuery(this).children().children('.container-size-h').addClass('set-color');
        jQuery(this).children('.container-type-version-name').addClass('set-active');
        // jQuery(this).children().children('.separator-border').css('display','block');
        jQuery('#product-url-href').attr("href", productUrl);
        // jQuery("#product-url-href").css('color','#fff200');

        //scroll down animation for mobile and desktop
        if (jQuery(window).width() < 426) {
            jQuery('html,body').animate({
                    scrollTop: jQuery("#product-url-href").offset().top - jQuery(".middle-header-content").height() - jQuery("#container-types-answer").height() / 4},
                'slow');
            setTimeout(function(){
                // jQuery("#product-url-href").css('color','#fff');
                jQuery(".container-chevron").css('opacity', 0);
            }, 30000);
        } else {
            jQuery('html,body').animate({
                    scrollTop: jQuery("#product-url-href").offset().top - jQuery(".middle-header-content").height() - jQuery("#container-types-answer").height() / 2},
                'slow');
            setTimeout(function(){
                // jQuery("#product-url-href").css('color','#000');
                jQuery(".container-chevron").css('opacity', 0);
            }, 30000);
        }

        jQuery(".container-chevron").css('opacity', 1);
        // jQuery("#product-url-href").css('color','#fff200');
        // setTimeout(function(){
        //     jQuery("#product-url-href").css('color','#000');
        // }, 1000);

    })

    jQuery('#product-url-href').on('click', function (e) {
        jQuery('.url-warning-message').remove();
        if (jQuery(this).attr('href') == '#' || jQuery(this).attr('href') == '') {
            event.preventDefault();
            jQuery('#container-product').append(productWarningMessage);
            setTimeout(function(){
                jQuery('.url-warning-message').remove();
            }, 2000);
        }
    })

})