Ext.define('Phlexible.tasks.model.Comment', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'current_state'},
        {name: 'comment'},
        {name: 'create_user'},
        {name: 'create_date'}
    ]
});
