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
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyTemplate: function() {
        this.tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div id="portal_problems_{id}" class="portlet-problem">',
            '<div class="p-problem-severity {[Phlexible.problem.ProblemIcons[values.severity]]}" ></div>',
            '<div class="p-problem-text">',
            '<span class="p-problem-message">{msg}</span>',
            '<tpl if="hint">',
            '<br />',
            '<span class="p-problem-solve">' + this.solutionText + ': {hint}</span>',
            '</tpl>',
            '</div>',
            '<div class="x-clear-both"></div>',
            '</div>',
            '</tpl>'
        );
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.problem.model.Problem',
            proxy: {
                type: 'memory',
                reader: {
                    type: 'json',
                    idProperty: 'id'
                }
            },
            sorters: [{property: 'severity', username: 'ASC'}]
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

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                xtype: 'tbtext',
                text: this.menuHintText
            }]
        }];
    },

    updateData: function (data) {
        var problemsMap = [],
            toRemove = [],
            store = this.store;

        Ext.each(data, function(item) {
            problemsMap.push(item.id);
            if (store.find('id', item.id) === -1) {
                store.add(new Phlexible.problem.model.Problem(item));
            }
        }, this);

        store.each(function(problem) {
            if (problemsMap.indexOf(problem.id) === -1) {
                toRemove.push(problem);
            }
        }, this);

        Ext.each(toRemove, function (problem) {
            store.remove(problem);
        });

        this.store.sort('type', 'ASC');
    }
});
