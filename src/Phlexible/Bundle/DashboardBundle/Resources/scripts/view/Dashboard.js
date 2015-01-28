/**
 * Dashboard
 */
Ext.define('Phlexible.dashboard.view.Dashboard', {
    extend: 'Ext.dashboard.Dashboard',

    requires: ['Ext.dashboard.Dashboard', 'Phlexible.dashboard.view.DashboardController'],

    xtype: 'dashboard.dashboard',
    controller: 'dashboard.dashboard',

    iconCls: Phlexible.Icon.get('dashboard'),
    cls: 'p-dashboard-dashboard',
    //header: false,
    border: false
});
