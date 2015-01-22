Ext.define('Phlexible.task.model.Comment', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'current_state'},
        {name: 'comment'},
        {name: 'create_user'},
        {name: 'create_date'}
    ]
});
