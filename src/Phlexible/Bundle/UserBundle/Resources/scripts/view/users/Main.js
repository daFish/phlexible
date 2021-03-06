/**
 * Users main view
 *
 * Input params:
 * - id (optional)
 *   Set focus on specific user
 */
Ext.define('Phlexible.user.view.users.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.user.view.users.Details',
        'Phlexible.user.view.users.Filter',
        'Phlexible.user.view.users.List'
    ],

    xtype: 'user.users.main',

    layout: 'border',
    cls: 'p-user-main',
    iconCls: Phlexible.Icon.get('users'),
    border: false,

    referenceHolder: true,
    viewModel: {
        stores: {
            users: {
                model: 'Phlexible.user.model.User',
                autoLoad: true,
                sorters: [{
                    property: 'username',
                    direction: 'ASC'
                }]
            }
        }
    },

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
            xtype: 'user.users.filter',
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
            xtype: 'user.users.list',
            itemId: 'usersGrid',
            reference: 'list',
            region: 'center',
            padding: '5 0 5 5',
            filterHelper: this.filterHelper,
            bind: {
                store: '{users}'
            }
        },{
            xtype: 'user.users.details',
            itemId: 'detailPanel',
            region: 'east',
            width: 300,
            padding: '5 5 5 0',
            split: true,
            collapsible: true,
            collapsed: true,
            overflowY: 'auto',
            bind: {
                users: '{list.selection}'
            }
        }];
    },

    loadParams: function(params) {
        var id = params.id,
            record = this.getComponent('usersGrid').getStore().getById(id);

        if (record) {
            this.getComponent('usersGrid').getSelectionModel().select([r]);
        }
    }
});
