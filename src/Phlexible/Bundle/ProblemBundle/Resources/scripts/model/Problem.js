Ext.define('Phlexible.problem.model.Problem', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'iconCls',
        'msg',
        'hint',
        'severity',
        'link',
        'source',
        'createdAt',
        'lastCheckedAt'
    ]
});