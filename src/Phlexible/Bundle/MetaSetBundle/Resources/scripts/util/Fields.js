Ext.provide('Phlexible.metasets.util.Fields');

Phlexible.metasets.util.Fields = function (config) {
    this.fields = {};
    this.editors = {};
    this.selectEditorCallbacks = {};
    this.beforeEditCallbacks = {};
    this.afterEditCallbacks = {};

    this.initFields();

    Phlexible.metasets.util.Fields.superclass.constructor.call(this, config);
};
Ext.extend(Phlexible.metasets.util.Fields, Ext.util.Observable, {
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
        var selectEditorCallbacks = {};
        for (var key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (this.fields[key].selectEditorCallback) {
                selectEditorCallbacks.key = this.fields[key].selectEditorCallback;
            }
        }
        return selectEditorCallbacks;
    },

    getBeforeEditCallbacks: function () {
        var selectEditorCallbacks = {};
        for (var key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (this.fields[key].selectEditorCallbacks) {
                selectEditorCallbacks.key = this.fields[key].selectEditorCallbacks;
            }
        }
        return selectEditorCallbacks;
    },

    getAfterEditCallbacks: function () {
        var afterEditCallbacks = {};
        for (var key in this.fields) {
            if (!this.fields.hasOwnProperty(key)) {
                continue;
            }
            if (this.fields[key].afterEditCallbacks) {
                afterEditCallbacks.key = this.fields[key].afterEditCallbacks;
            }
        }
        return afterEditCallbacks;
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
        if (!field.selectEditor) {
            field.selectEditor = Ext.emptyFn;
        }
        if (!field.beforeEdit) {
            field.beforeEdit = Ext.emptyFn;
        }
        if (!field.afterEdit) {
            field.afterEdit = Ext.emptyFn;
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
            selectEditor: function (editor, record) {
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
