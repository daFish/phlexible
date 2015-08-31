Ext.define('Phlexible.mediatemplate.model.MediaTemplate', {
    extend: 'Ext.data.Model',

    idProperty: 'key',
    fields: [
        {name: 'key', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'cache', type: 'boolean'},
        {name: 'system', type: 'boolean'},
        {name: 'storage', type: 'string'},
        {name: 'revision', type: 'integer'},
        {name: 'parameters'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_mediatemplate_get_mediatemplates'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: function(raw) {
                return raw.mediaTemplates ? raw.mediaTemplates : raw;
            },
            totalProperty: 'total',
            typeProperty: 'type'
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
            writeRecordId: true,
            transform: function(data, request) {
                // do some manipulation of the unserialized data object
                data.modifiedAt = Ext.Date.format(new Date, "Y-m-d H:i:s");

                return {mediaTemplate: data};
            }
        }
    }
});
