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
                            if (!metaSet.get('revision')) {
                                return Phlexible.Icon.inlineText('new', v);
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
        var metaSet = new Phlexible.metaset.model.MetaSet({
            revision: 0,
            name: 'metaset-' + Ext.id(null, '')
        });
        this.getComponent('list').getStore().add(metaSet);
        this.getComponent('list').getSelectionModel().select(metaSet);
    },

    renameSet: function(grid, metaSet) {
        Ext.MessageBox.prompt(this.renameSetText, this.renameSetDescriptionText, function(btn, name) {
            if (btn !== 'ok') {
                return;
            }
            Ext.Ajax.request({
                url: Phlexible.Router.generate('metaset_sets_rename'),
                params: {
                    name: name,
                    id: metaSet.get('id')
                },
                success: function(response) {
                    var result = Ext.decode(response.responseText);
                    if (result.success) {
                        this.getComponent('list').getStore().reload();
                        Phlexible.success(result.msg);
                    } else {
                        Phlexible.failure(result.msg);
                    }
                },
                scope: this
            });
        }, this, false, metaSet.get('name'));
    },

    addField: function() {
        var uuid = new Ext.data.identifier.Uuid(),
            field = new Phlexible.metaset.model.MetaSetField({
                id: uuid.generate(),
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
        var error = false,
            url,
            method,
            data = {
                metaset: {
                    name: this.metaSet.get('name'),
                    fields: []
                }
            };

        this.getComponent('fields').getStore().each(function(field) {
            if (field.get('type') === 'select' && !field.get('options')) {
                Phlexible.Notify.failure(this.selectNeedsOptionsText);
                error = true;
                return false;
            }
            if (field.get('type') === 'suggest' && !field.get('options')) {
                Phlexible.Notify.failure(this.suggestNeedsOptionsText);
                error = true;
                return false;
            }

            data.metaset.fields.push({
                id: field.data.id,
                name: field.data.name,
                type: field.data.type,
                required: field.data.required,
                synchronized: field.data.synchronized,
                readonly: field.data.readonly,
                options: field.data.options
            });
        }, this);

        if (error) {
            return;
        }

        if (this.metaSet.get('revision')) {
            url = Phlexible.Router.generate('phlexible_api_metaset_put_metaset', {metasetId: this.metaSet.get('id')});
            method = 'PUT';
        } else {
            url = Phlexible.Router.generate('phlexible_api_metaset_post_metasets');
            method = 'POST';
        }

        Ext.Ajax.request({
            url: url,
            method: method,
            jsonData: data,
            success: function (response) {
                //var data = Ext.decode(response.responseText);

                if (response.status === 201 || response.status === 204) {
                    this.getComponent('list').getStore().reload({
                        callback: function() {
                            this.selectSet(this.getStore().getSelectionModel().getSelected()[0]);
                        },
                        scope: this
                    });

                    //this.getComponent('fields').getStore().reload();
                    Phlexible.Notify.success('succ');
                } else {
                    Phlexible.Notify.failure('fail');
                }
            },
            scope: this
        });
    }
});
