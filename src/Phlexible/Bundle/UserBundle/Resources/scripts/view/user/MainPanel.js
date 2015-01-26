/**
 * Users main view
 *
 * Input params:
 * - id (optional)
 *   Set focus on specific user
 */
Ext.define('Phlexible.user.view.user.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.user-user-main',

    layout: 'border',
    cls: 'p-user-main',
    iconCls: Phlexible.Icon.get('users'),
    border: false,

    initComponent: function() {
        this.initMyFilterHelper();
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyFilterHelper: function() {
        this.filterHelper = Ext.create('Phlexible.gui.util.FilterHelper', {
            keys: [
                'key',
                'account_disabled',
                'account_has_expire_date',
                'account_expired',
                'roles',
                'groups'
            ]
        });

        if (!this.menuitem) {
            return;
        }

        this.filterHelper.applyValues(this.menuitem.getParameters());
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'user-user-filter',
            itemId: 'filterPanel',
            region: 'west',
            width: 250,
            collapsible: true,
            resizable: true,
            padding: '5 0 5 5',
            filterHelper: this.filterHelper,
            listeners: {
                applyFilter: function(values, filterHelper) {
                    var store = this.getComponent('usersGrid').getStore(),
                        key;

                    /*
                    if (this.menuitem) {
                        for (key in filterHelper.getUnsetValues()) {
                            this.menuitem.removeParameter(key);
                        }
                        for (key in filterHelper.getSetValues()) {
                            this.menuitem.setParameter(key, values[key]);
                        }
                    }
                    */

                    store.currentPage = 1;
                    store.reload({
                        start: 0,
                        page: 1
                    });
                },
                scope: this
            }
        },{
            xtype: 'user-user-list',
            itemId: 'usersGrid',
            region: 'center',
            padding: '5 0 5 5',
            filterHelper: this.filterHelper,
            listeners: {
                storeReload: function(grid, store) {
                    var selectionModel = grid.getSelectionModel(),
                        selection = selectionModel.getSelection(),
                        newSelection = [];

                    if (selection.length > 0) {
                        Ext.each(selection, function(record) {
                            newSelection.push(store.getAt(store.indexOf(record)));
                        });
                        this.onGridSelectionChange(grid, newSelection);
                    }
                },
                selectionchange: this.onGridSelectionChange,
                scope: this
            }
        },{
            xtype: 'user-user-details',
            itemId: 'detailPanel',
            region: 'east',
            width: 300,
            padding: '5 5 5 0',
            split: true,
            collapsible: true,
            collapsed: true,
            overflowY: 'auto'
        }];
    },

    onGridSelectionChange: function(grid, selected) {
        var detailPanel = this.getComponent('detailPanel');

        if (selected.length === 0) {
            detailPanel.clear();
            detailPanel.collapse();
        }
        else if (selected.length === 1) {
            detailPanel.showSingle(selected[0]);
            detailPanel.expand();
        }
        else {
            detailPanel.showMulti(selected);
            detailPanel.expand();
        }
    },

    loadParams: function(params) {
        var id = params.id,
            record = this.getComponent('usersGrid').getStore().getById(id);

        if (record) {
            this.getComponent('usersGrid').getSelectionModel().select([r]);
        }
    }
});
