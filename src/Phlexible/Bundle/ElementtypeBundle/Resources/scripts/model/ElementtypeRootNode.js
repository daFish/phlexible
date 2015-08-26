Ext.define('Phlexible.elementtype.model.ElementtypeRootNode', {
    extend: 'Ext.data.TreeModel',
    entityName: 'ElementtypeRootNode',
    childType: 'Phlexible.elementtype.model.ElementtypeNode',
    fields: [{
        name: 'id',
        type: 'string'
    },{
        name: 'dsId',
        type: 'string'
    },{
        name: 'elementtypeId',
        type: 'string'
    },{
        name: 'revision',
        type: 'string'
    },{
        name: 'type',
        type: 'string'
    },{
        name: 'editable',
        type: 'bool'
    },{
        name: 'mappings'
    },{
        name: 'properties'
    }]
});