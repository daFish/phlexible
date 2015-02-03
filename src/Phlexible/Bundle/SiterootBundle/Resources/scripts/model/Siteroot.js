Ext.define('Phlexible.siteroot.model.Siteroot', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.siteroot.model.Url',
        'Phlexible.siteroot.model.Navigation',
        'Phlexible.siteroot.model.SpecialTid'
    ],

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
        {name: 'specialTids'},
        {name: 'properties'},
        {name: 'navigations'},
        {name: 'urls'}
    ],
    hasMany: [
        {model: 'Phlexible.siteroot.model.Url', name: 'urls'},
        {model: 'Phlexible.siteroot.model.Navigation', name: 'navigations'},
        {model: 'Phlexible.siteroot.model.SpecialTid', name: 'specialTids'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_siteroot_get_siteroots'),
        reader: {
            type: 'json',
            rootProperty: 'siteroots',
            idProperty: 'id',
            totalProperty: 'count'
        }
    }
});
