Ext.define('Phlexible.message.model.Filter', {
    extend: 'Ext.data.Model',

    entityName: 'Filter',
    idProperty: 'id',
    fields:[
        {name: 'id', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'userId', type: 'string'},
        {name: 'private', type: 'bool'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'expression'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_message_get_filters'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'filters',
            totalProperty: 'count'
        },
        writer: {
            type: 'json',
            writeAllFields: true,
            transform: function(data, request) {
                return {filter: data};
            }
        }
    }
});
