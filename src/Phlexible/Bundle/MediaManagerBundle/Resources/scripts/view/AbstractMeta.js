Ext.define('Phlexible.mediamanager.view.AbstractMeta', {
    extend: 'Ext.tree.Panel',
    requires: [
        'Phlexible.metaset.window.MetaSetsWindow'
    ],

    cls: 'p-mediamanager-meta',
    iconCls: Phlexible.Icon.get('weather-cloud'),
    rootVisible: false,
    animate: false,

    small: false,
    params: null,

    saveText: '_saveText',
    metasetsText: '_metasetsText',
    noValuesText: '_noValuesText',
    fillRequiredFieldsText: '_fillRequiredFieldsText',
    emptyText: '_emptyText',
    keyText: '_keyText',
    valueText: '_valueText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);

        if (this.rights) {
            this.setRights(this.rights);
        }
        if (this.params) {
            this.loadMeta(this.params);
        }
    },

    getMetaRoute: function() {
        throw new Error("Implement getMetaRoute()");
    },

    getMetasetRoute: function() {
        throw new Error("Implement getMetasetRoute()");
    },

    getMetasetModel: function() {
        throw new Error("Implement getMetasetModel()");
    },

    getCheckRight: function() {
        throw new Error("Implement getCheckRight()");
    },

    initMyStore: function() {
        this.fieldData = this.fieldData || [];

        this.store = Ext.create('Ext.data.TreeStore', {
            model: this.getMetasetModel(),
            data: this.fieldData,
            autoLoad: false,
            listeners: {
                load: function (store, records) {
                    return; // TODO: check
                    // if no required fields are present for a file
                    // -> hide the 'required' column
                    var hasRequiredFields = false,
                        i;
                    for (i = records.length - 1; i >= 0; --i) {
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
                xtype: 'treecolumn',
                header: this.keyText,
                dataIndex: 'name',
                width: 100
            },
            {
                header: '&nbsp;',
                dataIndex: 'required',
                width: 30,
                renderer: function (v) {
                    return v ? Phlexible.Icon.get('exclamation') : '&nbsp;';
                }
            }
        ];

        Ext.each(Phlexible.Config.get('set.language.meta'), function (language) {
            this.columns.push({
                header: this.valueText + ' ' + language[2] + ' ' + language [1],
                language: language[0],
                flex: 1,
                hidden: false,//this.small && language[0] !== Phlexible.Config.get('language.metasets'),
                renderer: function(v, md, r) {
                    return r.get('de'); // TODO: language
                },
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

    initMyDockedItems: function () {
        var languageBtns = [];
        Ext.each(Phlexible.Config.get('set.language.meta'), function (language) {
            languageBtns.push({
                text: language[1],
                iconCls: language[2],
                language: language[0],
                checked: Phlexible.Config.get('language.metasets') === language
            });
        }, this);

        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'tbar',
            dock: 'top',
            border: false,
            items: [
                {
                    text: this.saveText,
                    itemId: 'saveBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.save,
                    scope: this
                },
                '->',
                {
                    xtype: 'cycle',
                    itemId: 'languageBtn',
                    showText: !this.small,
                    menu: languageBtns,
                    hidden: !this.small,
                    changeHandler: function (btn, item) {
                        this.changeLanguage(item.language);
                    },
                    scope: this
                },
                '-',
                {
                    text: this.metasetsText,
                    itemId: 'metasetsBtn',
                    iconCls: Phlexible.Icon.get('weather-clouds'),
                    handler: function () {
                        var w = Ext.create('Phlexible.metaset.window.MetaSetsWindow', {
                            baseParams: this.params,
                            metasetUrl: Phlexible.Router.generate(this.getMetasetRoute(), this.params),
                            metasetModel: this.getMetasetModel(),
                            listeners: {
                                savesets: this.reloadMeta,
                                scope: this
                            }
                        });
                        w.show();
                    },
                    scope: this
                }
            ]
        }];
    },

    loadMeta: function (params) {
        this.params = params;
        this.getStore().getProxy().setUrl(Phlexible.Router.generate(this.getMetaRoute(), params));
        Ext.Object.each(params, function(key, value) {
            this.getStore().getProxy().setExtraParam(key, value);
        }, this);
        this.getStore().load();
        return;
        Ext.Ajax.request({
            url: this.urls.load,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText), items = [];

                this.removeAll();
                if (data.meta && data.meta.length) {
                    Ext.each(data.meta, function (meta) {
                        items.push(this.createMetaGridConfig(meta.set_id, meta.title, meta.fields, this.small));
                    }, this);
                } else {
                    items.push({
                        border: false,
                        html: '<div class="x-grid-empty">' + this.strings.no_meta_values + '</div>'
                    });
                }

                this.add(items);
            },
            scope: this
        });
    },

    reloadMeta: function() {
        this.loadMeta(this.params);
    },

    createMetaGridConfig: function(setId, title, fieldData, small) {
        throw new Error("Implement createMetaGridConfig");
    },

    validateMeta: function() {
        this.items.each(function(p) {
            if (!p.validateMeta()) {
                Phlexible.Notify.failure(this.fillRequiredFieldsText);
                return false;
            }
        }, this);
    },

    changeLanguage: function(language) {
        this.items.each(function(p) {
            var cm = p.getColumnModel();
            Ext.each(cm.columns, function (column) {
                if (!column.language) {
                    return;
                }

                cm.setHidden(column.id, column.language != language);
                p.getView().layout();
            }, this);
        }, this);
    },

    setRights: function (rights) {
        if (rights.indexOf(this.getCheckRight()) != -1) {
            this.getDockedComponent('tbar').getComponent('saveBtn').show();
            this.getDockedComponent('tbar').getComponent('metasetsBtn').show();
        } else {
            this.getDockedComponent('tbar').getComponent('saveBtn').hide();
            this.getDockedComponent('tbar').getComponent('metasetsBtn').hide();
        }
    },

    empty: function () {
        this.getStore().removeAll();
    },

    save: function () {
        this.validateMeta();

        var sources = {};
        this.items.each(function(p) {
            sources[p.setId] = p.getFieldData();
        });
        var params = this.params;
        params.data = Ext.encode(sources);

        Ext.Ajax.request({
            url: this.urls.save,
            params: params,
            success: function (response) {
                var result = Ext.decode(response.responseText);
                if (result.success === false) {
                    Phlexible.Notify.failure(result.msg);
                }
                this.reloadMeta();
            },
            scope: this
        });
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
    }
});
