Ext.define('Phlexible.datasource.window.MetaSuggestWindow', {
    extend: 'Ext.window.Window',

    width: 400,
    height: 350,
    layout: 'fit',
    modal: true,

    valueField: null,
    optionsField: null,

    metaKey: null,
    metaSourceId: null,
    metaValue: null,
    metaLanguage: null,

    initComponent: function () {
        if (this.record && !this.metaKey) {
            this.metaKey = this.record.data.key;
        }
        if (this.record && !this.metaSourceId) {
            this.metaSourceId = this.record.data.options.sourceId;
        }
        if (this.record && !this.metaValue) {
            this.metaValue = this.record.data[this.valueField];
        }
        if (this.record && !this.metaLanguage && '_' == this.valueField[this.valueField.length - 3]) {
            this.metaLanguage = this.valueField.substr(this.valueField.length - 2);
        }

        // if still no meta language is set, use englisch as default
        if (!this.metaLanguage) {
            this.metaLanguage = 'en';
        }

        // if no value or an empty string is given, set to undefined to prevent setValue() call
        if (!this.metaValue) {
            this.metaValue = undefined;
        }

        this.title = 'Edit ' + this.metaKey;

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'form',
                autoScroll: true,
                labelWidth: 60,
                bodyStyle: 'padding: 5px;',
                border: false,
                disabled: true,
                items: [
                    {
                        xtype: 'tagfield',
                        hideLabel: true,
                        sourceId: this.metaSourceId,
                        emptyText: 'No tag selected',
                        anchor: '-20',
                        value: this.metaValue,
                        suggestLanguage: this.metaLanguage,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['key', 'value'],
                            proxy: {
                                type: 'ajax',
                                url: Phlexible.Router.generate('metaset_values'),
                                simpleSortMode: true,
                                reader: {
                                    type: 'json',
                                    rootProperty: 'values',
                                    idProperty: 'key'
                                },
                                extraParams: {
                                    sourceId: this.metaSourceId,
                                    language: this.metaLanguage
                                }
                            },
                            autoLoad: true,
                            listeners: {
                                load: function (store) {
                                    if (this.metaValue !== undefined) {
                                        var values = this.metaValue.split(',');
                                        for (var i = 0; i < values.length; i++) {
                                            if (!store.getById(values[i])) {
                                                var newObj = {
                                                    key: values[i],
                                                    value: values[i]
                                                };
                                                var newRecord = new Ext.data.Record(newObj);
                                                store.add(newRecord);
                                                //this.getComponent(0).getComponent(0).addItem(newObj);
                                            }
                                        }
                                        this.getComponent(0).getComponent(0).setValue(this.metaValue);
                                    }
                                    this.getComponent(0).enable();
                                },
                                scope: this
                            }
                        }),
                        displayField: 'value',
                        valueField: 'key',
                        mode: 'local',
                        //minLength: 2,
                        //maxLength: 4,
                        allowAddNewData: true,
                        stackItems: true,
                        extraItemCls: 'x-tag x-tag-red',
                        listeners: {
                            newitem: function (bs, v) {
                                var newObj = {
                                    key: v,
                                    value: v
                                };
                                bs.addNewItem(newObj);
                            }
                        }
                    }
                ]
            }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                {
                    text: 'Store',
                    handler: function () {
                        var value = this.getComponent(0).getComponent(0).getValue();
                        if (this.record) {
                            this.record.set(this.valueField, value);
                        }
                        this.fireEvent('store', this, value);
                        this.close();
                    },
                    scope: this
                },
                {
                    text: 'Cancel',
                    handler: function () {
                        this.close();
                    },
                    scope: this
                }
            ]
        }];
    }
});
