Ext.define('Phlexible.message.view.filter.Preview', {
    extend: 'Phlexible.message.view.list.List',
    requires: ['Phlexible.message.view.list.List'],

    xtype: 'message.filter.preview',

    cls: 'p-message-filter-preview',

    store: {
        //type: 'buffered',
        model: 'Message',
        autoLoad: false,
        remoteSort: true,
        //leadingBufferZone: 300,
        //pageSize: 100,
        pageSize: 25,
        sorters: [{
            property: 'createdAt',
            direction: 'DESC'
        }],
        listeners: {
            load: 'updateFacets'
        }
    },

    setExpression: function(expression) {
        this.getStore().getProxy().setExtraParam('expression', Ext.encode(expression));
    }
});
