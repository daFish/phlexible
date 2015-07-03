Ext.define('Phlexible.problem.model.Problem', {
    extend: 'Ext.data.Model',

    entityName: 'Problem',
    idProperty: 'id',
    fields: [
        {name: 'id', type: 'string'},
        {name: 'severity', type: 'string'},
        {name: 'message', type: 'string'},
        {name: 'hint', type: 'string'},
        {name: 'attributes'},
        {name: 'createdAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'lastCheckedAt', type: 'date', dateFormat: 'Y-m-d H:i:s'},
        {name: 'isLive', type: 'bool'}
    ],
    proxy: {
        type: 'rest',
        url: Phlexible.Router.generate('phlexible_api_problem_get_problems'),
        simpleSortMode: true,
        reader: {
            type: 'json',
            rootProperty: 'problems',
            totalProperty: 'count'
        }
    }
});
