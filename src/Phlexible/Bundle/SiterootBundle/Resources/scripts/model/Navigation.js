Ext.define('Phlexible.siteroot.model.Navigation', {
    extend: 'Ext.data.Model',

    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'handler', type: 'string'},
        {name: 'startTreeId', type: 'int'},
        {name: 'maxDepth', type: 'int'},
        {name: 'flags', type: 'int'},
        {name: 'additional'}
    ]
});
