Ext.define('Phlexible.queue.model.Job', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'command',
        'priority',
        'status',
        'create_time',
        'start_time',
        'end_time',
        'output'
    ]
});