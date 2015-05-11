Ext.define('Phlexible.message.model.Filter', {
    extend: 'Ext.data.Model',

    entityName: 'Filter',
    idProperty: 'id',
    fields:[
        {name: 'id'},
        {name: 'title'},
        {name: 'criteria'}
    ]
});
