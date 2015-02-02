Ext.define('Phlexible.mediatype.view.Mimetypes', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'mediatype.mimetypes',

    iconCls: Phlexible.Icon.get('image-share'),
    loadMask: true,
    stripeRows: true,
    viewConfig: {
        deferEmptyText: false
    },

    emptyText: '_emptyText',
    mimetypeText: '_mimetypeText',

    initComponent: function () {
        this.store = Ext.create('Ext.data.Store', {
            fields: ['mimetype'],
            sorters: [{
                property: 'mimetype',
                direction: 'ASC'
            }]
        });

        this.columns = [
            {
                header: this.mimetypeText,
                dataIndex: 'mimetype',
                sortable: true,
                flex: 1
            }
        ];

        this.callParent(arguments);
    },

    loadMimetypes: function (mimetypes) {
        if (mimetypes) {
            var mimetypesData = [];
            Ext.each(mimetypes, function (mimetype) {
                mimetypesData.push({mimetype: mimetype});
            });
            this.store.loadData(mimetypesData);
        } else {
            this.store.removeAll();
        }
    }
});
