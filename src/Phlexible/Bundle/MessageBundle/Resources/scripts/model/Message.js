Ext.define('Phlexible.message.model.Message', {
    extend: 'Ext.data.Model',

    fields:[
        {name: 'id', type: 'string'},
        {name: 'subject', type: 'string'},
        {name: 'body', type: 'string'},
        {name: 'priority', type: 'integer'},
        {name: 'type', type: 'integer'},
        {name: 'channel', type: 'string'},
        {name: 'role', type: 'string'},
        {name: 'user', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ]
});
