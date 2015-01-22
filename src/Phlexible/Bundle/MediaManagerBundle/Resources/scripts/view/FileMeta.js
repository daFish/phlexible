Ext.define('Phlexible.mediamanager.view.FileMeta', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediamanager-file-meta',

    title: '_FileMeta',
    cls: 'p-mediamanager-meta',
    iconCls: Phlexible.Icon.get('weather-cloud'),

    small: false,
    checkRight: Phlexible.mediamanager.Rights.FILE_MODIFY,
    key: 'key',
    params: null,

    saveText: '_saveText',
    metasetsText: '_metasetsText',
    noValuesText: '_noValuesText',
    fillRequiredFieldsText: '_fillRequiredFieldsText',

    initComponent: function () {
        this.initMyUrls();
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);

        if (this.rights) {
            this.setRights(this.rights);
        }
        if (this.params) {
            this.loadMeta(this.params);
        }
    },

    initMyUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_file_meta'),
            save: Phlexible.Router.generate('mediamanager_file_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_file_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_file_meta_sets_save'),
            available: Phlexible.Router.generate('metaset_sets_list')
        };
    },

    initMyItems: function() {
        this.items = [{
            html: 'empty'
        }];
    },

    initMyDockedItems: function () {
        var languageBtns = [];
        Ext.each(Phlexible.App.getConfig().get('set.language.meta'), function (item) {
            var language = item[0];
            var t9n = item[1];
            var flag = item[2];
            languageBtns.push({
                text: t9n,
                iconCls: flag,
                language: language,
                checked: Phlexible.App.getConfig().get('language.metasets') === language
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
                            urls: this.metasetUrls,
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
                        ctCls: 'x-grid-empty',
                        html: this.noValuesText
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
        return {
            xtype: 'mediamanager-file-metas',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            fieldData: fieldData
        };
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
        if (rights.indexOf(this.checkRight) != -1) {
            this.getDockedComponent('tbar').getComponent('saveBtn').show();
            this.getDockedComponent('tbar').getComponent('metasetsBtn').show();
        } else {
            this.getDockedComponent('tbar').getComponent('saveBtn').hide();
            this.getDockedComponent('tbar').getComponent('metasetsBtn').hide();
        }
    },

    empty: function () {
        this.removeAll();
    },

    save: function () {
        this.validateMeta();

        var sources = {};
        this.items.each(function(p) {
            sources[p.setId] = p.getFieldData();
        })
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
