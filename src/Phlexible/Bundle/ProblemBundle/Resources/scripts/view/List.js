Ext.define('Phlexible.problem.view.List', {
    extend: 'Ext.grid.Panel',
    xtype: 'problem.list',

    iconCls: Phlexible.Icon.get('exclamation'),
    loadMask: true,

    idText: '_idtext',
    problemText: '_problemText',
    severityText: '_severityText',
    sourceText: '_sourceText',
    createdAtText: '_createdAtText',
    lastCheckedAtText: '_lastCheckedAtText',
    linkText: '_linkText',
    solutionText: '_solutionText',

    initComponent: function () {
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                dataIndex: 'id',
                hidden: true
            }, {
                header: this.problemText,
                dataIndex: 'msg',
                flex: 1
            }, {
                header: this.severityText,
                dataIndex: 'severity',
                width: 80,
                renderer: function (v) {
                    return Phlexible.Icon.inline(Phlexible.problem.ProblemIcons[v]) + ' ' + v;
                }
            }, {
                header: this.sourceText,
                dataIndex: 'source',
                hidden: true
            }, {
                xtype: 'datecolumn',
                header: this.createdAtText,
                dataIndex: 'createdAt',
                width: 120,
                format: 'Y-m-d H:i:s'
            }, {
                xtype: 'datecolumn',
                header: this.lastCheckedAtText,
                dataIndex: 'lastCheckedAt',
                width: 120,
                format: 'Y-m-d H:i:s'
            }, {
                header: this.linkText,
                dataIndex: 'link',
                hidden: true
            }
        ];
    }
});
