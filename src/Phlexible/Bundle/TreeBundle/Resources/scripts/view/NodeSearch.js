Ext.provide('Phlexible.tree.view.NodeSearch');

Phlexible.tree.view.NodeSearch = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-preview-icon',
    cls: 'p-tree-node-search',

    initComponent: function() {
        this.viewConfig = {
            forceFit: true,
            emptyText: this.strings.no_results,
            deferEmptyText: false
        };

        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('tree_search_nodes'),
            baseParams: {
                siterootId: this.element.getSiterootId(),
                query: '',
                language: this.element.getLanguage()
            },
            root: 'results',
            fields: ['tid', 'version', 'title', 'icon'],
            sortInfo: {field: 'title', direction: 'ASC'}
        });

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.columns = [
            {
                dataIndex: 'title',
                header: this.strings.elements,
                renderer: function (v, md, r) {
                    var icon = '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" />';
                    var title = r.data.title;
                    var meta = r.data.tid;

                    return icon + ' ' + title + ' [' + meta + ']';
                }
            }
        ];

        this.tbar = [
            {
                xtype: 'textfield',
                emptyText: this.strings.element_search,
                enableKeyEvents: true,
                anchor: '-10',
                listeners: {
                    render: function (c) {
                        c.task = new Ext.util.DelayedTask(c.doSearch, this);
                    },
                    keyup: function (c, event) {
                        if (event.getKey() == event.ENTER) {
                            c.task.cancel();
                            c.doSearch();
                            return;
                        }

                        c.task.delay(500);
                    },
                    scope: this
                },
                doSearch: function () {
                    var c = this.getComponent(0).getComponent(0).getComponent(1);
                    var query = c.getTopToolbar().items.items[0].getValue();
                    if (!query) return;
                    var store = c.getStore();
                    store.baseParams.query = query;
                    store.baseParams.language = this.element.getLanguage();
                    store.load();
                }.createDelegate(this)
            }
        ];

        this.on({
            rowdblclick: function (c, itemIndex) {
                var r = c.getStore().getAt(itemIndex);
                if (!r) return;
                this.element.reload({id: r.data.tid, version: r.data.version, language: this.element.getLanguage(), lock: 1});
            },
            scope: this
        });

        Phlexible.tree.view.NodeSearch.superclass.initComponent.call(this);
    }
});

Ext.reg('tree-node-search', Phlexible.tree.view.NodeSearch);
