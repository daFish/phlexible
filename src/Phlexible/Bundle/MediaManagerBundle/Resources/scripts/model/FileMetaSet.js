Ext.define('Phlexible.mediamanager.model.FileMetaSet', {
    extend: 'Ext.data.TreeModel',

    entityName: 'FileMetaSet',
    childType: 'Phlexible.mediamanager.model.FileMeta',
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
