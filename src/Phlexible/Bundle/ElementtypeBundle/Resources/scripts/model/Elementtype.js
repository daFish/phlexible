Ext.define('Phlexible.elementtype.model.Elementtype', {
    extend: 'Ext.data.Model',
    entityName: 'Elementtype',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'uniqueId', type: 'string'},
        {name: 'revision', type: 'int'},
        {name: 'type', type: 'string'},
        {name: 'titles'},
        {name: 'icon', type: 'string'},
        {name: 'defaultTab', type: 'string'},
        {name: 'hideChildren', type: 'bool'},
        {name: 'deleted', type: 'bool'},
        {name: 'comment', type: 'string'},
        {name: 'defaultContentTab', type: 'string'},
        {name: 'metaSetId', type: 'string'},
        {name: 'template', type: 'string'},
        {name: 'mappings'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'createUser', type: 'string'},
        {name: 'modifiedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'modifyUser', type: 'string'},
        {name: 'new', type: 'bool', defaultValue: false},
        {
            name: 'title',
            calculate: function (data) {
                return data.titles.de;
            }
        }
    ]
});
