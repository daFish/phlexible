Ext.define('Phlexible.metaset.window.MetaSetsWindow', {
    extend: 'Ext.window.Window',

    title: '_MetaSetsWindow',
    iconCls: 'p-metaset-component-icon',
    width: 400,
    height: 300,
    layout: 'fit',

    baseParams: {},
    urls: {},

    metasetText: '_metasetText',
    actionsText: '_actionsText',
    removeText: '_removeText',
    selectText: '_selectText',
    addText: '_addText',
    cancelText: '_cancelText',
    saveText: '_saveText',

    initComponent: function () {
        if (!this.urls.list || !this.urls.available || !this.urls.save) {
            throw 'Missing url config';
        }

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'grid',
                border: false,
                viewConfig: {
                    forceFit: true
                },
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'name'],
                    proxy: {
                        type: 'ajax',
                        url: this.urls.list,
                        simpleSortMode: true,
                        sorters: [{property: 'name', direction: 'ASC'}],
                        reader: {
                            type: 'json',
                            rootProperty: 'sets',
                            idProperty: 'id',
                            totalProperty: 'count'
                        },
                        extraParams: this.baseParams
                    },
                    autoLoad: true
                }),
                columns: [
                    {
                        header: this.metasetText,
                        dataIndex: 'name'
                    },
                    {
                        header: this.actionsText,
                        width: 40,
                        actions: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.removeText,
                                handler: this.removeMetaSet,
                                scope: this
                            }
                        ]
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                xtype: 'combo',
                store: new Ext.data.JsonStore({
                    url: this.urls.available,
                    fields: ['id', 'name'],
                    root: 'sets',
                    id: 'id',
                    baseParams: this.baseParams,
                    sortInfo: {field: "name", direction: "ASC"},
                    listeners: {
                        load: function (store, records) {
                            Ext.each(records, function (record) {
                                if (this.getComponent(0).getStore().find('id', record.get('id')) !== -1) {
                                    store.remove(record);
                                }
                            }, this);
                        },
                        scope: this
                    }
                }),
                emptyText: this.selectText,
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                triggerAction: 'all',
                editable: false
            },
            {
                text: this.addText,
                iconCls: 'p-metaset-add-icon',
                handler: this.addMetaSet,
                scope: this
            }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.cancelText,
                handler: this.close,
                scope: this
            },{
                text: this.saveText,
                iconCls: 'p-metaset-save-icon',
                handler: this.save,
                scope: this
            }]
        }];
    },

    addMetaSet: function () {
        var id = this.getTopToolbar().items.items[0].getValue(),
            name = this.getTopToolbar().items.items[0].getRawValue();

        if (!id || !id.length) {
            return;
        }

        this.getComponent(0).getStore().add(new Ext.data.Record({id: id, name: name}));

        var combo = this.getTopToolbar().items.items[0],
            idx = combo.store.find('id', id);
        if (idx !== -1) {
            combo.store.removeAt(idx);
        }
        combo.setValue(null);
        return;

        Ext.Ajax.request({
            url: this.urls.add,
            params: Ext.apply({}, {set_id: set_id}, this.baseParams),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    var combo = this.getTopToolbar().items.items[0];
                    var r = combo.store.getById(set_id);
                    combo.store.remove(r);

                    this.getComponent(0).store.reload();

                    Phlexible.success(data.msg);

                    this.fireEvent('addset');
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    removeMetaSet: function (grid, record) {
        grid.getStore().remove(record);

        var combo = this.getTopToolbar().items.items[0];
        combo.store.addSorted(new Ext.data.Record({id: record.get('id'), name: record.get('name')}));
        return;

        var r = this.getComponent(0).getSelectionModel().getSelected();
        var set_id = r.data.set_id;

        var newRecord = new Ext.data.Record({
            set_id: set_id,
            name: r.data.name
        });
        var combo = this.getTopToolbar().items.items[0];
        var r = combo.store.insert(0, newRecord);

        Ext.Ajax.request({
            url: this.urls.remove,
            params: Ext.apply({}, {set_id: set_id}, this.baseParams),
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getComponent(0).store.reload();

                    Phlexible.success(data.msg);

                    this.fireEvent('removeset');
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    save: function() {
        var ids = [];
        Ext.each(this.getComponent(0).getStore().getRange(), function(record) {
            ids.push(record.get('id'));
        });

        var params = Phlexible.clone(this.baseParams);
        params.ids = ids.join(',');

        Ext.Ajax.request({
            url: this.urls.save,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.success(data.msg);

                    this.fireEvent('savesets');

                    this.close();
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});
