Ext.define('Phlexible.gui.view.BundlesController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.gui.bundles',
    routes: {
        'bundles': 'onBundles'
    },

    init: function () {
        this.loadFilterValues();

        this.initMyTasks();
        this.loadFilterValues();

        this.getView().getComponent('list').getStore().on({
            load: function () {
                if (this.filterData) {
                    this.setFilterData(this.filterData);
                }
            },
            scope: this
        });

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.task = new Ext.util.DelayedTask(this.updateFilter, this);
    },

    getPackagesForm: function() {
        return this.getView().getComponent('filter').getComponent('packages');
    },

    loadFilterValues: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('gui_bundles_filtervalues'),
            success: this.onLoadFilterValues,
            scope: this
        });
    },

    onLoadFilterValues: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.packages && data.packages.length && Ext.isArray(data.packages)) {
            this.getPackagesForm().removeAll();
            Ext.each(data.packages, function (item) {
                this.getPackagesForm().add({
                    xtype: 'checkbox',
                    name: 'package_' + item.id,
                    boxLabel: item.title,
                    checked: item.checked,
                    listeners: {
                        change: this.updateFilter,
                        scope: this
                    }
                });
            }, this);
        }
    },

    onBundles: function() {
        alert('onBundles');
    },

    onKeyup: function (field, event) {
        if (event.getKey() == event.ENTER) {
            this.task.cancel();
            this.updateFilter();
            return;
        }

        this.task.delay(500);
    },

    onResetFilter: function () {
        this.getView().getComponent('filter').getForm().reset();
        this.updateFilter();
    },

    updateFilter: function () {
        var store = this.getView().getComponent('list').getStore(),
            values = this.getView().getComponent('filter').getValues(),
            packages = [],
            filter = '',
            filters = [];

        Ext.Object.each(values, function(key, value) {
            //if (values[key] !== 'on') continue;

            if (key.substr(0, 8) == 'package_' && value === 'on') {
                packages.push(key.substr(8));
            }
            else if (key === 'filter') {
                filter = value;
            }
        });

        if (packages.length) {
            filters.push(new Ext.util.Filter({
                property: 'package',
                value: packages,
                operator: 'in'
            }));
        }
        if (filter) {
            filters.push(new Ext.util.Filter({
                property: 'id',
                value: filter,
                operator: 'like'
            }));
        }
        store.clearFilter(true);
        if (filters.length) {
            store.addFilter(filters);
            this.getView().getComponent('filter').getDockedComponent('buttons').getComponent('resetBtn').enable();
        } else {
            this.getView().getComponent('filter').getDockedComponent('buttons').getComponent('resetBtn').disable();
        }
    }
});