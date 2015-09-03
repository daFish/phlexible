/**
 * Groups main view
 */
Ext.define('Phlexible.user.view.groups.Main', {
    extend: 'Ext.Panel',
    requires: [
        'Phlexible.user.model.Group',
        'Phlexible.user.view.groups.List'
    ],
    xtype: 'user.groups.main',

    iconCls: Phlexible.Icon.get('users'),
    cls: 'p-group-main',
    layout: 'fit',

    referenceHolder: true,
    viewModel: {
        stores: {
            groups: {
                model: 'Phlexible.user.model.Group',
                autoLoad: true,
                sorters: [{
                    property: 'name',
                    direction: 'ASC'
                }]
            }
        }
    },

    initComponent: function() {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'user.groups.list',
            bind: {
                store: '{groups}'
            }
        }];
    }
});
