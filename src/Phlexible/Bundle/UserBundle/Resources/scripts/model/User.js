/**
 * User model
 */
Ext.define('Phlexible.user.model.User', {
    extend: 'Ext.data.Model',

    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'firstname', type: 'string'},
        {name: 'lastname', type: 'string'},
        {name: 'comment', type: 'string'},
        {name: 'username', type: 'string'},
        {name: 'email', type: 'string'},
        {name: 'plainPassword', type: 'string'},
        {name: 'confirmationToken', type: 'string'},
        {name: 'expired', type: 'boolean'},
        {name: 'enabled', type: 'boolean'},
        {name: 'locked', type: 'boolean'},
        {name: 'properties'},
        {name: 'roles'},
        {name: 'groups'},
        {name: 'credentialsExpired', type: 'boolean'},
        {name: 'credentialsExpireAt', type: 'date'},
        {name: 'passwordRequestedAt', type: 'date'},
        {name: 'lastLogin', type: 'date'},
        {name: 'expiresAt', type: 'date'},
        {name: 'createdAt', type: 'date'},
        //{name: 'createUser', type: 'string'},
        {name: 'modifiedAt', type: 'date'},
        //{name: 'modifyUser', type: 'string'},
        {name: 'extra'},
        {name: 'notify', default: false, persist: false}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_user_get_users'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'users',
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
                data.modifiedAt = new Date();

                return {user: data};
            }
        }
    }
});
