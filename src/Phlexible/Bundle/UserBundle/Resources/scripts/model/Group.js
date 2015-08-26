/**
 * Group model
 */
Ext.define('Phlexible.user.model.Group', {
    extend: 'Ext.data.Model',
    idProperty: 'id',
    fields: [
        'id',
        'name',
        'comment',
        'readonly',
        'memberCnt',
        'members'
    ]
});