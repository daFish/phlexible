/**
 * Main view
 *
 * Input params:
 * - id (optional)
 *   Set focus on specific user
 */
Ext.define('Phlexible.user.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.user.view.MainController',
        'Phlexible.user.view.groups.Main',
        'Phlexible.user.view.users.Main'
    ],

    xtype: 'user.main',
    controller: 'user.main',

    layout: 'fit',
    iconCls: Phlexible.Icon.get('users'),
    border: false,

    initComponent: function() {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = {
            xtype: 'tabpanel',
            itemId: 'tabPanel',
            border: false,
            activeTab: 0,
            items: [{
                xtype: 'user.users.main',
                itemId: 'users'
            },{
                xtype: 'user.groups.main',
                itemId: 'groups'
            }]
        };
    },

    loadParams: function(params) {
        this.getComponent('tabPanel').getComponent('users').loadParams(params);
    }
});
