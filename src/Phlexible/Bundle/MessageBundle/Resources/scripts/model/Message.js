Ext.define('Phlexible.message.model.Message', {
    extend: 'Ext.data.Model',

    entityName: 'Message',
    idProperty: 'id',
    fields:[
        {name: 'id', type: 'string'},
        {name: 'subject', type: 'string'},
        {name: 'body', type: 'string'},
        {name: 'type', type: 'integer'},
        {name: 'channel', type: 'string'},
        {name: 'role', type: 'string'},
        {name: 'user', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'typeText', calculate: function(data) {
            return Phlexible.Config.get('message.types')[data.type];
        }},
        {name: 'typeIconCls', calculate: function(data) {
            return Phlexible.message.TypeIcons[data.type];
        }},
        {name: 'createdAtFormatted', calculate: function(data) {
            return Ext.Date.format(data.createdAt, 'Y-m-d H:i:s');
        }}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_message_get_messages'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'messages',
            totalProperty: 'count'
        }
    }
});
