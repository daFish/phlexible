Ext.define('Phlexible.mediatemplate.model.MediaTemplate', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'key', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'cache', type: 'boolean'},
        {name: 'system', type: 'boolean'},
        {name: 'storage', type: 'string'},
        {name: 'revision', type: 'integer'},
        {name: 'parameters'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ]
});
