Ext.define('Phlexible.mediamanager.model.FileVersion', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'folderId', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'size', type: 'int'},
        {name: 'version', type: 'int'},
        {name: 'mediaType', type: 'string'},
        {name: 'mediaCategory', type: 'string'},
        {name: 'createUserId', type: 'string'},
        {name: 'createTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    ]
});