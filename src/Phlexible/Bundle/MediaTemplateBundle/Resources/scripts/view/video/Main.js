Ext.define('Phlexible.mediatemplate.view.video.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.video.Form',
        'Phlexible.mediatemplate.view.video.Preview'
    ],

    xtype: 'mediatemplate.video.main',

    layout: 'border',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.video.form',
                itemId: 'form',
                region: 'west',
                width: 320,
                margin: 5,
                listeners: {
                    saveTemplate: function () {
                        this.fireEvent('saveTemplate');
                    },
                    preview: function (params, debug) {
                        this.getComponent('preview').createPreview(params, debug);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatemplate.video.preview',
                itemId: 'preview',
                region: 'center',
                margin: '5 5 5 0',
                header: false
            }
        ];
    },

    loadParameters: function (key, parameters) {
        this.getComponent('form').loadParameters(key, parameters);
    }
});