Ext.define('Phlexible.mediatemplate.view.image.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.image.Form',
        'Phlexible.mediatemplate.view.image.Preview'
    ],

    xtype: 'mediatemplate.image.main',

    header: false,
    layout: 'border',

    imageTemplateTitleText: '_imageTemplateTitleText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function () {
        this.items = [
            {
                xtype: 'mediatemplate.image.form',
                region: 'west',
                itemId: 'form',
                width: 320,
                margin: 5,
                header: false,
                listeners: {
                    saveTemplate: function () {
                        this.fireEvent('paramssave');
                    },
                    preview: function (params, debug) {
                        this.getComponent('preview').createPreview(params, debug);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatemplate.image.preview',
                itemId: 'preview',
                region: 'center',
                margin: '5 5 5 0',
                header: false
            }
        ];
    },

    loadParameters: function (key, parameters) {
        this.getComponent('form').setTitle(Ext.String.format(this.imageTemplateTitleText, key));
        this.getComponent('form').loadParameters(key, parameters);
    }
});