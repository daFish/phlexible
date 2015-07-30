Ext.provide('Phlexible.tree.model.Version');

Phlexible.tree.model.Version = Ext.data.Record.create([
    {name: 'version', type: 'int'},
    {name: 'format', type: 'int'},
    {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
    {name: 'isPublished', type: 'boolean'},
    {name: 'wasPublished', type: 'boolean'}
]);
