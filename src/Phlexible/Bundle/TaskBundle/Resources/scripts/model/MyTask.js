Ext.define('Phlexible.tasks.model.MyTask', {
    extend: 'Ext.data.Model',
    fields:[
        {name: 'id', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'text', type: 'string'},
        {name: 'status', type: 'string'},
        {name: 'create_uid', type: 'string'},
        {name: 'create_user', type: 'string'},
        {name: 'create_date', type: 'string'}
    ]
});
