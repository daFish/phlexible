Ext.define('Phlexible.mediatype.view.List', {
    extend: 'Ext.grid.GridPanel',
    requires: [
        'Phlexible.mediatype.model.MediaType'
    ],
    xtype: 'mediatype.list',

    iconCls: 'p-mediatype-component-icon',
    loadMask: true,
    stripeRows: true,

    nameText: '_nameText',
    mimetypesText: '_mimetypesText',
    sizesText: '_sizesText',
    reloadText: '_reloadText',
    iconsForText: '_iconsForText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_mediatype_get_mediatypes'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'mediatypes',
                    idProperty: 'id',
                    totalProperty: 'count'
                }
            },
            model: 'Phlexible.mediatype.model.MediaType',
            autoLoad: true,
            sorters: [{
                property: 'key',
                direction: 'ASC'
            }]
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.nameText,
                dataIndex: 'name',
                sortable: true,
                width: 100
            },
            {
                header: 'de', //this.strings.type,
                dataIndex: 'titles',
                sortable: true,
                width: 250,
                renderer: function(v) {
                    return v['en'];
                }
            },
            {
                header: 'en', //this.strings.type,
                dataIndex: 'titles',
                sortable: true,
                width: 250,
                renderer: function(v) {
                    return v['en'];
                }
            },
            {
                header: this.mimetypesText,
                dataIndex: 'mimetypes',
                sortable: false,
                width: 150,
                renderer: function (m) {
                    if (!Ext.isArray(m) || !m.length) {
                        return 'No mimetypes';
                    }
                    if (m.length > 1) return m.length + ' mimetypes';
                    return m.length + ' mimetype';
                }
            },
            {
                header: this.sizesText,
                dataIndex: 'icons',
                sortable: false,
                width: 100,
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
        }]
    },

    initMyListeners: function() {
        this.on({
            selectionchange: function (sm) {
                var records = sm.getSelection();
                if (!records.length) {
                    return;
                }
                this.fireEvent('mediaTypeChange', records[0]);
            },
            itemdblclick: function (grid, mediaType) {
                debugger;
                var key = mediaType.get('key'),
                    name = mediaType.get('titles').en,
                    icons = mediaType.get('icons'),
                    html1 = '',
                    html2 = '';

                Ext.Object.each(icons, function(size, icon) {
                    html1 += '<td align="center" valign="bottom"><img src="' + icon + '" width="' + size + '" height="' + size + '" /></td>';
                    html2 += '<td align="center">' + size + 'x' + size + '</td>';
                });

                var w = Ext.create('Ext.window.Window', {
                    title: Ext.String.format(this.iconsForText, name),
                    width: 420,
                    height: 320,
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, lightgray; background-size: 30px 30px; padding: 5px;',
                    modal: true,
                    html: '<table><tr>' + html1 + '</tr><tr>' + html2 + '</tr></table>'
                });
                w.show();
            },
            scope: this
        });
    }
});
