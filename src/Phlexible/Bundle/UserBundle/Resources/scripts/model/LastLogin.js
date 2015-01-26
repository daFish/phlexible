/**
 * Last login model
 */
Ext.define('Phlexible.user.model.LastLogin', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'userId', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'loginTimestamp', type: 'string'},
        {name: 'loginSeconds', type: 'string'},
        {name: 'emailHash', type: 'string'}
    ]
});