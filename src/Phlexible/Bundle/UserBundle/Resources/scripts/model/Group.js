/**
 * Group model
 */
Ext.define('Phlexible.user.model.Group', {
    extend: 'Ext.data.Model',
    fields: [
        'gid',
        'name',
        'comment',
        'readonly',
        'memberCnt',
        'members'
    ]
});