/**
 * Group model
 */
Ext.define('Phlexible.user.model.Group', {
    extend: 'Ext.data.Model',

    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'comment', type: 'string'},
        {name: 'memberCnt', type: 'integer', persist: false, calculate: function(data) {
            if (Ext.isArray(data.members)) {
                return data.members.length
            }
            return 0;
        }},
        {name: 'members', persist: false},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createdBy', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedBy', type: 'string'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_user_get_groups'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'groups',
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
                return {group: data};
            }
        }
    }
});
