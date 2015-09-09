Ext.define('Phlexible.mediamanager.model.FileMeta', {
    extend: 'Ext.data.TreeModel',
    entityName: 'FileMeta',
    idProperty: 'key',


    fields: [{
        name: 'name',
        calculate: function (data) {
            return data.key;
        },
        depends: 'key'
    },{
        name: 'key',
        type: 'string'
    },{
        name: 'type',
        type: 'string'
    },{
        name: 'options',
        type: 'string'
    },{
        name: 'required',
        type: 'bool'
    },{
        name: 'synchronized',
        type: 'bool'
    },{
        name: 'readonly',
        type: 'bool'
    },{
        name: 'values'
    },{
        name: 'iconCls',
        type: 'string',
        defaultValue: 'p-icon-weather-cloud'
    }],
    proxy: {
        type: 'rest-filter',
        reader: {
            type: 'json',
            rootProperty: 'meta'
        }
    }
});
