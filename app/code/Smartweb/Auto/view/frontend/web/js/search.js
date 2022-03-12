require(['jquery', 'jquery/ui'], function ($) {
    jQuery( document ).ready(function() {
        jQuery('#car-mark').change(function () {
            var markId = jQuery('#car-mark').val();
            const elementsToEmpty = jQuery('#model-mark, #car-type , #bulb-type');
            elementsToEmpty.empty();
            elementsToEmpty.prop('disabled', true)
            jQuery.ajax({
                url: window.baseUrl,
                method: 'post',
                data: {'markId': markId}
            }).done(function (response) {
                const element = jQuery('#model-mark');
                const models = JSON.parse(response);
                models.forEach(function (model) {
                    element.append('<option value="' + model.id + '">' + model.label + '</option>')
                })
                element.prop('disabled', false);
            })
        });

        jQuery('#model-mark').change(function () {
            var modelId = jQuery('#model-mark').val();
            const elementsToEmpty = jQuery('#car-type , #bulb-type');
            elementsToEmpty.empty();
            elementsToEmpty.prop('disabled', true)
            jQuery.ajax({
                url: window.baseUrl,
                method: 'post',
                data: {'modelId': modelId}
            }).done(function (response) {
                const element = jQuery('#car-type');
                element.empty();
                const models = JSON.parse(response);
                models.forEach(function (model) {
                    element.append('<option value="' + model.id + '">' + model.label + '</option>')
                })
                element.prop('disabled', false);
            })
        });

        jQuery('#car-type').change(function () {
            var typeId = jQuery('#car-type').val();
            const element = jQuery('#bulb-type');
            element.prop('disabled', true)
            element.empty();
            jQuery.ajax({
                url: window.baseUrl,
                method: 'post',
                data: {'typeId': typeId}
            }).done(function (response) {
                const models = JSON.parse(response);
                models.forEach(function (model) {
                    element.append('<option value="' + model.id + '">' + model.label + '</option>')
                })
                element.prop('disabled', false);
            })
        });

        jQuery('#find').on('click', function () {
            var bulbId = jQuery('#bulb-type').val();
            jQuery.ajax({
                url: window.baseUrl,
                method: 'post',
                data: {'bulbId': bulbId}
            }).done(function (response) {
                jQuery('.products-grid').html(response)
            })
        });
    })
});
