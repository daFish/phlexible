Ext.define('Phlexible.problem.model.Problem', {
    extend: 'Ext.data.Model',
    fields: [
        'id',
        'checkCass',
        'iconClass',
        'severity',
        'msg',
        'hint',
        'link',
        'createdAt',
        'lastCheckedAt',
        'isLive'
    ]
});