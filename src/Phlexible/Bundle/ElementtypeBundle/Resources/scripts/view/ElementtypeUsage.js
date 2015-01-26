Ext.define('Phlexible.elementtype.ElementtypeUsage', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.elementtype-usage',

    iconCls: Phlexible.Icon.get('user'),

    emptyText: '_emptyText',
    typeText: '_typeText',
    asText: '_asText',
    idText: '_idText',
    titleText: '_titleText',
    latestVersionText: '_latestVersionText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            fields: ['type', 'as', 'id', 'title', 'latest_version'],
            proxy: {
                type: 'ajax',
                url: '',
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'list',
                    totalProperty: 'total'
                }
            },
            autoLoad: false,
            sorters: [{
                property: 'title',
                direction: 'ASC'
            }]
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.typeText,
                dataIndex: 'type',
                width: 100,
                sortable: true
            },
            {
                header: this.asText,
                dataIndex: 'as',
                width: 100,
                sortable: true
            },
            {
                header: this.idText,
                dataIndex: 'id',
                width: 50,
                sortable: true
            },
            {
                header: this.titleText,
                dataIndex: 'title',
                sortable: true,
                flex: 1
            },
            {
                header: this.latestVersionText,
                sortable: true,
                dataIndex: 'latest_version'
            }
        ];
    },

    empty: function () {
        this.store.removeAll();
    },

    load: function (id, title, version, type) {
        this.store.getProxy().setUrl(Phlexible.Router.generate('elementtypes_usage', {id: id}));
        this.store.reload();
    }
});
