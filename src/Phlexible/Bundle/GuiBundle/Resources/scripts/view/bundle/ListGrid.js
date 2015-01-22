Ext.define('Phlexible.gui.view.bundle.ListGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.gui-bundles-list',

    loadMask: true,
    hint: false,
    cls: 'p-gui-bundles-list',

    bundleText: '_bundleText',
    packageText: '_packageText',
    classnameText: '_classnameText',
    pathText: '_pathText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.gui.model.Bundle',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('gui_bundles'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'users',
                    idProperty: 'uid',
                    totalProperty: 'count'
                },
                extraParams: this.storeExtraParams
            },
            // TODO: enable when buffered paging reload works. disabled for now.
            autoLoad: true,
            remoteSort: false,
            sorters: [{
                property: 'id',
                direction: 'ASC'
            }],
            listeners: {
                load: function () {
                    if (this.filterData) {
                        this.setFilterData(this.filterData);
                    }
                },
                scope: this
            }
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.bundleText,
                width: 250,
                dataIndex: 'id',
                resizable: false
            },
            {
                header: this.packageText,
                width: 100,
                dataIndex: 'package',
                resizable: false
            },
            {
                header: this.classnameText,
                width: 400,
                dataIndex: 'classname',
                resizable: false,
                flex: 1
            },
            {
                header: this.pathText,
                width: 500,
                dataIndex: 'path',
                hidden: true,
                resizable: false
            }
        ];
    },

    setFilterData: function (data) {
        this.filterData = data;
        this.store.clearFilter();
        this.store.filterBy(function (record, id) {
            if (data.packages.length && data.packages.indexOf(record.data['package']) === -1) {
                return false;
            }

            if (data.filter) {
                var regex = new RegExp(data.filter, 'i');

                if (!record.data['id'].match(regex)) {
                    return false;
                }
            }

            return true;
        }, this);
    }
});
