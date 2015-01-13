Ext.define('Phlexible.siteroots.UrlGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroots-urls',

    title: Phlexible.siteroots.Strings.url_mappings,
    strings: Phlexible.siteroots.Strings,
    border: false,
    emptyText: Phlexible.siteroots.Strings.no_url_mappings,

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroots.model.Url'
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: 'ID',
                hidden: true,
                dataIndex: 'id'
            },
            {
                xtype: 'checkcolumn',
                header: this.strings['default'],
                dataIndex: 'default',
                languageIndex: 'language',
                width: 50
            },
            {
                id: 'hostname',
                header: this.strings.hostname,
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
                header: this.strings.language,
                dataIndex: 'language',
                sortable: true,
                renderer: this.renderLanguage,
                width: 100,
                editor: {
                    xtype: 'iconcombo',
                    allowBlank: true,
                    editable: false,
                    triggerAction: 'all',
                    selectOnFocus: true,
                    mode: 'local',
                    displayField: 'title',
                    valueField: 'language',
                    iconClsField: 'icon',
                    emptyText: '',
                    store: Ext.create('Ext.data.SimpleStore', {
                        model: 'Phlexible.gui.model.KeyValueIconCls',
                        data: Phlexible.App.getConfig().get('set.language.frontend')
                    })
                }
            },
            {
                header: this.strings.target,
                dataIndex: 'target',
                sortable: true,
                width: 200/*,
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
            },
            {
                xtype: 'actioncolumn',
                items: [{
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    tooltip: this.strings.delete,
                    handler: function (grid, rowIndex, colIndex) {
                        var r = grid.getStore().getAt(rowIndex);

                        Ext.MessageBox.confirm(this.strings.remove, this.strings.sure, function (btn) {
                            if (btn === 'yes') {
                                this.onDeleteUrl(r);
                            }
                        }, this);
                    },
                    scope: this
                }]
            }
        ];
    },

    initMyDockedItems: function() {
        this.tbar = [
            {
                text: this.strings.add_mapping,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddUrl,
                scope: this
            }
        ];
    },

    initMyListeners: function() {
        this.on({
            afterEdit: this.onValidateEdit,
            scope: this
        });
    },

    renderLanguage: function (v, md, r, ri, ci, store) {
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
     * Action if site
     */
    onAddUrl: function () {

        // create new empty record
        var newRecord = new Phlexible.siteroots.model.Url({
            id: '',
            hostname: '',
            language: '',
            target: ''
        });

        // add empty record to store
        this.store.insert(0, newRecord);
        this.selModel.selectFirstRow();
        this.startEditing(0, 2);
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        this.deletedRecords = [];
        this.store.commitChanges();

        // remember current siteroot id
        this.siterootId = id;

        this.store.loadData(data.urls);

        /*
         TODO: enable
        var cm = this.getSelectionModel();
        var editor = cm.getCellEditor(4, 0);
        editor.field.setSiterootId(id);
         */
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} r
     */
    onDeleteUrl: function (r) {
        if (!this.deletedRecords) {
            this.deletedRecords = [];
        }

        // remember record -> they are deleted on save
        this.deletedRecords.push(r);

        // delete record from store
        this.store.remove(r);
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
                Ext.Msg.alert(this.strings.failure, this.strings.err_url_empty);
                valid = false;
                return false;
            }

            if (r.data.target.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_target_empty);
                valid = false;
                return false;
            }

            if (r.data.language.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_language_empty);
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
