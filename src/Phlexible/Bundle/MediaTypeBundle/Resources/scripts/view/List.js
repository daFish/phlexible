Ext.define('Phlexible.mediatype.view.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'mediatype.list',

    iconCls: Phlexible.Icon.get('image-share'),
    loadMask: true,
    stripeRows: true,

    nameText: '_nameText',
    mimetypesText: '_mimetypesText',
    categoryText: '_categoryText',
    attributesText: '_attributesText',
    iconText: '_iconText',
    reloadText: '_reloadText',
    iconsForText: '_iconsForText',

    initComponent: function () {
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.nameText,
                dataIndex: 'name',
                sortable: true,
                width: 150
            },
            {
                header: this.categoryText,
                dataIndex: 'category',
                width: 150
            },
            {
                header: this.iconText,
                dataIndex: 'icon',
                width: 150
            },
            {
                header: this.mimetypesText,
                dataIndex: 'mimetypes',
                sortable: false,
                flex: 1,
                renderer: function(v) {
                    if (Ext.isEmpty(v)) {
                        return '&nbsp;';
                    }
                    return v.join(', ');
                }
            },
            {
                header: this.attributesText,
                dataIndex: 'attributes',
                sortable: false,
                width: 200,
                hidden: true,
                renderer: function(v) {
                    return JSON.stringify(v);
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.reloadText,
                iconCls: 'x-tbar-loading',
                handler: function () {
                    this.store.reload();
                },
                scope: this
            }]
        }];
    }
});
