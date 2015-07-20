Ext.provide('Phlexible.tree.model.Link');

Phlexible.tree.model.Link = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'iconCls', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'title', type: 'string'},
    {name: 'content', type: 'string'},
    {name: 'link', type: 'string'},
    {name: 'raw', type: 'string'}
]);
