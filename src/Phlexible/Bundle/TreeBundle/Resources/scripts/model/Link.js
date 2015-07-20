Ext.provide('Phlexible.tree.model.Link');

Phlexible.tree.model.Link = Ext.data.Record.create([
    {name: 'id', type: 'string'},
    {name: 'type', type: 'string'},
    {name: 'language', type: 'string'},
    {name: 'version', type: 'int'},
    {name: 'field', type: 'string'},
    {name: 'target', type: 'string'},
    {name: 'iconCls', type: 'string'},
    {name: 'link', type: 'string'}
]);
