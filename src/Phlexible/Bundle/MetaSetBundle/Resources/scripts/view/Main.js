Ext.define('Phlexible.metaset.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.metaset.model.MetaSet',
        'Phlexible.metaset.model.MetaSetField'
    ],
    xtype: 'metaset.main',

    layout: 'border',
    border: false,
    iconCls: Phlexible.Icon.get('weather-clouds'),
    referenceHolder: true,
    viewModel: {
        stores: {
            metasets: {
                model: 'MetaSet',
                autoLoad: true,
                sorters: [{
                    property: 'name',
                    direction: 'ASC'
                }]
            }
        }
    },

    metaSet: null,

    metaSetText: '_metaSetText',
    metaSetsText: '_metaSetsText',
    idText: '_idText',
    nameText: '_nameText',
    revisionText: '_revisionText',
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
                itemId: 'list',
                bind: {
                    store: '{metasets}'
                },
                reference: 'list',
                title: this.metaSetsText,
                iconCls: Phlexible.Icon.get('weather-clouds'),
                width: 200,
                padding: '5 0 5 5',
                columns: [
                    {
                        header: this.idText,
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        header: this.nameText,
                        dataIndex: 'name',
                        flex: 1,
                        renderer: function(v, md, metaSet) {
                            if (metaSet.dirty) {
                                md.tdCls = 'x-grid-dirty-cell';
                            }

                            return v;
                        }
                    },
                    {
                        header: this.revisionText,
                        dataIndex: 'revision',
                        width: 40,
                        hidden: true
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.actionsText,
                        width: 30,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                                tooltip: this.renameText,
                                handler: function(grid, rowIndex, colIndex, item, e, metaSet) {
                                    this.renameSet(metaSet);
                                },
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
                        },
                        '->',
                        {
                            text: this.saveText,
                            iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                            handler: this.save,
                            scope: this
                        }
                    ]
                }]
            },
            {
                xtype: 'grid',
                region: 'center',
                itemId: 'fields',
                bind: {
                    store: '{list.selection.fields}',
                    title: '{list.selection.name}'
                },
                title: this.metaSetText,
                padding: 5,
                enableDragDrop: true,
                ddGroup: 'metasetitem_reorder',
                stripeRows: true,
                emptyText: this.noFieldsText,
                viewConfig: {
                    deferEmptyText: false,
                    plugins: {
                        ptype: 'gridviewdragdrop',
                        dragText: 'Drag and drop to reorganize'
                    }
                },
                plugins: {
                    ptype: 'cellediting',
                    clicksToEdit: 1
                },
                columns: [
                    {
                        header: this.idText,
                        dataIndex: 'id',
                        width: 100,
                        hidden: true
                    },
                    {
                        header: this.nameText,
                        dataIndex: 'name',
                        width: 200,
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false
                        },
                        renderer: function(v, md, field) {
                            if (field.dirty) {
                                md.tdCls = 'x-grid-dirty-cell';
                            }

                            return v;
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
                                change: function(combo, newValue, oldValue) {
                                    if (newValue !== 'select' && newValue !== 'suggest') {
                                        var field = this.getComponent('fields').getSelectionModel().getSelection()[0];
                                        field.set('options', '');
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
                                isDisabled: function(view, rowIndex, colIndex, item, field) {
                                    return field.get('type') !== 'select' && field.get('type') !== 'suggest';
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
            var metaSet = new Phlexible.metaset.model.MetaSet({
                name: name,
                revision: 1,
                createdAt: Ext.Date.format(new Date, "Y-m-d H:i:s"),
                createdBy: Phlexible.User.getUsername(),
                modifiedAt: Ext.Date.format(new Date, "Y-m-d H:i:s"),
                modifiedBy: Phlexible.User.getUsername()
            });
            this.getViewModel().getStore('metasets').add(metaSet);
            this.getComponent('list').getSelectionModel().select(metaSet);
        }, this, false, 'metaset-' + Ext.id(null, ''));
    },

    renameSet: function(metaSet) {
        Ext.MessageBox.prompt(this.renameSetText, this.renameSetDescriptionText, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            metaSet.set('name', name);
        }, this, false, metaSet.get('name'));
    },

    addField: function() {
        var uuid = new Ext.data.identifier.Uuid(),
            field = new Phlexible.metaset.model.MetaSetField({
                //id: uuid.generate(),
                name: 'field-' + Ext.id(null, ''),
                type: 'textfield',
                required: false,
                synchronized: false,
                readonly: false,
                options: ''
            });

        this.getComponent('fields').getStore().add(field);
    },

    configureField: function(view, rowIndex, colIndex, item, e, field) {
        var win;

        if (field.get('type') === 'suggest') {
            win = Ext.create('Phlexible.datasource.window.SuggestConfigurationWindow', {
                options: field.get('options'),
                listeners: {
                    select: function(options) {
                        field.set('options', options);
                    },
                    scope: this
                }
            });
            win.show();
        }
        else if (field.get('type') === 'select') {
            win = Ext.create('Phlexible.metaset.window.SelectConfigurationWindow', {
                options: field.get('options'),
                listeners: {
                    store: function(options) {
                        field.set('options', options);
                    },
                    scope: this
                }
            });
            win.show();
        }
    },

    removeField: function (grid, rowIndex, colIndex, item, e, field) {
        grid.getStore().remove(field);
    },

    save: function () {
        this.getViewModel().getStore('metasets').sync();
    }
});
