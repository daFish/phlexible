Ext.define('Phlexible.mediamanager.model.LatestFile', {
    extend: 'Ext.data.Model',
    file: [
        {name: 'id', type: 'string'},
        {name: 'file_id', type: 'string'},
        {name: 'file_version', type: 'int'},
        {name: 'folder_id', type: 'string'},
        {name: 'folder_path', type: 'string'},
        {name: 'document_type_key', type: 'string'},
        {name: 'time', type: 'date'},
        {name: 'title', type: 'string'},
        {name: 'cache'}
    ]
});
