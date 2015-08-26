Ext.define('Phlexible.elementtype.view.Usage', {
    extend: 'Ext.grid.GridPanel',
    requires: [
        'Phlexible.elementtype.model.ElementtypeUsage'
    ],
    xtype: 'elementtype.usage',

    iconCls: Phlexible.Icon.get('document-share'),

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
            model: 'Phlexible.elementtype.model.ElementtypeUsage',
            proxy: {
                type: 'ajax',
                url: '',
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'usages',
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
        this.store.getProxy().setUrl('');
        this.store.removeAll();
    },

    /**
     * @param {Phlexible.elementtype.model.Elementtype} elementtype
     */
    load: function (elementtype) {
        this.disable();

        if (elementtype.get('new')) {
            this.empty();
        } else {
            this.store.getProxy().setUrl(Phlexible.Router.generate('phlexible_api_elementtype_get_elementtype_usages', {elementtypeId: elementtype.id}));
            this.store.reload({
                callback: this.enable,
                scope: this
            });
        }
    }
});
