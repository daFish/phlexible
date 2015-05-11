Ext.define('Phlexible.siteroot.model.Url', {
    extend: 'Ext.data.Model',

    entityName: 'SiterootUrl',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'globalDefault', type: 'boolean'},
        {name: 'default', type: 'boolean'},
        {name: 'hostname', type: 'string'},
        {name: 'language', type: 'string'},
        {name: 'target', type: 'string'},
        {name: 'siterootId', reference: {
            type: 'Siteroot',
            inverse: 'urls'
        }}
    ]
});