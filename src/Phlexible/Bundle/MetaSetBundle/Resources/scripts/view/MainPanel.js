Ext.define('Phlexible.metaset.view.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.metaset-main',

    title: '_MainPanel',
    layout: 'border',
    border: false,
    iconCls: Phlexible.Icon.get('weather-clouds'),

    idText: '_idText',
    nameText: '_nameText',
    actionsText: '_actionsText',
    renameText: '_renameText',
    addText: '_addText',
    noFieldsText: '_noFieldsText',
    typeText: '_typeText',
    requiredText: '_requiredText',
    synchronizedText: '_synchronizedText',
    readonlyText: '_readonlyText',
    optionsText: '_optionsText',
    configureText: '_configureText',
    removeFieldText: '_removeFieldText',
    addFieldText: '_addFieldText',
    saveText: '_saveText',
    addSetText: '_addSetText',
    addSetDescriptionText: '_addSetDescriptionText',
    renameSetText: '_renameSetText',
    renameSetDescriptionText: '_renameSetDescriptionText',
    selectNeedsOptionsText: '_selectNeedsOptionsText',
    suggestNeedsOptionsText: '_suggestNeedsOptionsText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var metaFields = Ext.create('Phlexible.metaset.util.Fields');

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
                        url: Phlexible.Router.generate('metaset_sets_list'),
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
                        header: this.idText,
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        header: this.nameText,
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.actionsText,
                        width: 30,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                                tooltip: this.renameText,
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
                            text: this.addText,
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
                emptyText: this.noFieldsText,
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
                        url: Phlexible.Router.generate('metaset_sets_fields'),
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
                        header: this.idText,
                        dataIndex: 'id',
                        width: 100,
                        hidden: true
                    },
                    {
                        header: this.nameText,
                        dataIndex: 'key',
                        width: 200,
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        }
                    },
                    {
                        header: this.typeText,
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
                        header: this.requiredText,
                        dataIndex: 'required',
                        width: 85
                    },
                    {
                        xtype: 'checkcolumn',
                        header: this.synchronizedText,
                        dataIndex: 'synchronized',
                        width: 85
                    },
                    {
                        xtype: 'checkcolumn',
                        header: this.readonlyText,
                        dataIndex: 'readonly',
                        width: 85
                    },
                    {
                        header: this.optionsText,
                        dataIndex: 'options',
                        width: 200,
                        hidden: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.actionsText,
                        dataIndex: 'type',
                        width: 40,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                                tooltip: this.configureText,
                                isDisabled: function(view, rowIndex, colIndex, item, record) {
                                    console.log(record.get('type'));
                                    return record.get('type') !== 'select' && record.get('type') !== 'suggest';
                                },
                                handler: this.configureField,
                                scope: this
                            },
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.removeFieldText,
                                handler: this.removeField,
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
                            text: this.addFieldText,
                            iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                            handler: this.addField,
                            scope: this
                        },
                        '-',
                        {
                            text: this.saveText,
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
        Ext.MessageBox.prompt(this.addSetText, this.addSetDescriptionText, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('metaset_sets_create'),
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
        Ext.MessageBox.prompt(this.renameSetText, this.renameSetDescriptionText, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('metaset_sets_rename'),
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
            var w = Ext.create('Phlexible.metaset.window.SuggestConfigurationWindow', {
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
            var w = Ext.create('Phlexible.metaset.window.SelectConfigurationWindow', {
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

    removeField: function (grid, record) {
        grid.getStore().remove(record);
    },

    save: function () {
        var params = [];

        for (var i = 0; i < this.getComponent(1).store.getCount(); i++) {
            var r = this.getComponent(1).store.getAt(i);

            if (r.get('type') === 'select' && !r.get('options')) {
                Phlexible.Notify.failure(this.selectNeedsOptionsText);
                return;
            }
            if (r.get('type') === 'suggest' && !r.get('options')) {
                Phlexible.Notify.failure(this.suggestNeedsOptionsText);
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
            url: Phlexible.Router.generate('metaset_sets_save'),
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
