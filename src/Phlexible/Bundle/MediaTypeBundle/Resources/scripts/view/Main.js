Ext.define('Phlexible.mediatype.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatype.view.MainController',
        'Phlexible.mediatype.view.List',
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
                xtype: 'panel',
                layout: 'border',
                region: 'east',
                itemId: 'east',
                width: 400,
                border: false,
                padding: '5 5 5 0',
                items: [{
                    xtype: 'mediatype.mimetypes',
                    componentId: 'mimetypes',
                    region: 'north',
                    height: 300
                },{
                    xtype: 'panel',
                    componentId: 'icons',
                    region: 'center',
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, lightgray; background-size: 30px 30px; padding: 5px;',
                    html: ''
                }]
            }
        ];
    }
});
