/**
 * Available store
 */
Ext.define('Phlexible.dashboard.store.Available', {
    extend: 'Ext.data.Store',
    storeId: 'dashboard-available',
    model: 'Phlexible.dashboard.model.Portlet'
});