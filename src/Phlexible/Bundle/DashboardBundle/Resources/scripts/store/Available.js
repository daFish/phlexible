/**
 * Available store
 */
Ext.define('Phlexible.dashboard.store.Available', {
    extend: 'Ext.data.Store',
    required: ['Phlexible.dashboard.model.Portlet'],

    storeId: 'dashboard-available',
    model: 'Phlexible.dashboard.model.Portlet'
});