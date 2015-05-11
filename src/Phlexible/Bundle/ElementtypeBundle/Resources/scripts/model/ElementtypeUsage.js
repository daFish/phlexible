Ext.define('Phlexible.elementtype.model.ElementtypeUsage', {
    extend: 'Ext.data.Model',
    entityName: 'ElementtypeUsage',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'title', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'latest_version', type: 'string'},
        {name: 'as', type: 'string'}
    ]
});