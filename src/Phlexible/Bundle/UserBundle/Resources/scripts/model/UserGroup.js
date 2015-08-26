/**
 * User group model
 */
Ext.define('Phlexible.user.model.UserGroup', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id'},
        {name: 'group'},
        {name: 'member'}
    ]
});