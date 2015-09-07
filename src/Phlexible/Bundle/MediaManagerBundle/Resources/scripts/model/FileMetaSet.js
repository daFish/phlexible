Ext.define('Phlexible.mediamanager.model.FileMetaSet', {
    extend: 'Ext.data.TreeModel',
    entityName: 'FileMetaSet',
    childType: 'Phlexible.mediamanager.model.FileMeta',
    idProperty: 'id',

    fields: [{
        name: 'id',
        type: 'string'
    },{
        name: 'name',
        calculate: function (data) {
            return data.title;
        },
        depends: 'title'
    },{
        name: 'title',
        type: 'string'
    },{
        name: 'iconCls',
        type: 'string',
        defaultValue: 'p-icon-weather-clouds'
    }],
    proxy: {
        type: 'rest',
        reader: {
            type: 'json',
            typeProperty: 'mtype'
        },
        extraParams: {}
    }
});
