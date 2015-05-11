Ext.define('Phlexible.elementtype.model.ElementtypeNode', {
    extend: 'Ext.data.TreeModel',
    entityName: 'ElementtypeNode',
    childType: 'Phlexible.elementtype.model.ElementtypeNode',
    fields: [{
        name: 'id',
        type: 'string'
    },{
        name: 'dsId',
        type: 'string'
    },{
        name: 'type',
        type: 'string'
    },{
        name: 'editable',
        type: 'bool'
    },{
        name: 'reference',
        type: 'bool'
    },{
        name: 'invalid',
        type: 'bool'
    },{
        name: 'properties'
    },{
        name: 'configuration'
    },{
        name: 'labels'
    },{
        name: 'validation'
    },{
        name: 'iconCls',

        calculate: function (data) {
            if (!data.type || !Phlexible.fields.FieldTypes.get(data.type)) {
                return '';
            }

            return Phlexible.fields.FieldTypes.get(data.type).iconCls;
        }
    }]
});