Ext.define('Phlexible.mediamanager.model.LatestFile', {
    extend: 'Ext.data.Model',
    file: [
        {name: 'id', type: 'string'},
        {name: 'fileId', type: 'string'},
        {name: 'fileVersion', type: 'int'},
        {name: 'folderId', type: 'string'},
        {name: 'folderPath', type: 'string'},
        {name: 'mediaType', type: 'string'},
        {name: 'mediaTypeTitle', type: 'string'},
        {name: 'time', type: 'date'},
        {name: 'title', type: 'string'},
        {name: 'cache'}
    ]
});
