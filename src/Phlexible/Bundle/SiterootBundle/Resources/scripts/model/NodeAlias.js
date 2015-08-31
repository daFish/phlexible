Ext.define('Phlexible.site.model.NodeAlias', {
    extend: 'Ext.data.Model',

    //entityName: 'SiteNodeAlias',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'language', type: 'string'},
        {name: 'nodeId', type: 'int'},
        {name: 'siteId', reference: {
            type: 'Site',
            inverse: 'nodeAliases',
            persist: false
        }}
    ]
});
