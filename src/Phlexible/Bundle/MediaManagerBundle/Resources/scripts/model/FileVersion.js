Ext.define('Phlexible.mediamanager.FileVersion', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'folderId', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'size', type: 'int'},
        {name: 'version', type: 'int'},
        {name: 'documentTypeKey', type: 'string'},
        {name: 'assetType', type: 'string'},
        {name: 'createUserId', type: 'string'},
        {name: 'createTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    ]
});