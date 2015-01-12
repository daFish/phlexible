/**
 * Portlets store
 */
Ext.define('Phlexible.dashboard.store.Portlets', {
    extend: 'Ext.data.Store',
    storeId: 'dashboard-portlets',
    model: 'Phlexible.dashboard.model.Portlet'
});