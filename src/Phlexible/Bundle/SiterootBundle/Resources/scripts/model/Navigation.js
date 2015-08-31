Ext.define('Phlexible.site.model.Navigation', {
    extend: 'Ext.data.Model',

    //entityName: 'SiteNavigation',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'nodeId', type: 'int'},
        {name: 'maxDepth', type: 'int'},
        {name: 'siteId', reference: {
            type: 'Site',
            inverse: 'navigations',
            persist: false
        }}
    ]
});
