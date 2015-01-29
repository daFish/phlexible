Ext.define('Phlexible.problem.view.Problems', {
    extend: 'Ext.grid.GridPanel',
    requires: ['Phlexible.problem.view.ProblemsController'],
    xtype: 'problem.problems',
    controller: 'problem.problems',

    iconCls: Phlexible.Icon.get('exclamation'),
    loadMask: true,
    border: false,

    idtext: '_idtext',
    problemText: '_problemText',
    severityText: '_severityText',
    sourceText: '_sourceText',
    createdAtText: '_createdAtText',
    lastCheckedAtText: '_lastCheckedAtText',
    linkText: '_linkText',
    solutionText: '_solutionText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.problem.model.Problem',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('problem_list'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    idProperty: 'id'
                }
            },
            autoLoad: true
        });
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
                width: 400
            }, {
                header: this.severityText,
                dataIndex: 'severity',
                width: 80,
                renderer: function (v) {
                    return Phlexible.Icon.inline(Phlexible.problem.ProblemIcons[v]) + ' ' + v;
                }
            }, {
                header: this.sourceText,
                dataIndex: 'source'
            }, {
                header: this.createdAtText,
                dataIndex: 'createdAt',
                width: 160
            }, {
                header: this.lastCheckedAtText,
                dataIndex: 'lastCheckedAt',
                width: 160
            }, {
                header: this.linkText,
                dataIndex: 'link',
                hidden: true
            }];

        this.plugins = [{
            ptype: 'rowexpander',
            dataIndex: 'hint',
            rowBodyTpl: [
                this.solutionText + ': {hint}'
            ]
        }];
    }
});
