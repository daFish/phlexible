Ext.define('Phlexible.metaset.model.MetaSetField', {
    extend: 'Ext.data.Model',
    requires: [
        'Phlexible.metaset.validator.Options'
    ],

    entityName: 'MetaSetField',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'name', type: 'string'},
        {name: 'type', type: 'string'},
        {name: 'options', type: 'string'},
        {name: 'synchronized', type: 'boolean'},
        {name: 'readonly', type: 'boolean'},
        {name: 'required', type: 'boolean'},
        {name: 'metaSetId', reference: {
            type: 'MetaSet',
            inverse: 'fields',
            persist: false
        }}
    ],
    validators: {
        options: { type: 'metaset-options' }
    }
});
