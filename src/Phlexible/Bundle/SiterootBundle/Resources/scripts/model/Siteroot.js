Ext.define('Phlexible.siteroot.model.Siteroot', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.siteroot.model.Url',
        'Phlexible.siteroot.model.Navigation',
        'Phlexible.siteroot.model.SpecialTid'
    ],

    entityName: 'Siteroot',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'default', type: 'boolean'},
        {name: 'patterns'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createUserId', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUserId', type: 'string'},
        {name: 'titles'},
        {name: 'properties'},
        {name: 'title', type: 'string', calculate: function(data) {
            return data.titles.de;
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_siteroot_get_siteroots'),
        reader: {
            type: 'json',
            rootProperty: 'siteroots',
            totalProperty: 'count'
        }
    }
});
