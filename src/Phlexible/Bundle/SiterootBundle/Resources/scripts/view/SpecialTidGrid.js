Ext.define('Phlexible.siteroot.view.SpecialTidGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroot-specialtids',

    title: '_SpecialTidGrid',
    border: false,
    emptyText: '_emptyText',

    keyText: '_keyText',
    languageText: '_languageText',
    tidText: '_tidText',
    deleteText: '_deleteText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addSpecialTidText: '_addSpecialTidText',
    emptyKeyText: '_emptyKeyText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroot.model.SpecialTid',
            sorters: [{
                property: 'key',
                direction: 'asc'
            }]
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.keyText,
                dataIndex: 'key',
                flex: 1,
                sortable: true,
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
                header: this.tidText,
                dataIndex: 'tid',
                width: 200,
                sortable: true,
                editor: {
                    xtype: 'numberfield',
                    allowBlank: false
                }
            }, {
                xtype: 'actioncolumn',
                width: 30,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.removeText,
                        handler: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            Ext.MessageBox.confirm(this.removeText, this.removeDescriptionText, function (btn) {
                                if (btn === 'yes') {
                                    this.onDeleteSpecialTid(r);
                                }
                            }, this);
                        },
                        scope: this
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.addSpecialTidText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddSpecialTid,
                scope: this
            }]
        }]
    },

    /**
     * Action if site
     */
    onAddSpecialTid: function () {

        // create new empty record
        var newRecord = new Phlexible.siteroot.model.SpecialTid({
            id: '',
            siteroot_id: this.siterootId,
            key: '',
            language: '',
            tid: 0
        });

        // add empty record to store
        this.store.insert(0, newRecord);
        this.selModel.selectFirstRow();
        this.startEditing(0, 0);
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

        this.store.loadData(data.specialtids);
    },

    renderLanguage: function (v, md, r, ri, ci, store) {
        var editor = this.getColumnModel().getCellEditor(1, 0);

        var estore = editor.field.store;
        var eri = estore.find('language', v);
        if (eri !== -1) {
            return estore.getAt(eri).get('language');
        }

        return v;
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} r
     */
    onDeleteSpecialTid: function (r) {

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
            if (!r.data.tid) {
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
        Ext.each(this.store.getModifiedRecords() || [], function (r) {

            if (r.data.key.length <= 0) {
                Phlexible.Notify.failure(this.emptyKeyText);
                valid = false;
                return false;
            }

//            if(r.language.length <= 0) {
//                Ext.Msg.alert(this.strings.failure, this.strings.err_language_empty);
//                return false;
//            }
        });

        if (!valid) {
            return false;
        }

        // fetch modified records
        var records = [];
        Ext.each(this.store.getRange() || [], function (r) {
            records.push(r.data);
        });

        return {
            specialtids: records
        };
    }

});
