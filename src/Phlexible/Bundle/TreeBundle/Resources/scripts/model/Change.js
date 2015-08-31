Ext.define('Phlexible.tree.model.Change', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'id', type: 'int'},
        {name: 'nodeId', type: 'int'},
        {name: 'type', type: 'string'},
        {name: 'version', type: 'int'},
        {name: 'language', type: 'string'},
        {name: 'comment', type: 'string'},
        {name: 'action', type: 'string'},
        {name: 'username', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ]
});
