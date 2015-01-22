Ext.define('Phlexible.task.model.Transition', {
    extend: 'Ext.data.Model',
    fields: [
        {name: 'id'},
        {name: 'name'},
        {name: 'old_state'},
        {name: 'new_state'},
        {name: 'create_user'},
        {name: 'create_date'}
    ]
});
