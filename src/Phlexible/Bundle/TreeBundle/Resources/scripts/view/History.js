Ext.provide('Phlexible.tree.view.History');

Phlexible.tree.view.History = Ext.extend(Ext.grid.GridPanel, {
    strings: Phlexible.elements.Strings,
    iconCls: 'p-element-tab_history-icon',
    cls: 'p-tree-history',
    viewConfig: {
        forceFit: true
    },

    initComponent: function() {
        this.store = new Ext.data.SimpleStore({
            fields: ['tid', 'version', 'language', 'title', 'icon', 'ts'],
            sortInfo: {field: 'ts', direction: 'DESC'}
        });

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect: true
        });

        this.columns = [
            {
                dataIndex: 'title',
                header: this.strings.history,
                renderer: function (v, md, r) {
                    var icon = '<img src="' + r.data.icon + '" width="18" height="18" style="vertical-align: middle;" />';
                    var date = Math.floor(new Date().getTime() / 1000 - r.data.ts / 1000);
                    if (date) {
                        date = 'Geöffnet vor ' + Phlexible.Format.age(date);
                    } else {
                        date = 'Gerade geöffnet';
                    }
                    var title = r.data.title;
                    var meta = r.data.tid + ', v' + r.data.version + ', ' + r.data.language;

                    return icon + ' ' + title + ' [' + meta + ']<br />' +
                        '<span style="color: gray; font-size: 10px;">' + date + '</span>';
                }
            }
        ];

        this.on({
            rowdblclick: function (c, itemIndex) {
                var r = c.getStore().getAt(itemIndex);
                if (!r) return;
                this.element.reload({id: r.data.tid, version: r.data.version, language: r.data.language, lock: 1});
            },
            scope: this
        });

        this.element.on({
            historychange: function () {
                this.store.loadData(this.element.history.getRange());
            },
            scope: this
        });

        Phlexible.tree.view.History.superclass.initComponent.call(this);
    }
});

Ext.reg('tree-history', Phlexible.tree.view.History);
