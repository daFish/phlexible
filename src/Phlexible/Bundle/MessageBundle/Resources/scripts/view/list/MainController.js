Ext.define('Phlexible.message.view.list.MainController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.message.list.main',

    updateFacets: function(store) {
        this.getView().getComponent('filter').updateFacets(store.getProxy().getReader().rawData.facets);
    },

    updateFilter: function (criteria) {
        var store = this.getStore('messages');
        store.getProxy().setExtraParam('criteria', criteria ? Ext.encode(criteria) : null);
        store.reload();
    }
});
