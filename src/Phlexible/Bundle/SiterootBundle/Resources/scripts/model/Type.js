Ext.provide('Phlexible.siteroots.model.Type');

Phlexible.siteroots.model.Type = Ext.data.Record.create([
    {name: 'name', type: 'string'},
    {name: 'icon', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'allowed', type: 'boolean'},
    {name: 'nodeTypes'}
]);
