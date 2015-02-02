Ext.define('Phlexible.mediatemplate.view.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.mediatemplate.view.MainController',
        'Phlexible.mediatemplate.view.List',
        'Phlexible.mediatemplate.view.image.Main',
        'Phlexible.mediatemplate.view.audio.Main',
        'Phlexible.mediatemplate.view.video.Main',
        'Phlexible.mediatemplate.view.pdf2swf.Main'
    ],

    xtype: 'mediatemplate.main',
    controller: 'mediatemplate.main',

    iconCls: Phlexible.Icon.get('image-select'),
    layout: 'border',
    border: false,

    loadParams: function () {
    },

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'mediatemplate.list',
            region: 'west',
            itemId: 'list',
            width: 200,
            margin: '5 0 5 5',
            header: false,
            listeners: {
                loadTemplate: 'onLoadTemplate',
                createTemplate: 'onCreateTemplate'
            }
        }, {
            region: 'center',
            layout: 'card',
            itemId: 'cards',
            activeItem: 0,
            border: false,
            items: [{
                xtype: 'mediatemplate.image.main',
                itemId: 'image',
                border: false,
                listeners: {
                    saveTemplate: 'onSaveTemplate'
                }
            }, {
                xtype: 'mediatemplate.video.main',
                itemId: 'video',
                border: false,
                listeners: {
                    saveTemplate: 'onSaveTemplate'
                }
            }, {
                xtype: 'mediatemplate.audio.main',
                itemId: 'audio',
                border: false,
                listeners: {
                    saveTemplate: 'onSaveTemplate'
                }
            }, {
                xtype: 'mediatemplate.pdf2swf.main',
                itemId: 'pdf2swf',
                border: false,
                listeners: {
                    saveTemplate: 'onSaveTemplate'
                }
            }]
        }];
    },

    getListPanel: function() {
        return this.getComponent('list');
    },

    getCardPanel: function() {
        return this.getComponent('cards');
    }
});
