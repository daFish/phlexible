Ext.define('Phlexible.mediamanager.model.Folder', {
    extend: 'Ext.data.TreeModel',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'path', type: 'string'},
        {name: 'hasVersions', type: 'bool'},
        {name: 'volumeId', type: 'string'},
        {name: 'createTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createUser', type: 'string'},
        {name: 'createUserId', type: 'string'},
        {name: 'modifyTime', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUser', type: 'string'},
        {name: 'modifyUserId', type: 'string'},
        {name: 'usageStatus', type: 'int'},
        {name: 'usedIn'},
        {name: 'attributes'},
        {name: 'rights'}
    ]
});
