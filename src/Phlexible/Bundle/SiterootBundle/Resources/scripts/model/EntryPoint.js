Ext.define('Phlexible.site.model.EntryPoint', {
    extend: 'Ext.data.Model',

    //entityName: 'SiteEntryPoint',
    fields: [
        {name: 'name', type: 'string'},
        {name: 'hostname', type: 'string'},
        {name: 'nodeId', type: 'integer'},
        {name: 'language', type: 'string'},
        {name: 'siteId', reference: {
            type: 'Site',
            inverse: 'entryPoints',
            persist: false
        }}
    ]
});
