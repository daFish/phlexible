Ext.define('Phlexible.mediamanager.model.FileMeta', {
    extend: 'Ext.data.TreeModel',

    entityName: 'FileMeta',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'options', type: 'string'},
        {name: 'required', type: 'bool'},
        {name: 'synchronized', type: 'bool'},
        {name: 'readonly', type: 'bool'},
        {name: 'values'}
    ],
    proxy: {
        type: 'rest-filter',
        reader: {
            type: 'json',
            rootProperty: 'meta'
        }
    }
});
