define(['jquery', 'jquery/ui', 'mage/mage'], function ($) {
    "use strict";
    return function form() {
        const url = 'syncit_form/form/submit';
        (function () {
            jQuery('#syncit-form').submit(function (event) {
                jQuery('body').trigger('processStart');
                event.preventDefault();
                if (!jQuery(this).validation('isValid')) return;
                const formData = jQuery(this).serialize();
                jQuery.ajax({
                    url: url,
                    method: 'post',
                    data: formData,
                }).done(function (response) {
                    jQuery('body').trigger('processStop');
                    response = JSON.parse(response)
                    if (parseInt(response.response_code) === 200 && response.success === true) {
                        jQuery('#syncit-form').hide();
                        document.body.scrollTop = document.documentElement.scrollTop = 0;
                        jQuery('.success').html(response.message);
                    } else if (parseInt(response.response_code) === 444 && response.success === false) {
                        alert(response.message);
                        jQuery("#captcha-container-syncit_form button").trigger('click');
                    } else if (parseInt(response.response_code) === 400 && response.success === false) {
                        alert(response.message);
                    }
                })
            })
        })();
    }
});
