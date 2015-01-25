Ext.define('Phlexible.message.model.Subscription', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'id'},
        {name: 'filter'},
        {name: 'filterId'},
        {name: 'handler'}
    ]
});
