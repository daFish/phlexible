Ext.define('Phlexible.metaset.util.Fields', {
    constructor: function () {
        this.fields = {};
        this.editors = {};
        this.selectEditorCallbacks = {};
        this.beforeEditCallbacks = {};
        this.afterEditCallbacks = {};

        this.initFields();
    },

    getFields: function () {
        return this.fields;
    },

    getStoreData: function () {
        var data = [];
        Ext.Object.each(this.fields, function(key, field) {
            data.push([key, field.title]);
        });
        return data;
    },

    getEditors: function () {
        var editors = [];
        Ext.Object.each(this.fields, function(key, field) {
            if (field.editor) {
                editors.push([key, field.editor]);
            }
        });
        return editors;
    },

    getSelectEditorCallbacks: function () {
        var callbacks = {};
        Ext.Object.each(this.fields, function(key, field) {
            if (typeof field.selectEditorCallback === 'function') {
                callbacks[key] = field.selectEditorCallback;
            }
        });
        return callbacks;
    },

    getBeforeEditCallbacks: function () {
        var callbacks = {};
        Ext.Object.each(this.fields, function(key, field) {
            if (typeof field.beforeEditCallback === 'function') {
                callbacks[key] = field.beforeEditCallback;
            }
        });
        return callbacks;
    },

    getAfterEditCallbacks: function () {
        var callbacks = {};
        Ext.Object.each(this.fields, function(key, field) {
            if (typeof field.afterEditCallback === 'function') {
                callbacks[key] = field.afterEditCallback;
            }
        });
        return callbacks;
    },

    get: function(key) {
        return this.fields[key];
    },

    set: function(key, field) {
        if (!field.title) {
            throw new Error('Title is required.');
        }

        if (!field.editor) {
            field.editor = null;
        }
        if (!field.configure) {
            field.configure = Ext.emptyFn;
        }
        if (!field.validate) {
            field.validate = Ext.emptyFn;
        }

        this.fields[key] = field;
    },

    initFields: function () {
        this.set('textfield', {
            title: 'Textfield',
            editor: new Ext.form.TextField()
        });
        this.set('textarea', {
            title: 'Textarea',
            editor: new Ext.form.TextArea()
        });
        this.set('date', {
            title: 'Date',
            editor: new Ext.form.DateField({format: 'd.m.Y'})
        });
        this.set('boolean', {
            title: 'Boolean',
            editor: new Ext.form.ComboBox({
                store: new Ext.data.SimpleStore({
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
            })
        });
        this.set('select', {
            title: 'Select',
            editor: new Ext.form.ComboBox({
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'value']
                }),
                valueField: 'key',
                displayField: 'value',
                mode: 'local',
                triggerAction: 'all',
                editable: false,
                typeAhead: false
            }),
            selectEditorCallback: function (editor, record) {
                var options = Phlexible.clone(record.data.options);
                if (!record.data.required) {
                    options.unshift(['', '(' + Phlexible.elements.Strings.empty + ')']);
                }
                editor.field.store.loadData(options);
            },
            configure: function (record) {
                var w = new Phlexible.metasets.SelectConfigurationWindow({
                    options: record.get('options'),
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
        });
    }
});
