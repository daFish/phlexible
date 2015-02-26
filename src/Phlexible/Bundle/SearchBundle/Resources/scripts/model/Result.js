Ext.define('Phlexible.search.model.Result', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'image'},
        {name: 'title'},
        {name: 'author'},
        {name: 'date', type: 'date', dateFormat: 'timestamp'},
        {name: 'component'},
        {name: 'handler'}
    ]
});
