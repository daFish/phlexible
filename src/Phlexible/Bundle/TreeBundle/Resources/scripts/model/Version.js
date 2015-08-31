Ext.define('Phlexible.tree.model.Version', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'version', type: 'int'},
        {name: 'format', type: 'int'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'isPublished', type: 'boolean'},
        {name: 'wasPublished', type: 'boolean'}
    ]
});
