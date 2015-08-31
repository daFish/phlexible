Ext.define('Phlexible.tree.model.LatestNodeChange', {
    extend: 'Ext.data.Model',

    id: 'nodeId',
    sortInfo: {field: 'modifieddAt', direction: 'DESC'},
    fields: [
        {name: 'nodeId', type: 'int'},
        {name: 'language', type: 'string'},
        {name: 'version', type: 'int'},
        {name: 'title', type: 'string'},
        {name: 'icon', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUserId', type: 'string'},
        {name: 'menu'}
    ]
});
