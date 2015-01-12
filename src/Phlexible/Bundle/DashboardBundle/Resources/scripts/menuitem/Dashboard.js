/**
 * Dashboard item
 */
Ext.define('Phlexible.dashboard.menuitem.Dashboard', {
    extend: 'Phlexible.gui.menu.item.XtypeItem',
    text: '_dashboard',
    iconCls: Phlexible.Icon.get('dashboard'),
    type: 'xtype',
    component: 'dashboard-dashboard'
});
