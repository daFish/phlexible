Ext.provide('Phlexible.elementtypes.model.Elementtype');

Phlexible.elementtypes.model.Elementtype = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'icon', type: 'string'},
    {name: 'name', type: 'string'},
    {name: 'version', type: 'string'},
    {name: 'type', type: 'string'}
]);
