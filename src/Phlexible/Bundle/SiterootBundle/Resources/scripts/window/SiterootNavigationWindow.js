Ext.define('Phlexible.siteroot.window.SiterootNavigationWindow', {
    extend: 'Ext.window.Window',

    title: '_SiterootNavigationWindow',
    width: 400,
    height: 300,
    modal: true,
    layout: 'fit',

    idText: '_id',
    titleText: '_title',
    selectSiterootText: '_selectSiterootText',
    storeText: '_storeText',
    cancelText: '_cancelText',

    initComponent: function () {
        this.values = this.record.get('additional').split(',');

        this.initMyItems();
        this.initMyDockedItems();
        this.loadSiteroots();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'grid',
                border: false,
                enableDragDrop: true,
                viewConfig: {
                    forceFit: true
                },
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'title']
                }),
                sm: new Ext.grid.RowSelectionModel({
                    singleSelect: true,
                    listeners: {
                        selectionchange: function (sm) {
                            var sel = sm.getSelected();

                            if (sel) {
                                this.getComponent(0).getTopToolbar().items.items[4].enable();
                            } else {
                                this.getComponent(0).getTopToolbar().items.items[4].disable();
                            }
                        },
                        scope: this
                    }
                }),
                columns: [
                    {
                        header: this.idText,
                        dataIndex: 'id',
                        width: 150,
                        hidden: true
                    },
                    {
                        header: this.titleText,
                        width: 150,
                        dataIndex: 'title'
                    }
                ],
                tbar: [
                    'Add:',
                    ' ',
                    {
                        xtype: 'combo',
                        width: 250,
                        store: new Ext.data.SimpleStore({
                            fields: ['id', 'title']
                        }),
                        emptyText: this.selectSiterootText,
                        editable: false,
                        mode: 'local',
                        displayField: 'title',
                        valueField: 'id',
                        listeners: {
                            select: function (combo, r) {
                                combo.store.remove(r);

                                var c = this.getComponent(0);
                                c.store.add(r);

                                combo.setValue('');
                            },
                            scope: this
                        }
                    }, '-', {
                        text: 'Remove',
                        disabled: true,
                        handler: function () {
                            var r = this.getComponent(0).getSelectionModel().getSelected();

                            if (!r) return;

                            this.getComponent(0).store.remove(r);

                            this.getComponent(0).getTopToolbar().items.items[2].store.add(r);
                        },
                        scope: this
                    }],
                listeners: {
                    render: function (grid) {
                        this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                            copy: false
                        });
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
            items: [
                {
                    text: this.storeText,
                    handler: function () {
                        var records = this.getComponent(0).store.getRange();

                        var data = '';
                        for (var i = 0; i < records.length; i++) {
                            data += (data ? ',' : '') + records[i].get('id');
                        }

                        this.record.set('additional', data);

                        this.close();
                    },
                    scope: this
                },
                {
                    text: this.cancelText,
                    handler: function () {
                        this.close();
                    },
                    scope: this
                }
            ]
        }];
    },

    loadSiteroots: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroot_list'),
            baseParams: {
                key: this.navigationKey,
                id: this.siterootId
            },
            success: function (response) {
                var rows = Ext.decode(response.responseText);

                for (var i = 0; i < rows.length; i++) {
                    var item = new Ext.data.Record({
                        id: rows[i].id,
                        title: rows[i].title
                    });
                    if (this.values.indexOf(item.data.id) !== -1) {
                        this.getComponent(0).store.add(item);
                    } else {
                        this.getComponent(0).getTopToolbar().items.items[2].store.add(item);
                    }
                }

                this.getComponent(0).store.commitChanges();
            },
            scope: this
        });
    }
});
