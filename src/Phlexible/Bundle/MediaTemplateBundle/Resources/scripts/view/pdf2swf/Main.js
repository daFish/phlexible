Ext.define('Phlexible.mediatemplate.view.pdf2swf.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.pdf2swf.Form',
        'Phlexible.mediatemplate.view.pdf2swf.Preview'
    ],

    xtype: 'mediatemplate.pdf2swf.main',

    header: false,
    layout: 'border',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.pdf2swf.form',
                itemId: 'form',
                region: 'west',
                width: 320,
                margin: 5,
                header: false,
                listeners: {
                    paramsload: function () {

                    },
                    paramssave: function () {
                        this.fireEvent('paramssave');
                    },
                    preview: function (params, debug) {
                        this.getComponent('preview').createPreview(params, debug);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatemplate.pdf2swf.preview',
                itemId: 'preview',
                region: 'center',
                margin: '5 5 5 0',
                header: false
            }
        ];
    },

    loadParameters: function (key, parameters) {
        this.getComponent('form').setTitle(Ext.String.format(this.pdf2swfTemplateTitleText, key));

        this.getComponent('form').loadParameters(key, parameters);
    }
});