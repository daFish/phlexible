Ext.define('Phlexible.mediatype.view.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'mediatype.list',

    iconCls: Phlexible.Icon.get('image-share'),
    loadMask: true,
    stripeRows: true,

    nameText: '_nameText',
    mimetypesText: '_mimetypesText',
    noMimetypesText: '_noMimetypesText',
    sizesText: '_sizesText',
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
                flex: 1
            },
            {
                header: 'de', //this.strings.type,
                dataIndex: 'titles',
                sortable: true,
                flex: 2,
                renderer: function(v) {
                    return v.en;
                }
            },
            {
                header: 'en', //this.strings.type,
                dataIndex: 'titles',
                sortable: true,
                flex: 2,
                renderer: function(v) {
                    return v.en;
                }
            },
            {
                header: this.sizesText,
                dataIndex: 'icons',
                sortable: false,
                flex: 1,
                renderer: function(v) {
                    return Ext.Object.getKeys(v).join(',');
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
