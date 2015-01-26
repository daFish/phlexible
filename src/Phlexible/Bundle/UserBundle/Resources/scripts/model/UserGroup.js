/**
 * User group model
 */
Ext.define('Phlexible.user.model.UserGroup', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'group'},
        {name: 'member'}
    ]
});