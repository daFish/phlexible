Ext.define('Phlexible.siteroot.view.Urls', {
    extend: 'Ext.grid.Panel',

    xtype: 'siteroot.urls',

    border: false,
    emptyText: '_emptyText',

    idText: '_idText',
    defaultText: '_defaultText',
    hostnameText: '_hostnameText',
    languageText: '_languageText',
    targetText: '_targetText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addMappingText: '_addMappingText',
    emptyUrlText: '_emptyUrlText',
    emptyTargetText: '_emptyTargetText',
    emptyLanguageText: '_emptyLanguageText',
    actionsText: '_actionsText',

    initComponent: function () {
        this.initMyColumns();
        this.initMyPlugins();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                hidden: true,
                dataIndex: 'id'
            },
            {
                xtype: 'checkcolumn',
                header: this.defaultText,
                dataIndex: 'default',
                languageIndex: 'language',
                width: 50
            },
            {
                id: 'hostname',
                header: this.hostnameText,
                dataIndex: 'hostname',
                sortable: true,
                vtype: 'url',
                flex: 1,
                editor: {
                    xtype: 'textfield',
                    allowBlank: false
                }
            },
            {
                header: this.languageText,
                dataIndex: 'language',
                sortable: true,
                renderer: this.renderLanguage,
                width: 100,
                editor: {
                    xtype: 'iconcombo',
                    allowBlank: true,
                    editable: false,
                    triggerAction: 'all',
                    selectOnFocus: false,
                    mode: 'local',
                    displayField: 'title',
                    valueField: 'language',
                    iconClsField: 'icon',
                    emptyText: '',
                    store: Ext.create('Ext.data.SimpleStore', {
                        model: 'Phlexible.gui.model.KeyValueIconCls',
                        data: Phlexible.Config.get('set.language.frontend')
                    })
                }
            },
            {
                header: this.targetText,
                dataIndex: 'target',
                sortable: true,
                width: 200,/*,
             TODO: enable
             editor: Ext.reate('Phlexible.elements.EidSelector', {
             labelSeparator: '',
             element: {
             siteroot_id: this.siterootId
             },
             width: 300,
             listWidth: 283,
             treeWidth: 283
             })*/
                editor: {
                    xtype: 'numberfield'
                }
            },
            {
                xtype: 'actioncolumn',
                text: this.actionsText,
                width: 30,
                items: [{
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    tooltip: this.removeText,
                    handler: function (grid, rowIndex, colIndex, item, e, url) {
                        Ext.MessageBox.confirm(this.removeText, this.removeDescriptionText, function (btn) {
                            if (btn === 'yes') {
                                this.onDeleteUrl(url);
                            }
                        }, this);
                    },
                    scope: this
                }]
            }
        ];
    },

    initMyPlugins: function() {
        this.plugins = [{
            ptype: 'cellediting',
            clicksToEdit: 1
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            border: false,
            items: [{
                text: this.addMappingText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddUrl,
                scope: this
            }]
        }];
    },

    initMyListeners: function() {
        this.on({
            afterEdit: this.onValidateEdit,
            scope: this
        });
    },

    renderLanguage: function (v, md, r, ri, ci, store) {
        return v;
        var editor = this.getColumnModel().getCellEditor(3, 0);

        var estore = editor.field.store;
        var eri = estore.find('language', v);
        if (eri !== -1) {
            return estore.getAt(eri).get('language');
        }

        return v;
    },

    onValidateEdit: function (event) {

        if (event.record.get('hostname') === '') {
            this.startEditing(event.row, 2);
        }

        if (event.record.get('target') === '') {
            this.startEditing(event.row, 4);
        }
    },

    /**
     * Add url
     */
    onAddUrl: function () {
        var url = Ext.create('Phlexible.siteroot.model.Url', {
            id: '',
            hostname: '',
            language: '',
            target: ''
        });

        // add empty record to store
        this.store.insert(0, url);
        this.selModel.selectFirstRow();
        this.startEditing(0, 2);
    },

    /**
     * Remove url
     *
     * @param {Phlexible.siteroot.model.Url} url
     */
    onDeleteUrl: function (url) {
        this.store.remove(url);
    },

    isValid: function () {
        var valid = true;

        this.store.each(function (r) {
            if (!r.data.target || !r.data.hostname || !r.data.language) {
                valid = false;
                return false;
            }
        }, this);

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        // check data
        var valid = true;
        Ext.each(this.store.getModifiedRecords() || [], function(r) {
            if (r.data.hostname.length <= 0) {
                Phlexible.Notify.failure(this.emptyUrlText);
                valid = false;
                return false;
            }

            if (r.data.target.length <= 0) {
                Phlexible.Notify.failure(this.emptyTargetText);
                valid = false;
                return false;
            }

            if (r.data.language.length <= 0) {
                Phlexible.Notify.failure(this.emptyLanguageText);
                valid = false;
                return false;
            }
        });

        if (!valid) {
            return false;
        }

        // fetch deleted records
        var deleted = [];
        Ext.each(this.deletedRecords || [], function (r) {
            if (r.data.id.length > 0) {
                deleted.push(r.data.id);
            }
        });

        // fetch modified records
        var created = [];
        var modified = [];
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            if (r.data.id) {
                modified.push(r.data);
            } else {
                created.push(r.data);
            }
        });

        return {
            mappings: {
                deleted: deleted,
                modified: modified,
                created: created
            }
        };
    }

});
