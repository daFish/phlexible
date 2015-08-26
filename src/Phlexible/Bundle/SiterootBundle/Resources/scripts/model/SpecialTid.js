Ext.define('Phlexible.siteroot.model.SpecialTid', {
    extend: 'Ext.data.Model',

    entityName: 'SiterootSpecialTid',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'language', type: 'string'},
        {name: 'treeId', type: 'int'},
        {name: 'siterootId', reference: {
            type: 'Siteroot',
            inverse: 'specialTids'
        }}
    ]
});