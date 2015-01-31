/**
 * User model
 */
Ext.define('Phlexible.user.model.User', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'firstname', type: 'string'},
        {name: 'lastname', type: 'string'},
        {name: 'comment', type: 'string'},
        {name: 'username', type: 'string'},
        {name: 'email', type: 'string'},
        {name: 'salt', type: 'string'},
        {name: 'plainPassword', type: 'string'},
        {name: 'confirmationToken', type: 'string'},
        {name: 'expired', type: 'boolean'},
        {name: 'enabled', type: 'boolean'},
        {name: 'locked', type: 'boolean'},
        {name: 'accountNonExpired', type: 'boolean'},
        {name: 'properties'},
        {name: 'roles'},
        {name: 'groups'},
        {name: 'passwordRequestedAt', type: 'date'},
        {name: 'lastLogin', type: 'date'},
        {name: 'expiresAt', type: 'date'},
        {name: 'createdAt', type: 'date'},
        {name: 'createUser', type: 'string'},
        {name: 'modifiedAt', type: 'date'},
        {name: 'modifyUser', type: 'string'},
        {name: 'extra'}
    ]
});