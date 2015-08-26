Ext.define('Phlexible.message.model.Subscription', {
    extend: 'Ext.data.Model',

    entityName: 'Subscription',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'filter'},
        {name: 'filterId', type: 'string'},
        {name: 'userId', type: 'string'},
        {name: 'handler', type: 'string'},
        {name: 'attributes'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_message_get_subscriptions'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'subscriptions'
        },
        writer: {
            type: 'json',
            writeAllFields: true,
            transform: function(data, request) {
                return {subscription: data};
            }
        }
    }
});
