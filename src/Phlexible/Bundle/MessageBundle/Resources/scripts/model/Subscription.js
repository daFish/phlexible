Ext.define('Phlexible.message.model.Subscription', {
    extend: 'Ext.data.Model',

    entityName: 'Subscription',
    idProperty: 'id',
    fields: [
        {name: 'id'},
        {name: 'filter'},
        {name: 'filterId'},
        {name: 'handler'}
    ]
});
