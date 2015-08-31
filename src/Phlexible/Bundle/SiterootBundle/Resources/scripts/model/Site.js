Ext.define('Phlexible.site.model.Site', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.site.model.EntryPoint',
        'Phlexible.site.model.Navigation',
        'Phlexible.site.model.NodeAlias',
        'Phlexible.site.model.NodeConstraint'
    ],

    entityName: 'Site',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'default', type: 'boolean'},
        {name: 'hostname', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createdBy', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedBy', type: 'string'},
        {name: 'titles'},
        {name: 'properties'},
        {name: 'title', type: 'string', persist: false, calculate: function(data) {
            return data.titles.de || data.hostname;
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_site_get_sites'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'sites',
            totalProperty: 'total'
        },
        writer: {
            type: 'json',
            allDataOptions: {
                persist: true,
                associated: true
            },
            partialDataOptions: {
                persist: true,
                changes: false,
                critical: true,
                associated: true
            },
            writeRecordId: false,
            transform: function(data, request) {
                // do some manipulation of the unserialized data object
                data.modifiedAt = new Date();
                data.modifiedBy = Phlexible.User.getUsername();

                return {site: data};
            }
        }
    }
});
