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
        {name: 'mediaType', type: 'string'},
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
        {name: 'attributes'},
        {name: 'mediaTypeTitle', calculate: function(data) {
            return data.mediaType;
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_mediamanager_get_files'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'files',
            idProperty: 'id',
            totalProperty: 'total'
        },
        extraParams: {
            limit: 30//Phlexible.Config.get('mediamanager.files.num_files', 10),
            //filter: Ext.encode(this.activeFilter)
        }
    }
});
