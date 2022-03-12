require(['jquery', 'jquery/ui'], function ($) {
    var baseUrl = window.baseUrl;
    var url = baseUrl + '/search/search/container';
    var url = url.replace("#", "");

    // jQuery('#search-results .categories .category-name').on('click', function (e) {
    jQuery('#search-results .categories').on('click', function (e) {
        var elementText = jQuery(event.target).text().trim();
        if (elementText == 'i') {
            return;
        }

        jQuery('.loader-gif').css('display','block');
        // var categoryId = jQuery(this).attr('id');
        var categoryId = jQuery(this).children('div').attr('id');
        // var filterData = jQuery(this).children('div').attr('data-source');
        jQuery("#container-types-answer").empty();
        jQuery('#container-types').css('display','block');
        jQuery('.categories').children().children('.category-name').removeClass('set-active');
        jQuery(this).children().children('.category-name').addClass('set-active');
        jQuery('#container-types div').children().children('.category-name').removeClass('set-active');
        // jQuery('.container-type').attr('data-source', categoryId + '=' + filterData);

        jQuery.ajax({
            url: url,
            method: 'post',
            data: {'categoryId': categoryId},
        }).done(function (answerContainer) {
            jQuery('.loader-gif').css('display','none');
            jQuery('html,body').animate({
                    scrollTop: jQuery("#container-types-answer").offset().top - jQuery(".middle-header-content").height()},
                'slow');
            jQuery("#container-types-answer").html(answerContainer);
            jQuery("#container-types-answer").show();
            var existingElement =  document.getElementById('search-history');
            if (typeof(existingElement) != 'undefined' && existingElement != null)
            {
                document.getElementById('search-history').remove();
            }

        })
    })

    // jQuery('.container-grose a').on("click", function(e) {
    //     e.stopPropagation();
    // });
})