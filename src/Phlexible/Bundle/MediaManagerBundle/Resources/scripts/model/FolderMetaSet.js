Ext.define('Phlexible.mediamanager.model.FolderMetaSet', {
    extend: 'Ext.data.TreeModel',

    entityName: 'FolderMetaSet',
    childType: 'Phlexible.mediamanager.model.FolderMeta',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'}
    ],
    proxy: {
        type: 'rest-filter',
        reader: {
            type: 'json',
            rootProperty: 'metasets'
        },
        writer: {
            type: 'json',
            allDataOptions: {
                persist: true,
                associated: true
            },
            partialDataOptions: {
                persist: true,
                changes: false,
                critical: true,
                associated: true
            },
            writeRecordId: false,
            transform: function(data, request) {
                return {set: data};
            }
        }
    }
});
