Ext.ns('Phlexible.mediamanager');

Phlexible.mediamanager.FileMeta = Ext.extend(Ext.Panel, {
    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.file_meta,
    cls: 'p-mediamanager-meta',
    iconCls: 'p-metaset-component-icon',

    small: false,
    right: Phlexible.mediamanager.Rights.FILE_MODIFY,
    key: 'key',
    params: {},

    initUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_file_meta'),
            save: Phlexible.Router.generate('mediamanager_file_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_file_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_file_meta_sets_save'),
            available: Phlexible.Router.generate('metasets_sets_list')
        };
    },

    initComponent: function () {
        this.initUrls();

        this.items = [];

        this.populateTbar();

        Phlexible.mediamanager.FileMeta.superclass.initComponent.call(this);
    },

    populateTbar: function () {
        var toggleId = Ext.id();

        var languageBtns = [];
        Ext.each(Phlexible.Config.get('set.language.meta'), function (item) {
            var language = item[0];
            var t9n = item[1];
            var flag = item[2];
            languageBtns.push({
                text: t9n,
                iconCls: flag,
                language: language,
                checked: Phlexible.Config.get('language.metasets') === language
            });
        }, this);

        var cycleBtn = {
            xtype: 'cycle',
            showText: !this.small,
            items: languageBtns,
            hidden: !this.small,
            changeHandler: function (btn, item) {
                this.changeLanguage(item.language);
            },
            scope: this
        };

        this.tbar = [{
            text: this.strings.save,
            iconCls: 'p-mediamanager-meta_save-icon',
            handler: this.save,
            scope: this
        },
        '->',
        cycleBtn,
        '-',
        {
            text: this.strings.metasets,
            iconCls: 'p-metaset-component-icon',
            handler: function () {
                var w = new Phlexible.metasets.MetaSetsWindow({
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
        }];
    },

    loadMeta: function (params) {
        this.params = params;
        Ext.Ajax.request({
            url: this.urls.load,
            params: params,
            success: function (response) {
                var data = Ext.decode(response.responseText);

                this.removeAll();
                if (data.meta && data.meta.length) {
                    Ext.each(data.meta, function (meta) {
                        this.add(this.createMetaGridConfig(meta.set_id, meta.title, meta.fields, this.small));
                    }, this);
                } else {
                    this.add({
                        border: false,
                        xbodyStyle: 'padding: 5px;',
                        ctCls: 'x-grid-empty',
                        html: this.strings.no_meta_values
                    });
                }

                this.doLayout();
            },
            scope: this
        });
    },

    reloadMeta: function() {
        this.loadMeta(this.params);
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager-filemetagrid',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            data: fields
        };
    },

    validateMeta: function() {
        this.items.each(function(p) {
            if (!p.validateMeta()) {
                Ext.MessageBox.alert(this.strings.error, this.strings.fill_required_fields);
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
        if (rights.indexOf(this.right) != -1) {
            this.getTopToolbar().items.items[0].show();
            this.getTopToolbar().items.items[4].show();
        } else {
            this.getTopToolbar().items.items[0].hide();
            this.getTopToolbar().items.items[4].hide();
        }
    },

    empty: function () {
        this.removeAll();
    },

    save: function () {
        this.validateMeta();

        var sources = {};
        this.items.each(function(p) {
            sources[p.setId] = p.getData();
        })
        var params = this.params;
        params.data = Ext.encode(sources);

        Ext.Ajax.request({
            url: this.urls.save,
            params: params,
            success: function (response) {
                var result = Ext.decode(response.responseText);
                if (result.success === false) {
                    Ext.MessageBox.alert(this.strings.error, result.msg);
                }
                this.reloadMeta();
            },
            scope: this
        });
    },

    getData: function () {
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

Ext.reg('mediamanager-filemeta', Phlexible.mediamanager.FileMeta);
