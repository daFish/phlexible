Ext.define('Phlexible.metaset.model.MetaSet', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.metaset.model.MetaSetField'
    ],

    entityName: 'MetaSet',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'revision', type: 'integer'},
        {name: 'createUser', type: 'string'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUser', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_metaset_get_metasets'),
        reader: {
            type: 'json',
            rootProperty: 'metasets',
            totalProperty: 'count'
        }
    }
});
