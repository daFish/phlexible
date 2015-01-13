Ext.define('Phlexible.problems.ProblemsGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.problems-list',

    title: Phlexible.problems.Strings.problems,
    strings: Phlexible.problems.Strings,
    iconCls: Phlexible.Icon.get('exclamation'),

    loadMask: true,

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.problems.model.Problem',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('problems_list'),
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
                header: this.strings.id,
                dataIndex: 'id',
                hidden: true
            }, {
                header: this.strings.problem,
                dataIndex: 'msg',
                width: 400
            }, {
                header: this.strings.severity,
                dataIndex: 'severity',
                width: 80,
                renderer: function (v) {
                    return Phlexible.Icon.inline(Phlexible.problems.ProblemIcons[v]) + ' ' + v;
                }
            }, {
                header: this.strings.source,
                dataIndex: 'source'
            }, {
                header: 'createdAt',
                dataIndex: 'createdAt',
                width: 160
            }, {
                header: 'lastCheckedAt',
                dataIndex: 'lastCheckedAt',
                width: 160
            }, {
                header: this.strings.link,
                dataIndex: 'link',
                hidden: true
            }];

        this.plugins = [{
            ptype: 'rowexpander',
            dataIndex: 'hint',
            rowBodyTpl: [
                this.strings.solution + ': {hint}'
            ]
        }];
    }
});
