Ext.define('Phlexible.problem.portlet.Problems', {
    extend: 'Ext.dashboard.Panel',
    alias: 'widget.problems-portlet',

    iconCls: Phlexible.Icon.get('exclamation'),
    bodyPadding: 5,

    imageUrl: '/bundles/phlexibleproblem/images/portlet-problems.png',

    solutionText: '_solutionText',
    menuHintText: '_menuHintText',
    noProblemsText: '_noProblemsText',
    newProblemText: '_newProblemText',

    initComponent: function () {
        this.initMyTemplate();
        this.initMyStore();
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyTemplate: function() {
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div id="portal_problems_{id}" class="portlet-problem">',
            '<div class="p-problem-icon {iconCls}"></div>',
            '<div class="p-problem-severity p-problem-severity_{severity}-icon" ></div>',
            '<div class="p-problem-text">',
            '<span class="p-problem-message">{msg}</span>',
            '<tpl if="hint">',
            '<br />',
            '<span class="p-problem-solve">' + this.solutionText + ': {hint}</span>',
            '</tpl>',
            '</div>',
            '<div class="x-clear-both"></div>',
            '</div>',
            '</tpl>',
            '<div><hr />' + this.menuHintText + '</div>'
        );
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.problem.model.Problem',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json',
                    idProperty: 'id',
                }
            },
            sorters: [{property: 'severity', username: 'ASC'}],
            data: this.item.data
        });
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.portlet-problem',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.noProblemsText,
                deferEmptyText: false,
                autoHeight: true,
                store: this.store,
                tpl: this.tpl,
                listeners: {
                    click: function (view, index) {
                        var r = view.store.getAt(index);

                        var link = r.get('link');

                        if (link && link.handler) {
                            var handler = link.handler;
                            if (typeof handler == 'string') {
                                handler = Phlexible.evalClassString(handler);
                            }
                            handler(link);
                        }
                    },
                    scope: this
                }
            }
        ];
    },

    updateData: function (data) {
        var problemsMap = [];

        for (var i = 0; i < data.length; i++) {
            var row = data[i];
            problemsMap.push(row.id);
            var r = this.store.getById(row.id);
            if (!r) {
                this.store.add(new Phlexible.problem.model.Problem(row));

                Ext.fly('portal_problems_' + row.id).frame('#8db2e3', 1);
            }
        }

        for (var i = this.store.getCount() - 1; i > 0; i--) {
            var r = this.store.getAt(i);
            if (problemsMap.indexOf(r.id) == -1) {
                this.store.remove(r);
            }
        }

        if (!this.store.getCount()) {
            this.store.removeAll();
        }

        this.store.sort('type', 'ASC');
    }
});
