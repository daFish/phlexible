Ext.define('Phlexible.mediamanager.view.FileMetas', {
    extend: 'Ext.grid.GridPanel',
    xtype: 'mediamanager.file-metas',

    title: '_FileMetaGrid',
    cls: 'p-mediamanager-meta-grid',
    //iconCls: 'p-metaset-component-icon',
    stripeRows: true,
    enableColumnMove: false,
    enableColumnHide: true,
    deferEmptyText: true,

    small: false,

    emptyText: '_emptyText',
    keyText: '_keyText',
    valueText: '_valueText',

    initComponent: function () {
        if (this.small) {
            this.enableColumnHide = false;
        }

        this.initMyStore();
        this.initMyColumns();
        //this.initMySelectionModel();
        this.initMyPlugins();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        var fields = ['key', 'type', 'options', 'required', 'synchronized', 'readonly'];

        Ext.each(Phlexible.Config.get('set.language.meta'), function (language) {
            fields.push('value_' + language[0]);
        }, this);

        this.fieldData = this.fieldData || [];

        this.store = Ext.create('Ext.data.Store', {
            fields: fields,
            data: this.fieldData,
            listeners: {
                load: function (store, records) {
                    // if no required fields are present for a file
                    // -> hide the 'required' column
                    var hasRequiredFields = false;
                    for (var i = records.length - 1; i >= 0; --i) {
                        if (1 == records[i].get('required')) {
                            hasRequiredFields = true;
                            break;
                        }
                    }
                    this.getColumnModel().setHidden(1, !hasRequiredFields);

                    this.validateMeta();
                },
                scope: this
            }
        });

        delete this.fieldData;
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.keyText,
                dataIndex: 'key',
                width: 100
            },
            {
                header: '&nbsp;',
                dataIndex: 'required',
                width: 30,
                renderer: function (v) {
                    return 1 == v ? /*Phlexible.inlineIcon('p-mediamanager-wizard_required-icon')*/ 'wizard' : '';
                }
            }
        ];

        Ext.each(Phlexible.Config.get('set.language.meta'), function (language) {
            this.columns.push({
                header: this.valueText + ' ' + language[2] + ' ' + language [1],
                dataIndex: 'value_' + language[0],
                language: language[0],
                flex: 1,
                hidden: false,//this.small && language[0] !== Phlexible.Config.get('language.metasets'),
                xrenderer: this.formatField,
                xgetEditor: function(record) {
                    var type = record.get('editType');

                    if (type === 'text') {
                        return Ext.create('Ext.grid.CellEditor', {
                            field: Ext.create('Ext.form.field.Text')
                        });
                    } else if (type === 'combo') {
                        return Ext.create('Ext.grid.CellEditor', {
                            field: combo
                        });
                    }
                }
            });
        }, this);
    },

    initMyPlugins: function() {
        this.plugins = [{
            ptype: 'cellediting',
            clicksToEdit: 1
        }];
    },

    initMySelectionModel: function() {
        var metaFields = Ext.create('Phlexible.metaset.util.Fields');

        this.cm = new Phlexible.gui.grid.TypeColumnModel({
            columns: columns,
            store: this.store,
            grid: this,
            editors: metaFields.getEditors(),
            selectEditorCallbacks: metaFields.getSelectEditorCallbacks(),
            beforeEditCallbacks: metaFields.getBeforeEditCallbacks(),
            afterEditCallbacks: metaFields.getAfterEditCallbacks()
        });
    },

    initMyListeners: function() {
        this.on({
            beforeedit: function (e) {
                // skip editing english values if language is synchronized
                var record = e.record;
                var ci = e.column;
                var isSynchronized = (1 == record.get('synchronized'));
                var cm = this.getColumnModel();
                var column = cm.getColumnById(cm.getColumnId(ci));
                if (isSynchronized && (!column.language || column.language !== Phlexible.Config.get('language.metasets'))) {
                    return false;
                }
                if (e.record.data.readonly) {
                    return false;
                }
            },
            afteredit: function (e) {
                this.validateMeta();
            },
            scope: this
        });

    },

    setFieldData: function (fieldData) {
        this.getStore().loadData(fieldData);
    },

    getFieldData: function () {
        var data = {};
        var records = this.store.getRange();

        for (var i = 0; i < records.length; i++) {
            var key = records[i].data.key;
            var values = records[i].data;
            if (values.type == 'date') {
                for (var j in values) {
                    if (j.substr(0, 6) === 'value_') {
                        values[j] = this.formatDate(values[j]);
                    }
                }
            }
            data[key] = values;
        }

        return data;
    },

    formatDate: function (v) {
        if (typeof v !== 'object') {
            var dt = Date.parseDate(v, 'Y-m-d');
        }
        else {
            var dt = v;
        }

        if (dt) {
            v = dt.format('d.m.Y');
        }

        return v;
    },

    formatField: function (v, md, r, ri, ci, store) {

        var isSynchronized = (1 == r.data['synchronized']);

        // mark synchronized fields
        if (isSynchronized) {
            var cm = this.getColumnModel();
            var language = cm.getColumnById(cm.getColumnId(ci)).language;
            if (language) {
                if (language === Phlexible.Config.get('language.metasets')) {
                    md.attr = 'style="border:1px solid green;"';
                }
                else {
                    md.attr = 'style="border:1px solid red;"';
                }
            }
        }

        if (v && r.data.type == 'date') {
            v = this.formatDate(v);
        }
        else if (v && r.data.type === 'select') {
            for (var i = 0; i < r.data.options.length; i++) {
                if (r.data.options[i][0] == v) {
                    v = r.data.options[i][1];
                    break;
                }
            }
        }

        return v;
    },

    validateMeta: function () {
        var valid = true;
        var languages = Phlexible.Config.get('set.language.meta');
        var language;
        var defaultLanguage = Phlexible.Config.get('language.metasets');

        var metaRecords = this.getStore().getRange();

        for (var i = 0; i < metaRecords.length; i++) {
            row = metaRecords[i].data;

            if (1 == row['synchronized']) {
                for (var j = 0; j < languages.length; ++j) {
                    language = languages[j][0];
                    if (language !== defaultLanguage) {
                        metaRecords[i].set('value_' + language, row['value_' + defaultLanguage]);
                    }
                }
            }

            if (1 == row.required) {
                valid &= this.isRequiredFieldFilled(row);
            }
        }

        /*
         var tbar = this.getTopToolbar();
         if (valid) {
         tbar.items.items[0].enable();
         } else {
         tbar.items.items[0].disable();
         }
         */

        return valid;
    },

    isRequiredFieldFilled: function (data) {
        var code, field;
        var languages = Phlexible.Config.get('set.language.meta');

        for (var i = 0; i < languages.length; ++i) {
            code = languages[i][0];
            field = 'value_' + code;

            if (!data[field]) {
                return false;
            }
        }

        return true;
    }

});
