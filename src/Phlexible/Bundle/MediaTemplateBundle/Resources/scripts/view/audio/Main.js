Ext.define('Phlexible.mediatemplate.view.audio.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.audio.Form',
        'Phlexible.mediatemplate.view.audio.Preview'
    ],

    xtype: 'mediatemplate.audio.main',

    layout: 'border',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.audio.form',
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
                xtype: 'mediatemplate.audio.preview',
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