Ext.define('Phlexible.message.model.Message', {
    extend: 'Ext.data.Model',

    fields:[
        {name: 'id'},
        {name: 'subject'},
        {name: 'body'},
        {name: 'priority'},
        {name: 'type'},
        {name: 'channel'},
        {name: 'role'},
        {name: 'user'},
        {name: 'createdAt'}
    ]
});
