Ext.define('Phlexible.mediamanager.model.LatestFile', {
    extend: 'Ext.data.Model',
    entityName: 'LatestFile',
    idProperty: 'id',
    file: [
        {name: 'id', type: 'string'},
        {name: 'fileId', type: 'string'},
        {name: 'fileVersion', type: 'int'},
        {name: 'folderId', type: 'string'},
        {name: 'folderPath', type: 'string'},
        {name: 'mediaType', type: 'string'},
        {name: 'mediaTypeTitle', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'name', type: 'string'},
        {name: 'cache'}
    ]
});
