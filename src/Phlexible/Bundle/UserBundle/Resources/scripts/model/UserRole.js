/**
 * User role model
 */
Ext.define('Phlexible.user.model.UserRole', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'name'},
        {name: 'member'}
    ]
});