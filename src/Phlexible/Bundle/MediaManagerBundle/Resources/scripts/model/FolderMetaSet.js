Ext.define('Phlexible.mediamanager.model.FolderMetaSet', {
    extend: 'Ext.data.TreeModel',
    entityName: 'FolderMetaSet',
    childType: 'Phlexible.mediamanager.model.FolderMeta',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'key', type: 'string'},
        {name: 'title', type: 'string'}
    ]
});
