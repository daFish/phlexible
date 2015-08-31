Ext.define('Phlexible.site.model.NodeConstraint', {
    extend: 'Ext.data.Model',

    //entityName: 'SiteNodeConstraint',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'allowed', type: 'boolean'},
        {name: 'nodeTypes'},
        {name: 'siteId', reference: {
            type: 'Site',
            inverse: 'nodeConstraints',
            persist: false
        }}
    ]
});
