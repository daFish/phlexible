/**
 * User model
 */
Ext.define('Phlexible.user.model.User', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'username', type: 'string'},
        {name: 'email', type: 'string'},
        {name: 'emailHash', type: 'string'},
        {name: 'firstname', type: 'string'},
        {name: 'lastname', type: 'string'},
        {name: 'comment', type: 'string'},
        {name: 'expired', type: 'boolean'},
        {name: 'expiresAt', type: 'string'},
        {name: 'disabled', type: 'boolean'},
        {name: 'properties'},
        {name: 'roles'},
        {name: 'createDate'},
        {name: 'createUser', type: 'string'},
        {name: 'modifyDate'},
        {name: 'modifyUser', type: 'string'},
        {name: 'extra'}
    ]
});