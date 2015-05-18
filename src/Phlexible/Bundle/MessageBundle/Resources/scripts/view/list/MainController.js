Ext.define('Phlexible.message.view.list.MainController', {
    extend: 'Ext.app.ViewController',
    alias: 'controller.message.list.main',

    updateFacets: function(store) {
        this.getView().getComponent('filter').updateFacets(store.getProxy().getReader().rawData.facets);
    },

    updateFilter: function (expression) {
        var store = this.getStore('messages');
        store.getProxy().setExtraParam('expression', expression ? Ext.encode(expression) : null);
        store.reload();
    }
});
