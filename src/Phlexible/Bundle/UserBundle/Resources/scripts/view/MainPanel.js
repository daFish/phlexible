/**
 * Main view
 *
 * Input params:
 * - id (optional)
 *   Set focus on specific user
 */
Ext.define('Phlexible.user.view.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.user-main',

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
                xtype: 'user-user-main',
                itemId: 'users'
            },{
                xtype: 'user-group-main',
                itemId: 'groups'
            }]
        };
    },

    loadParams: function(params) {
        this.getComponent('tabPanel').getComponent('users').loadParams(params);
    }
});
