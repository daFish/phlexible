Ext.define('Phlexible.mediatemplate.view.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.mediatemplate.model.MediaTemplate',
        'Phlexible.mediatemplate.model.ImageTemplate',
        'Phlexible.mediatemplate.model.VideoTemplate',
        'Phlexible.mediatemplate.model.MediaTemplate',
        'Phlexible.mediatemplate.view.MainController',
        'Phlexible.mediatemplate.view.List',
        'Phlexible.mediatemplate.view.Preview',
        'Phlexible.mediatemplate.view.image.Main',
        'Phlexible.mediatemplate.view.audio.Main',
        'Phlexible.mediatemplate.view.video.Main'
    ],

    xtype: 'mediatemplate.main',
    controller: 'mediatemplate.main',
    referenceHolder: true,
    viewModel: {
        stores: {
            templates: {
                model: 'Phlexible.mediatemplate.model.MediaTemplate',
                autoLoad: true,
                sorters: [{
                    property: 'key',
                    direction: 'ASC'
                }]
            }
        }
    },

    iconCls: Phlexible.Icon.get('image-resize'),
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
            reference: 'list',
            width: 200,
            margin: '5 0 5 5',
            bind: {
                store: '{templates}'
            },
            listeners: {
                selectionchange: 'onSelectionChange',
                createTemplate: 'onCreateTemplate'
            }
        },{
            region: 'center',
            border: false,
            layout: 'border',
            items: [{
                layout: 'card',
                region: 'west',
                itemId: 'cards',
                width: 320,
                border: true,
                activeItem: 0,
                margin: 5,
                items: [{
                    xtype: 'mediatemplate.image.main',
                    itemId: 'image',
                    reference: 'image',
                    header: false,
                    border: false,
                    bind: {
                        mediaTemplate: '{list.selection}'
                    },
                    listeners: {
                        save: 'onSave',
                        preview: 'onPreviewTemplate'
                    }
                }, {
                    xtype: 'mediatemplate.video.main',
                    itemId: 'video',
                    header: false,
                    border: false,
                    bind: {
                        mediaTemplate: '{list.selection}'
                    },
                    listeners: {
                        save: 'onSave',
                        preview: 'onPreviewTemplate'
                    }
                }, {
                    xtype: 'mediatemplate.audio.main',
                    itemId: 'audio',
                    header: false,
                    border: false,
                    bind: {
                        mediaTemplate: '{list.selection}'
                    },
                    listeners: {
                        save: 'onSave',
                        preview: 'onPreviewTemplate'
                    }
                }]
            },{
                xtype: 'mediatemplate.preview',
                region: 'center',
                itemId: 'preview',
                margin: '5 5 5 0',
                border: true
            }]
        }];
    },

    getListPanel: function() {
        return this.getComponent('list');
    },

    getCardPanel: function() {
        return this.getComponent(1).getComponent('cards');
    },

    getPreviewPanel: function() {
        return this.getComponent(1).getComponent('preview');
    }
});
