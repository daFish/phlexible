Ext.define('Phlexible.mediatype.view.MimetypesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.mediatype-mimetypes',

    title: Phlexible.mediatype.Strings.view.MimetypesGrid.title,
    iconCls: Phlexible.Icon.get('image-share'),
    loadMask: true,
    stripeRows: true,

    mimetypeText: Phlexible.mediatype.Strings.view.MimetypesGrid.mimetypeText,

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
