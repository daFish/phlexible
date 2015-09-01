Ext.define('Phlexible.mediatype.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatype.model.MediaType',
        'Phlexible.mediatype.view.MainController',
        'Phlexible.mediatype.view.List'
    ],
    xtype: 'mediatype.main',
    controller: 'mediatype.main',

    iconCls: Phlexible.Icon.get('image-share'),
    layout: 'border',
    border: false,
    referenceHolder: true,
    viewModel: {
        stores: {
            mediatypes: {
                model: 'Phlexible.mediatype.model.MediaType',
                autoLoad: true,
                sorters: [{
                    property: 'name',
                    direction: 'ASC'
                }]
            }
        }
    },

    mimetypesText: '_mimetypesText',
    mimetypeText: '_mimetypeText',
    noMimetypesText: '_noMimetypesText',
    iconsText: '_iconsText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatype.list',
                reference: 'list',
                region: 'center',
                padding: 5,
                bind: {
                    store: '{mediatypes}'
                }
            },
            {
                xtype: 'panel',
                layout: 'border',
                region: 'east',
                itemId: 'east',
                width: 300,
                border: false,
                items: [{
                    region: 'north',
                    xtype: 'grid',
                    height: 220,
                    title: this.mimetypesText,
                    emptyText: this.noMimetypesText,
                    stripeRows: true,
                    padding: '5 5 5 0',
                    viewConfig: {
                        deferEmptyText: false
                    },
                    store: Ext.create("Ext.data.Store", {
                        fields: ['mimetype']
                    }),
                    /*
                    bind: {
                        store: '{list.selection.mimetypes}'
                    },
                    */
                    columns: [
                        {
                            header: this.mimetypeText,
                            dataIndex: 'mimetype',
                            sortable: true,
                            flex: 1
                        }
                    ]
                },{
                    xtype: 'panel',
                    title: this.iconsText,
                    region: 'center',
                    padding: '0 5 5 0',
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, #cbdbef; background-size: 30px 30px;',
                    bodyPadding: 10,
                    html: '',
                    bind: {
                        html: '<div style="text-align: center;">' +
                            '<div>16x16<br/><img width="16" height="16" src="/bundles/phlexiblemediamanager/svg/{list.selection.icon}.svg" /></div><hr/>' +
                            '<div>32x32<br/><img width="32" height="32" src="/bundles/phlexiblemediamanager/svg/{list.selection.icon}.svg" /><div/><hr/>' +
                            '<div>64x64<br/><img width="64" height="64" src="/bundles/phlexiblemediamanager/svg/{list.selection.icon}.svg" /><div/><hr/>' +
                            '<div>256x256<br/><img width="256" height="256" src="/bundles/phlexiblemediamanager/svg/{list.selection.icon}.svg" /><div/>' +
                            '</div>'
                    }
                }]
            }
        ];
    }
});
