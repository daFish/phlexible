/**
 * Dashboard
 */
Ext.define('Phlexible.dashboard.view.Dashboard', {
    extend: 'Ext.dashboard.Dashboard',
    requires: [
        'Ext.dashboard.Dashboard',
        'Phlexible.dashboard.view.DashboardController'
    ],

    xtype: 'dashboard.dashboard',
    controller: 'dashboard.dashboard',

    iconCls: Phlexible.Icon.get('dashboard'),
    componentCls: 'p-dashboard-dashboard',
    border: false,

    dockedItems: [{
        xtype: 'dashboard-infobar-welcome',
        dock: 'top',
        data: {},
        listeners: {
            addPortlet: 'onAddPortlet'
        }
    }]
});
