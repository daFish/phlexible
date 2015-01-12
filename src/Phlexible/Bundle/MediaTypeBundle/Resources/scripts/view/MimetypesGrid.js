Ext.define('Phlexible.mediatype.MimetypesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.mediatype-mimetypes',

    title: Phlexible.mediatype.Strings.mimetypes,
    strings: Phlexible.mediatype.Strings,
    iconCls: Phlexible.Icon.get('image-share'),
    loadMask: true,
    stripeRows: true,

    initComponent: function () {
        this.store = new Ext.data.Store({
            fields: ['mimetype'],
            sorters: [{
                property: 'mimetype',
                direction: 'ASC'
            }]
        });

        this.columns = [
            {
                header: this.strings.mimetype,
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
