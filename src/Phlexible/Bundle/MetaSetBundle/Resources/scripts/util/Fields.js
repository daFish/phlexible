Ext.define('Phlexible.metaset.util.Fields', {
    constructor: function () {
        this.initFields();
        this.initEditors();
        this.initSelectEditorCallbacks();
        this.initBeforeEditCallbacks();
        this.initAfterEditCallbacks();

        this.callParent(arguments);
    },

    getFields: function () {
        return this.fields;
    },

    getStoreData: function () {
        var data = [];
        for (var key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            data.push([key, this.fields[key].title]);
        }
        return data;
    },

    getEditors: function () {
        var editors = [];
        for (var key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (this.fields[key].editor) {
                editors.push([key, this.fields[key].editor]);
            }
        }
        return editors;
    },

    getSelectEditorCallbacks: function () {
        var callbacks = {}, key;
        for (key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (typeof this.fields[key].selectEditorCallback === 'function') {
                callbacks[key] = this.fields[key].selectEditorCallback;
            }
        }
        return callbacks;
    },

    getBeforeEditCallbacks: function () {
        var callbacks = {}, key;
        for (key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (typeof this.fields[key].beforeEditCallback === 'function') {
                callbacks[key] = this.fields[key].beforeEditCallback;
            }
        }
        return callbacks;
    },

    getAfterEditCallbacks: function () {
        var callbacks = {}, key;
        for (key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (typeof this.fields[key].afterEditCallback === 'function') {
                callbacks[key] = this.fields[key].afterEditCallback;
            }
        }
        return callbacks;
    },

    get: function(key) {
        return this.fields[key];
    },

    initEditors: function () {
        this.editors = {
            textfield: {
                xtype: 'textfield'
            },
            textarea: {
                xtype: 'textarea'
            },
            date: {
                xtype: 'datefield',
                format: 'd.m.Y'
            },
            'boolean': {
                xtype: 'combo',
                store: Ext.create('Ext.data.Store', {
                    fields: ['value'],
                    data: [
                        ['true'],
                        ['false']
                    ]
                }),
                displayField: 'value',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                typeAhead: false
            },
            select: {
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'value']
                }),
                valueField: 'key',
                displayField: 'value',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                typeAhead: false
            }
        };
    },

    initSelectEditorCallbacks: function () {
        this.selectEditorCallbacks = {
            select: function (editor, record) {
                var options = Phlexible.clone(record.data.options);
                if (!record.data.required) {
                    options.unshift(['', '(' + Phlexible.elements.Strings.empty + ')']);
                }
                editor.field.store.loadData(options);
            }
        };
    },

    initBeforeEditCallbacks: function () {
        this.beforeEditCallbacks = {
            suggest: function (grid, field, record) {
                if (grid.master !== undefined) {
                    var isSynchronized = (1 == record.get('synchronized'));

                    // skip editing english values if language is synchronized
                    if (!grid.master && isSynchronized) {
                        return false;
                    }
                }

                var w = Ext.create('Phlexible.metaset.window.MetaSuggestWindow', {
                    record: record,
                    valueField: field,
                    metaLanguage: grid.language,
                    listeners: {
                        store: function (options) {
                            record.set('options', options);
                        },
                        scope: this
                    }
                });
                w.show();
            },
            validate: function (record) {
                if (!record.get('options')) {
                    Ext.MessageBox.alert(Phlexible.metasets.Strings.failure, Phlexible.metasets.Strings.select_needs_options);
                    return false;
                }

                return true;
            }
        };
    }
});
