Ext.define('Phlexible.siteroot.model.Url', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'global_default', type: 'boolean'},
        {name: 'default', type: 'boolean'},
        {name: 'hostname'},
        {name: 'language'},
        {name: 'target'}
    ]
});