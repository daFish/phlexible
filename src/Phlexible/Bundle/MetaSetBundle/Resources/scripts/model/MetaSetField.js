Ext.define('Phlexible.metaset.model.MetaSetField', {
    extend: 'Ext.data.Model',

    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'options', type: 'string'},
        {name: 'synchronized', type: 'boolean'},
        {name: 'readonly', type: 'boolean'},
        {name: 'required', type: 'boolean'}
    ]
});
