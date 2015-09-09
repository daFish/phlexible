Ext.define('Phlexible.mediamanager.model.FolderMeta', {
    extend: 'Ext.data.TreeModel',
    entityName: 'FolderMeta',
    idProperty: 'key',

    fields: [
        {name: 'key', type: 'string'},
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
