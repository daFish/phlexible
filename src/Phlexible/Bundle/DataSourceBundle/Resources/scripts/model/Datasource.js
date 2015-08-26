Ext.define('Phlexible.datasource.model.Datasource', {
    extend: 'Ext.data.Model',
    entityName: 'Datasource',
    field: [
        {name: 'id', type: 'string'},
        {name: 'title', type: 'string'}
    ]
});