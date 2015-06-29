Ext.define('Phlexible.elementtype.configuration.root.Mappings', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.elementtype.configuration.root.DateMapping',
        'Phlexible.elementtype.configuration.root.LinkMapping',
        'Phlexible.elementtype.configuration.root.TitleMapping'
    ],
    xtype: 'elementtype.configuration.root.mappings',

    iconCls: Phlexible.Icon.get('arrow-join'),
    border: false,
    layout: 'border',
    padding: 5,

    actionsText: '_actionsText',
    removeText: '_removeText',
    fieldText: '_fieldText',
    chooseFieldText: '_chooseFieldText',
    patternText: '_patternText',
    previewText: '_previewText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'grid',
                region: 'west',
                width: 160,
                store: Ext.create('Ext.data.Store', {
                    fields: ['name', 'type', 'state', 'pattern', 'fields'],
                    data: [
                        ['backend', 'title', 0, null, null],
                        ['page', 'title', 0, null, null],
                        ['navigation', 'title', 0, null, null],
                        ['date', 'date', 0, null, null],
                        ['forward', 'link', 0, null, null],
                        ['custom1', 'field', 0, null, null],
                        ['custom2', 'field', 0, null, null],
                        ['custom3', 'field', 0, null, null],
                        ['custom4', 'field', 0, null, null],
                        ['custom5', 'field', 0, null, null]
                    ]
                }),
                columns: [
                    {
                        header: '&nbsp;',
                        dataIndex: 'state',
                        width: 30,
                        renderer: function(v) {
                            if (v === 1) {
                                return Phlexible.Icon.get(Phlexible.Icon.ERROR);
                            } else if (v === 2) {
                                return Phlexible.Icon.get(Phlexible.Icon.OK);
                            }
                            return '';
                        }
                    },{
                        header: this.fieldText,
                        dataIndex: 'name',
                        flex: 1
                    },{
                        xtype: 'actioncolumn',
                        header: this.actionsText,
                        width: 30,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.removeText,
                                showIndex: 'state',
                                handler: function (view, rowIndex, colIndex, item, e, record) {
                                    record.set('fields', null);
                                    record.set('pattern', null);
                                    record.set('state', 0);
                                    record.commit();
                                }
                            }
                        ]
                    }
                ],
                listeners: {
                    selectionchange: function(mapgrid, selected) {
                        if (!selected.length) {
                            return;
                        }
                        var r = selected[0],
                            name, type, grid;

                        name = r.get('name');
                        type = r.get('type');
                        if (type === 'title') {
                            this.getComponent(1).getLayout().setActiveItem(1);
                            grid = this.getMappedTitleGrid();
                            grid.getComponent(0).getView().refresh();
                            grid.getComponent(0).getStore().removeAll();
                            grid.getComponent(1).setValue('');
                            if (r.get('state')) {
                                if (r.get('fields')) {
                                    grid.getComponent(0).getStore().loadData(r.get('fields'));
                                }
                                if (r.get('pattern')) {
                                    grid.getComponent(1).setValue(r.get('pattern'));
                                }
                            }
                            this.updateTitlePreview(name);
                        } else if (type === 'date') {
                            this.getComponent(1).getLayout().setActiveItem(2);
                            grid = this.getMappedDateGrid();
                            grid.getComponent(0).getView().refresh();
                            grid.getComponent(0).getStore().removeAll();
                            if (r.get('state') && r.get('fields')) {
                                grid.getComponent(0).getStore().loadData(r.get('fields'));
                            }
                        } else if (type === 'link') {
                            this.getComponent(1).getLayout().setActiveItem(3);
                            grid = this.getMappedLinkGrid();
                            grid.getComponent(0).getView().refresh();
                            grid.getComponent(0).getStore().removeAll();
                            if (r.get('state') && r.get('fields')) {
                                grid.getComponent(0).getStore().loadData(r.get('fields'));
                            }
                        } else if (type === 'field') {
                            this.getComponent(1).getLayout().setActiveItem(4);
                            grid = this.getMappedFieldGrid();
                            grid.getComponent(0).getView().refresh();
                            grid.getComponent(0).getStore().removeAll();
                            grid.getComponent(1).setValue('');
                            if (r.get('state')) {
                                if (r.get('fields')) {
                                    grid.getComponent(0).getStore().loadData(r.get('fields'));
                                }
                                if (r.get('pattern')) {
                                    grid.getComponent(1).setValue(r.get('pattern'));
                                }
                            }
                            this.updateFieldPreview(name);
                        } else {
                            this.getComponent(1).getLayout().setActiveItem(0);
                        }
                    },
                    scope: this
                }
            },
            {
                region: 'center',
                layout: 'card',
                activeItem: 0,
                bodyStyle: 'padding: 5px;',
                border: false,
                items: [
                    {
                        html: this.chooseFieldText,
                        border: false
                    },
                    {
                        xtype: 'form',
                        border: false,
                        labelAlign: 'top',
                        autoScroll: true,
                        defaults: {
                            anchor: '100%'
                        },
                        items: [
                            {
                                xtype: 'elementtype.configuration.root.title-mapping',
                                height: 200,
                                allowedTypes: [
                                    'textfield',
                                    'numberfield',
                                    'date',
                                    'select'
                                ],
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                        this.updateTitlePreview(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.patternText,
                                name: 'pattern',
                                allowBlank: false,
                                listeners: {
                                    change: function (field, pattern) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('pattern', pattern);
                                        this.validate(name);
                                        this.updateTitlePreview(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.previewText,
                                name: 'preview',
                                readOnly: true
                            }
                        ]
                    },
                    {
                        border: false,
                        items: [
                            {
                                xtype: 'elementtype.configuration.root.date-mapping',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    },
                    {
                        border: false,
                        items: [
                            {
                                xtype: 'elementtype.configuration.root.link-mapping',
                                height: 200,
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            }
                        ]
                    },
                    {
                        xtype: 'form',
                        border: false,
                        labelAlign: 'top',
                        autoScroll: true,
                        items: [
                            {
                                xtype: 'elementtype.configuration.root.title-mapping',
                                height: 200,
                                allowedTypes: [
                                    'textfield',
                                    'numberfield',
                                    'textarea',
                                    'date',
                                    'select',
                                    'multiselect',
                                    'suggest'
                                ],
                                listeners: {
                                    change: function (fields) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('fields', fields);
                                        this.validate(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.patternText,
                                name: 'pattern',
                                anchor: '-10',
                                allowBlank: false,
                                listeners: {
                                    change: function (field, pattern) {
                                        var name = this.getComponent(0).getSelectionModel().getSelection()[0].get('name');
                                        this.findRecord(name).set('pattern', pattern);
                                        this.validate(name);
                                        this.updateFieldPreview(name);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: this.previewText,
                                name: 'preview',
                                anchor: '-10',
                                readOnly: true
                            }
                        ]
                    }
                ]
            }
        ];

        this.callParent(arguments);
    },

    getMappedTitleGrid: function () {
        return this.getComponent(1).getComponent(1);
    },

    getMappedDateGrid: function () {
        return this.getComponent(1).getComponent(2);
    },

    getMappedLinkGrid: function () {
        return this.getComponent(1).getComponent(3);
    },

    getMappedFieldGrid: function () {
        return this.getComponent(1).getComponent(4);
    },

    findRecord: function(name) {
        var store = this.getComponent(0).getStore(),
            index = store.find('name', name);
        if (index === -1) {
            throw new Error("Field " + name + " not found.");
        }
        return store.getAt(index);
    },

    validate: function(name) {
        var record = this.findRecord(name),
            state = 0,
            type = record.get('type'),
            fields = record.get('fields'),
            pattern = record.get('pattern');

        if (Ext.isArray(fields) && fields.length) {
            state++;
        }

        switch (type) {
            case 'title':
                if (pattern) {
                    state++;
                }
                break;
            case 'date':
                if (state) {
                    state++;
                }
                break;
            case 'link':
                if (state) {
                    state++;
                }
                break;
            case 'field':
                if (state) {
                    state++;
                }
                break;
            default:
                throw new Error("Type " + type + " not known.");
        }

        record.set('state', state);
    },

    updateTitlePreview: function (name) {
        var r = this.findRecord(name),
            fields = r.get('fields'),
            pattern = Ext.clone(r.get('pattern')) || '',
            previewField = this.getMappedTitleGrid().getComponent(2);

        if (fields) {
            Ext.each(fields, function (field) {
                pattern = pattern.replace('$' + field.index, field.title);
            });
        }

        previewField.setValue(pattern);
    },

    updateFieldPreview: function (name) {
        var record = this.findRecord(name),
            fields = record.get('fields'),
            pattern = Ext.clone(record.get('pattern')) || '',
            previewField = this.getMappedFieldGrid().getComponent(2);

        if (fields) {
            Ext.each(fields, function (field) {
                pattern = pattern.replace('$' + field.index, field.title);
            });
        }

        previewField.setValue(pattern);
    },

    empty: function () {
        this.getComponent(0).getStore().each(function(record) {
            record.set('state', 0);
            record.set('pattern', '');
            record.set('fields', null);
        });
    },

    loadMappings: function (mappings) {
        this.empty();
        Ext.Object.each(mappings, function(name, mapping) {
            var record = this.findRecord(name);
            record.set('fields', mapping.fields);
            record.set('pattern', mapping.pattern || '');
            this.validate(name);
        }, this);
        this.getComponent(0).getStore().commitChanges();
        this.getComponent(0).getSelectionModel().selectRange(0, 0);
    },

    loadNode: function (node) {
        if (node.get('type') == 'root' && node.get('type') !== 'layout') {
            this.tab.show();
            this.enable();
            this.loadMappings(node.get('mappings'));
        } else {
            this.tab.hide();
            this.disable();
            this.empty();
        }
    },

    getSaveValues: function () {
        var mappings = {};
        this.getComponent(0).getStore().each(function(record) {
            if (record.get('state') === 2) {
                mappings[record.get('name')] = {
                    fields: record.get('fields'),
                    pattern: record.get('pattern')
                };
            }
        });

        //Phlexible.console.debug(mappings);

        return mappings;
    },

    isValid: function () {
        if (this.getComponent(0).getStore().find('state', 1) === -1) {
            //this.ownerCt.header.child('span').removeClass('error');
            this.setIconCls(Phlexible.Icon.get('arrow-join'));

            return true;
        }
        else {
            //this.ownerCt.header.child('span').addClass('error');
            this.setIconCls(Phlexible.Icon.get('exclamation-red'));

            return false;
        }
    }
});
