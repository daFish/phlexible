Ext.provide('Phlexible.tree.view.accordion.Meta');

Ext.require('Phlexible.metasets.util.Fields');
Ext.require('Phlexible.gui.grid.TypeColumnModel');

Phlexible.tree.view.accordion.Meta = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elements.Strings,
    title: Phlexible.elements.Strings.meta,
    tabTip: Phlexible.elements.Strings.meta,
    cls: 'p-tree-meta',
    iconCls: 'p-metaset-component-icon',
    autoHeight: true,
    autoScroll: true,
    autoExpandColumn: 1,
    viewConfig: {
        emptyText: 'No meta values defined.'
    },

    key: 'meta',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.store = new Ext.data.JsonStore({
            fields: ['key', 'type', 'options', 'value', 'readonly', 'required', 'synchronized'],
            listeners: {
                load: function () {
                    this.validateMeta();
                },
                scope: this
            }
        });

        this.sm = new Ext.grid.RowSelectionModel();

        var metaFields = new Phlexible.metasets.util.Fields();

        this.cm = new Phlexible.gui.grid.TypeColumnModel({
            columns: [
                {
                    header: this.strings.key,
                    dataIndex: 'key',
                    renderer: function (v, md, r) {
                        if (r.data.required) {
                            v = '<b>' + v + '</b>';
                        }
                        return v;
                    }
                },
                {
                    header: this.strings.value,
                    dataIndex: 'value',
                    editor: new Ext.form.TextField(),
                    renderer: function (v, md, r) {
                        if (r.data['synchronized']) {
                            if (this.master) {
                                md.css = md.css + ' synchronized-master';
                            } else {
                                md.css = md.css + ' synchronized-slave';
                            }
                        }

                        return v;
                    }.createDelegate(this)
                }
            ],
            store: this.store,
            grid: this,
            editors: metaFields.getEditors(),
            selectEditorCallbacks: metaFields.getSelectEditorCallbacks(),
            beforeEditCallbacks: metaFields.getBeforeEditCallbacks(),
            afterEditCallbacks: metaFields.getAfterEditCallbacks()
        });

        this.on({
            beforeedit: function (e) {
                var field = e.field;
                var record = e.record;
                var isSynchronized = (1 == record.get('synchronized'));

                // skip editing english values if language is synchronized
                if (!this.master && isSynchronized) {
                    return false;
                }
            },
            afteredit: function (e) {
                this.validateMeta();
            },
            scope: this
        });

        Phlexible.tree.view.accordion.Meta.superclass.initComponent.call(this);
    },

    onLoadElement: function (element) {
        if (element.getElementtypeType() != 'full' || !element.getMeta() || !element.getMeta().fields) {
            this.hide();
            return;
        }

        this.language = element.getLanguage();
        this.master = element.getIsMaster() || 0;

        this.store.removeAll();
        this.store.loadData(element.getMeta().fields);

        this.show();
    },

    getData: function () {
        var data = {};
        var records = this.store.getRange();

        for (var i = 0; i < records.length; i++) {
            data[records[i].data.key] = records[i].data['value'];
        }

        return data;
    },

    xupdateSource: function (response) {
        var source = Ext.decode(response.responseText);
        this.setSource(source);
    },

    isValid: function () {
        return this.validateMeta();
    },

    validateMeta: function () {
        var valid = true;

        var metaRecords = this.getStore().getRange();
        for (var i = 0; i < metaRecords.length; i++) {
            row = metaRecords[i].data;

            //if (1 == row['synchronized']) {
            //    metaRecords[i].set('value_en', row.value_de);
            //}

            if (1 == row.required) {
                valid &= !!row['value'];
            }
        }

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        this.metaValid = valid;

        return valid;
    }
});

Ext.reg('tree-accordion-meta', Phlexible.tree.view.accordion.Meta);
