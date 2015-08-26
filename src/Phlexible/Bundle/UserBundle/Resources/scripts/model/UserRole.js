/**
 * User role model
 */
Ext.define('Phlexible.user.model.UserRole', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: [
        {name: 'id'},
        {name: 'role'},
        {name: 'member'}
    ]
});