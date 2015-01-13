Ext.define('MediaTypesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.mediatype-list',

    title: Phlexible.mediatype.Strings.media_types,
    strings: Phlexible.mediatype.Strings,
    iconCls: 'p-mediatype-component-icon',
    loadMask: true,
    stripeRows: true,

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
                url: Phlexible.Router.generate('mediatypes_list'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'mediatypes',
                    idProperty: 'id',
                    totalProperty: 'totalCount'
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
                header: this.strings.id,
                dataIndex: 'id',
                sortable: false,
                hidden: true,
                width: 220
            },
            {
                id: 'key',
                header: this.strings.key,
                dataIndex: 'key',
                sortable: true,
                width: 100
            },
            {
                header: 'de', //this.strings.type,
                dataIndex: 'de',
                sortable: true,
                width: 250
            },
            {
                header: 'en', //this.strings.type,
                dataIndex: 'en',
                sortable: true,
                width: 250
            },
            {
                header: this.strings.mimetypes,
                dataIndex: 'mimetypes',
                sortable: false,
                width: 100,
                renderer: function (m) {
                    if (!Ext.isArray(m) || !m.length) {
                        return 'No mimetypes';
                    }
                    if (m.length > 1) return m.length + ' mimetypes';
                    return m.length + ' mimetype';
                }
            },
            {
                header: '16',
                dataIndex: 'icon16',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '32',
                dataIndex: 'icon32',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '48',
                dataIndex: 'icon48',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            },
            {
                header: '256',
                dataIndex: 'icon256',
                sortable: false,
                width: 30,
                renderer: this.iconRenderer
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.strings.reload,
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
            itemdblclick: function (grid, r) {
                var key = r.get('key');

                var w = Ext.create('Ext.window.Window', {
                    title: Ext.String.format(this.strings.icons_for, r.get('en')),
                    width: 420,
                    height: 320,
                    bodyStyle: 'background: white; background: linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(135deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 15px 15px, linear-gradient(-45deg, transparent 75%, rgba(255, 255, 255, .4) 0%) 0 0, lightgray; background-size: 30px 30px; padding: 5px;',
                    modal: true,
                    html: '<table><tr>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes16/' + key + '.gif') + '" width="16" height="16" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes32/' + key + '.gif') + '" width="32" height="32" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes48/' + key + '.gif') + '" width="48" height="48" />' +
                        '</td>' +
                        '<td align="center" valign="bottom">' +
                        '<img src="' + Phlexible.bundleAsset('/phlexiblemediatype/mimetypes256/' + key + '.gif') + '" width="256" height="256" />' +
                        '</td>' +
                        '</tr><tr>' +
                        '<td align="center">16x16</td>' +
                        '<td align="center">32x32</td>' +
                        '<td align="center">48x48</td>' +
                        '<td align="center">256x256</td>' +
                        '</tr></table>'
                });
                w.show();
            },
            scope: this
        });
    },

    iconRenderer: function (k) {
        var icon = k ? 'tick-circle' : 'cross-circle';
        return Phlexible.Icon.inline(icon);
    }
});
