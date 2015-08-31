Ext.define('Phlexible.tree.model.Node', {
    extend: 'Ext.data.TreeModel',

    entityName: 'Node',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'integer'},
        {name: 'workspace', type: 'string'},
        {name: 'locale', type: 'string'},
        {name: 'siterootId', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'typeId', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'navigationTitle', type: 'string'},
        {name: 'backendTitle', type: 'string'},
        {name: 'slug', type: 'string'},
        {name: 'customDate', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'forward', type: 'string'},
        {name: 'sortMode', type: 'string'},
        {name: 'sortDir', type: 'string'},
        {name: 'sort', type: 'int'},
        {name: 'icon', type: 'string'},
        {name: 'inNavigation', type: 'bool'},
        {name: 'isRestricted', type: 'bool'},
        {name: 'isInstance', type: 'bool'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createdBy', type: 'string'},
        {name: 'isPublished', type: 'bool'},
        {name: 'publishedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'pbulishedBy', type: 'string'},
        {name: 'isAsync', type: 'bool'},
        {name: 'publishedVersion', type: 'int'},
        {name: 'allowedChildren'},
        {name: 'permissions'},
        {name: 'hideChildren', type: 'bool'},
        {name: 'elementtypeId', type: 'string'},
        {name: 'elementtypeName', type: 'string'},
        {name: 'elementtypeType', type: 'string'}
    ],
    proxy: {
        type: 'ajax',
        url: Phlexible.Router.generate('tree_nodes'),
        simpleSortMode: true,
        remoteSort: true,
        reader: {
            type: 'json'
            //rootProperty: 'sites',
            //totalProperty: 'total'
        }
    }
});
