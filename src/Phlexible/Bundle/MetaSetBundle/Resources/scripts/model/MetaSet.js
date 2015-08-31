Ext.define('Phlexible.metaset.model.MetaSet', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.metaset.model.MetaSetField'
    ],

    entityName: 'MetaSet',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'revision', type: 'integer'},
        {name: 'createdBy', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedBy', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_metaset_get_metasets'),
        reader: {
            type: 'json',
            rootProperty: 'metasets',
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
                data.modifiedAt = Ext.Date.format(new Date, "Y-m-d H:i:s");
                data.modifiedBy = Phlexible.User.getUsername();

                return {metaSet: data};
            }
        }
    }
});
