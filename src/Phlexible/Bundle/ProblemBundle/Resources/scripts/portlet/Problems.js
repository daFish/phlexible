Ext.define('Phlexible.problems.portlet.Problems', {
    extend: 'Portal.view.Portlet',
    alias: 'widget.problems-portlet',

    title: Phlexible.problems.Strings.problems,
    strings: Phlexible.problems.Strings,
    bodyStyle: 'padding: 5px 5px 5px 5px',
    iconCls: Phlexible.Icon.get('exclamation'),

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
            '<div class="p-problems-icon {iconCls}"></div>',
            '<div class="p-problems-severity p-problems-severity_{severity}-icon" ></div>',
            '<div class="p-problems-text">',
            '<span class="p-problems-message">{msg}</span>',
            '<tpl if="hint">',
            '<br />',
            '<span class="p-problems-solve">' + this.strings.solution + ': {hint}</span>',
            '</tpl>',
            '</div>',
            '<div class="x-clear-both"></div>',
            '</div>',
            '</tpl>',
            '<div><hr />' + this.strings.menu_hint + '</div>'
        );
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.SimpleStore', {
            model: 'Phlexible.problems.model.Problem',
            id: 'id',
            sortInfo: {field: 'severity', username: 'ASC'}
        });

        var data = this.record.get('data');
        if (data) {
            Ext.each(data, function (item) {
                this.add(new Phlexible.problems.portlet.ProblemRecord(item, item.id));
            }, this.store);
        }
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'dataview',
                itemSelector: 'div.portlet-problem',
                style: 'overflow: auto',
                singleSelect: true,
                emptyText: this.strings.no_problems,
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
                this.store.add(new Phlexible.problems.portlet.ProblemRecord(row, row.id));

                Phlexible.msg('Problem', this.strings.new_problem + ' "' + row.msg + '".');
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
