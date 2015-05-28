Ext.provide('Phlexible.mediamanager.model.File');

Phlexible.mediamanager.model.File = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'name', type: 'string'},
    {name: 'site_id', type: 'string'},
    {name: 'folder_id', type: 'string'},
    {name: 'folder', type: 'string'},
    {name: 'mime_type', type: 'string'},
    {name: 'media_type', type: 'string'},
    {name: 'hash', type: 'string'},
    {name: 'size', type: 'int'},
    {name: 'hidden', type: 'bool'},
    {name: 'present', type: 'bool'},
    {name: 'version', type: 'int'},
    {name: 'create_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'create_user', type: 'string'},
    {name: 'create_user_id', type: 'string'},
    {name: 'modify_time', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'modify_user', type: 'string'},
    {name: 'modify_user_id', type: 'string'},
    {name: 'cache'},
    {name: 'properties'},
    {name: 'used_in'},
    {name: 'used', type: 'int'},
    {name: 'focal'},
    {name: 'attributes'}
]);
