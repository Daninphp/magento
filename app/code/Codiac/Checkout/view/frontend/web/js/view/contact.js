define([
    'Magento_Ui/js/form/form',
    'Magento_Checkout/js/model/step-navigator',
    'mage/translate',
    'underscore',
    'mage/storage',
    'mage/url'
], function (Component, stepNavigator, $t, __, storage, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Codiac_Checkout/contact',
            visible: true
        },
        initialize: function () {
            this._super();

            stepNavigator.registerStep(
                'contact',
                'contact',
                $t('Contact'),
                this.visible,
                _.bind(this.navigate, this),
                this.sortOrder
            );
        },

        initObservable: function () {
            this._super().observe(['visible']);

            return this;
        },

        navigate: function (step) {
            step && step.isVisible(true);
        },

        setContactInformation: function () {
            const data = this.source.contact;

            jQuery.ajax({
                url: urlBuilder.build('contact/checkout'),
                method: "post",
                data: JSON.stringify(data),
                dataType: "json",
            }).done(function (answer) {
                console.log('done');
            }).fail(function (answer) {
                console.log(answer)
            });

            //validate
            //form submission
            stepNavigator.next();
        }
    })
})