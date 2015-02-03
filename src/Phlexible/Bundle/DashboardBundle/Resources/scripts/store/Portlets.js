/**
 * Portlets store
 */
Ext.define('Phlexible.dashboard.store.Portlets', {
    extend: 'Ext.data.Store',
    requires: ['Phlexible.dashboard.model.Portlet'],

    storeId: 'dashboard-portlets',
    model: 'Phlexible.dashboard.model.Portlet'
});