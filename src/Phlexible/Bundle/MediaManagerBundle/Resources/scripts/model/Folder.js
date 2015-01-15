Ext.define('Phlexible.mediamanager.model.Folder', {
    extend: 'Ext.data.TreeModel',
    fields: [
        {name: 'rights'},
        {name: 'volumeId', type: 'string'},
        {name: 'versions', type: 'bool'},
        {name: 'usedId'}
    ]
});
