Ext.define('Phlexible.metasets.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.metasets-main',

    title: Phlexible.metasets.Strings.metasets,
    strings: Phlexible.metasets.Strings,
    layout: 'border',
    border: false,
    iconCls: Phlexible.Icon.get('weather-clouds'),

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var metaFields = Ext.create('Phlexible.metasets.util.Fields');

        this.items = [
            {
                xtype: 'grid',
                region: 'west',
                width: 200,
                padding: '5 0 5 5',
                loadMask: true,
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'name'],
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('metasets_sets_list'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            rootProperty: 'sets',
                            idProperty: 'uid',
                            totalProperty: 'count'
                        },
                        extraParams: this.storeExtraParams
                    },
                    autoLoad: true,
                    sorters: [{
                        property: 'name',
                        direction: 'ASC'
                    }]
                }),
                columns: [
                    {
                        header: this.strings.id,
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        header: this.strings.name,
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.strings.actions,
                        width: 30,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                                tooltip: this.strings.rename,
                                handler: this.renameSet,
                                scope: this
                            }
                        ]
                    }
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            text: this.strings.add,
                            iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                            handler: this.createSet,
                            scope: this
                        }
                    ]
                }],
                listeners: {
                    itemdblclick: function (grid, record) {
                        var id = record.get('id');
                        this.getComponent(1).setId = id;
                        this.getComponent(1).enable();
                        this.getComponent(1).getStore().getProxy().extraParams.id = id;
                        this.getComponent(1).store.load();
                    },
                    scope: this
                }
            },
            {
                xtype: 'grid',
                region: 'center',
                padding: 5,
                disabled: true,
                enableDragDrop: true,
                ddGroup: 'metasetitem_reorder',
                loadMask: {
                    text: 'blubb'
                },
                deferEmptyText: false,
                emptyText: this.strings.no_fields,
                stripeRows: true,
                viewConfig: {
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Drag and drop to reorganize'
                    }
                },
                plugins: {
                    ptype: 'cellediting',
                    clicksToEdit: 1
                },
                store: Ext.create('Ext.data.Store', {
                    fields: ['id', 'key', 'type', 'required', 'synchronized', 'readonly', 'options'],
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('metasets_sets_fields'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            rootProperty: 'values'
                        },
                        extraParams: {
                            id: ''
                        }
                    }
                }),
                columns: [
                    {
                        header: this.strings.id,
                        dataIndex: 'id',
                        width: 100,
                        hidden: true
                    },
                    {
                        header: this.strings.name,
                        dataIndex: 'key',
                        width: 200,
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        header: this.strings.type,
                        dataIndex: 'type',
                        width: 200,
                        editor: {
                            xtype: 'combo',
                            store: Ext.create('Ext.data.Store', {
                                fields: ['type', 'text'],
                                data: metaFields.getFields()
                            }),
                            displayField: 'text',
                            valueField: 'type',
                            mode: 'local',
                            typeAhead: false,
                            triggerAction: 'all',
                            editable: false,
                            listeners: {
                                change: function(field, newValue, oldValue) {
                                    if (newValue !== 'select' && newValue !== 'suggest') {
                                        var r = this.getComponent(1).getSelectionModel().getSelection()[0];
                                        r.set('options', '');
                                    }
                                },
                                scope: this
                            }

                        }
                    },
                    {
                        xtype: 'checkcolumn',
                        header: this.strings.required,
                        dataIndex: 'required',
                        width: 85
                    },
                    {
                        xtype: 'checkcolumn',
                        header: this.strings.synchronized,
                        dataIndex: 'synchronized',
                        width: 85
                    },
                    {
                        xtype: 'checkcolumn',
                        header: this.strings.readonly,
                        dataIndex: 'readonly',
                        width: 85
                    },
                    {
                        header: this.strings.options,
                        dataIndex: 'options',
                        width: 200,
                        hidden: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.strings.actions,
                        dataIndex: 'type',
                        width: 40,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                                tooltip: this.strings.configure,
                                isDisabled: function(view, rowIndex, colIndex, item, record) {
                                    console.log(record.get('type'));
                                    return record.get('type') !== 'select' && record.get('type') !== 'suggest';
                                },
                                handler: this.configureField,
                                scope: this
                            },
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.strings.remove_field,
                                handler: this.deleteField,
                                scope: this
                            }
                        ]
                    }
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items: [
                        {
                            text: this.strings.add_field,
                            iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                            handler: this.addField,
                            scope: this
                        },
                        '-',
                        {
                            text: this.strings.save,
                            iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                            handler: this.save,
                            scope: this
                        }
                    ]
                }]
            }
        ];
    },

    createSet: function() {
        Ext.MessageBox.prompt(this.strings.add_set, this.strings.add_set_desc, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('metasets_sets_create'),
                params: {
                    name: name
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);

                    if (data.success) {
                        Phlexible.success(data.msg);

                        this.getComponent(0).store.reload();
                    } else {
                        Ext.MessageBox.alert('Failure', data.msg);
                    }
                },
                scope: this
            });
        }, this);
    },

    renameSet: function(grid, record) {
        Ext.MessageBox.prompt(this.strings.rename_set, this.strings.rename_set_desc, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('metasets_sets_rename'),
                params: {
                    name: name,
                    id: record.get('id')
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        this.getComponent(0).getStore().reload();
                        Phlexible.success(result.msg);
                    } else {
                        Phlexible.failure(result.msg);
                    }
                },
                scope: this
            })
        }, this, false, record.get('name'));
    },

    addField: function() {
        var r = new Ext.data.Record({
            id: '',
            key: '',
            type: 'textfield',
            required: false,
            synchronized: false,
            readonly: false,
            options: ''
        });
        r.set('key', 'field-' + r.id);
        this.getComponent(1).store.add(r);
    },

    configureField: function(view, rowIndex, colIndex, item, e, record) {
        if (record.get('type') === 'suggest') {
            var w = Ext.create('Phlexible.metasets.SuggestConfigurationWindow', {
                options: record.get('options'),
                listeners: {
                    select: function(options) {
                        record.set('options', options);
                    },
                    scope: this
                }
            });
            w.show();
        }
        else if (record.get('type') === 'select') {
            var w = Ext.create('Phlexible.metasets.SelectConfigurationWindow', {
                options: record.get('options'),
                listeners: {
                    store: function(options) {
                        record.set('options', options);
                    },
                    scope: this
                }
            });
            w.show();
        }
    },

    deleteField: function (grid, record) {
        grid.getStore().remove(record);
    },

    save: function () {
        var params = [];

        for (var i = 0; i < this.getComponent(1).store.getCount(); i++) {
            var r = this.getComponent(1).store.getAt(i);

            if (r.get('type') === 'select' && !r.get('options')) {
                Ext.MessageBox.alert(this.strings.failure, this.strings.select_needs_options);
                return;
            }
            if (r.get('type') === 'suggest' && !r.get('options')) {
                Ext.MessageBox.alert(this.strings.failure, this.strings.suggest_needs_options);
                return;
            }

            params.push({
                id: r.data.id,
                key: r.data.key,
                type: r.data.type,
                required: r.data.required,
                'synchronized': r.data['synchronized'],
                readonly: r.data['readonly'],
                options: r.data.options
            });
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('metasets_sets_save'),
            params: {
                id: this.getComponent(1).setId,
                data: Ext.encode(params)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getComponent(1).store.reload();

                    Phlexible.success(data.msg);
                } else {
                    Ext.MessageBox.alert('Success', data.msg);
                }
            },
            scope: this
        });
    }
});
