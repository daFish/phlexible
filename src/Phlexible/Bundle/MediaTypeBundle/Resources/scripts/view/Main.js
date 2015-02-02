Ext.define('Phlexible.mediatype.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatype.view.MainController',
        'Phlexible.mediatype.view.MediaTypes',
        'Phlexible.mediatype.view.Mimetypes'
    ],

    xtype: 'mediatype.main',
    controller: 'mediatype.main',

    iconCls: Phlexible.Icon.get('image-share'),
    layout: 'border',
    border: false,

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatype.list',
                region: 'center',
                header: false,
                padding: 5,
                listeners: {
                    mediaTypeChange: 'onChangeMediaType'
                }
            },
            {
                xtype: 'mediatype.mimetypes',
                itemId: 'mimetypes',
                region: 'east',
                padding: '5 5 5 0',
                width: 400
            }
        ];
    }
});
