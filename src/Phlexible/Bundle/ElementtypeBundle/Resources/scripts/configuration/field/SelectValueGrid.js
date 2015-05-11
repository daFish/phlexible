Ext.define('Phlexible.elementtypes.configuration.SelectValueGrid', {
    extend: 'Ext.grid.Panel',
    xtype: 'elementtype.configuration.selection-value',

    border: true,
    viewConfig: {
        forceFit: true
    },
    autoHeight: true,
    cls: 'p-elementtypes-value-grid',

    defaultValue: null,

    enableDragDrop: true,
    ddGroup: 'fieldvalue',

    keyText: '_keyText',
    deText: '_deText',
    enText: '_enText',
    addOptionText: '_addOptionText',
    removeOptionText: '_removeOptionText',
    setDefaultText: '_setDefaultText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.SimpleStore', {
            fields: ['key', 'value_de', 'value_en']
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.keyText,
                dataIndex: 'key',
                width: 100,
                sortable: false,
                editor: {
                    xtype: 'textfield',
                    allowBlank: true
                }
            },
            {
                header: this.deText,
                dataIndex: 'value_de',
                width: 100,
                sortable: false,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            {
                header: this.enText,
                dataIndex: 'value_en',
                width: 100,
                sortable: false,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.tbar = [
            {
                text: this.addOptionText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: function () {
                    this.store.add(new Ext.data.Record({key: 'new key', value_de: '', value_en: ''}));
                },
                scope: this
            },
            {
                text: this.removeOptionText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                disabled: true,
                handler: function () {
                    var recordToBeRemoved = this.getSelectionModel().getSelected();
                    if (!recordToBeRemoved) {
                        return;
                    }
                    var key = recordToBeRemoved.get('key');
                    if (this.defaultValue == key) {
                        this.defaultValue = null;
                    }

                    this.store.remove(recordToBeRemoved);
                },
                scope: this
            },
            {
                text: this.setDefaultText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                disabled: true,
                handler: function (item) {
                    if (!this.getSelectionModel().getSelected()) {
                        return;
                    }
                    var record = this.getSelectionModel().getSelected(),
                        defaultIndex = this.store.find('key', this.defaultValue),
                        row, selectedIndex;

                    if (defaultIndex != -1) {
                        row = Ext.get(this.view.getRow(defaultIndex));
                        row.removeClass('p-elementtype-value-grid-default');
                    }

                    this.defaultValue = record.get('key');
                    selectedIndex = this.store.indexOf(record);
                    row = Ext.get(this.view.getRow(selectedIndex));
                    row.addClass('p-elementtype-value-grid-default');
                },
                scope: this
            }
        ];
    },

    initMyListeners: function() {
        this.on({
            selectionchange: function (grid, selected) {
                if (selected.length) {
                    this.getTopToolbar().items.items[1].enable();
                    this.getTopToolbar().items.items[2].enable();
                } else {
                    this.getTopToolbar().items.items[1].disable();
                    this.getTopToolbar().items.items[2].disable();
                }
            },
            validateedit: function (validationobject) {
                if (validationobject.field == 'key') {
                    var recordQuery = this.store.find('key', new RegExp('^' + validationobject.value + '$'));

                    if (recordQuery != -1) {
                        validationobject.value = validationobject.originalValue;
                        return;
                    }

                    if (this.defaultValue == validationobject.originalValue) {
                        this.defaultValue = validationobject.value;
                        this.fireEvent('defaultchange', this.defaultValue);
                    }
                }
            },
            afteredit: function (validationobject) {
                var defaultIndex = this.store.find('key', this.defaultValue);
                if (defaultIndex != -1) {
                    var row = Ext.get(this.view.getRow(defaultIndex)); //validationobject.row));
                    row.addClass('p-elementtype-value-grid-default');
                }
            },
            render: function (grid) {
                return false; // TODO: fix
                this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                    copy: false
                });
                // if you need scrolling, register the grid view's scroller with the scroll manager
                //            Ext.dd.ScrollManager.register(g.getView().getEditorParent());
            },
        });
    },

    loadData: function (sourceList, defaultValue) {
        this.store.removeAll();

        if (sourceList && sourceList[0]) {
            if (sourceList[0].key) {
                var modified_list = [];
                Ext.each(sourceList, function (item) {
                    modified_list.push([item.key, item.de, item.en]);
                });
                this.store.loadData(modified_list);
            }
            else {
                this.store.loadData(sourceList);
            }
        }

        var defaultIndex = this.store.find('key', defaultValue);
        if (defaultIndex != -1) {
            var row = Ext.get(this.view.getRow(defaultIndex));
            row.addClass('p-elementtype-value-grid-default');
        }

        this.defaultValue = defaultValue;
    },

    isValid: function() {
        var valid = true;
        this.store.each(function(r) {
            if (!r.data.key || !r.data.value_de || !r.data.value_en) {
                valid = false;
                return false;
            }
        });
        return valid;
    },

    getDefaultValue: function() {
        return this.defaultValue;
    }
});
