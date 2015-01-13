Ext.define('Phlexible.problems.model.Problem', {
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