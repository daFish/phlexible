Ext.define('Phlexible.mediamanager.model.File', {
    extend: 'Ext.data.Model',

    entityName: 'File',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'path', type: 'string'},
        {name: 'volumeId', type: 'string'},
        {name: 'folderId', type: 'string'},
        {name: 'folderPath', type: 'string'},
        {name: 'hasVersions', type: 'bool'},
        {name: 'mimeType', type: 'string'},
        {name: 'mediaCategory', type: 'string'},
        {name: 'mediaType', type: 'string'},
        {name: 'mediaTypeTitle', type: 'string'},
        {name: 'size', type: 'int'},
        {name: 'hidden', type: 'bool'},
        {name: 'present', type: 'bool'},
        {name: 'version', type: 'int'},
        {name: 'createTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createUser', type: 'string'},
        {name: 'createUserId', type: 'string'},
        {name: 'modifyTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUser', type: 'string'},
        {name: 'modifyUserId', type: 'string'},
        {name: 'cache'},
        {name: 'meta'},
        {name: 'properties'},
        {name: 'usedIn'},
        {name: 'usageStatus', type: 'int'},
        {name: 'focal'},
        {name: 'attributes'}
    ]
});
