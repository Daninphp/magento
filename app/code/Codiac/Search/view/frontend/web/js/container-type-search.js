require(['jquery', 'jquery/ui'], function ($) {
    var url = jQuery(location).attr("href") + '/search/search/container';
    jQuery('.container-type-version').on('click', function () {
        jQuery('.loader-gif').css('display','block');
        var id = jQuery(this).attr('id');
        var dataSource = jQuery(this).attr('data-source');
        jQuery.ajax({
            url: url,
            method: 'post',
            data: {'id': id, 'dataSource': dataSource, 'attribute': 'type'},
        }).done(function (answerContainer) {
            answerContainer = JSON.parse(answerContainer);
            jQuery(".container-types-answer").empty();
            jQuery('.loader-gif').css('display','none');
            if(typeof answerContainer === 'string') {
                window.location = answerContainer
            } else {
                jQuery('#container-error-message').append(answerContainer.error);
            }
        })
    });

});