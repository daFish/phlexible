Ext.define('Phlexible.siteroots.NavigationGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.siteroots-navigations',

    title: Phlexible.siteroots.Strings.navigations,
    strings: Phlexible.siteroots.Strings,
    border: false,
    emptyText: Phlexible.siteroots.Strings.no_navigations,

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.siteroots.model.Navigation'
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.strings.title,
                dataIndex: 'title',
                editor: new Ext.form.TextField(),
                flex: 1
            },
            {
                header: this.strings.handler,
                dataIndex: 'handler',
                width: 150,
                hidden: true
            },
            {
                header: this.strings.start_tid,
                dataIndex: 'start_tid',
                editor: new Ext.form.TextField(),
                width: 80
            },
            {
                header: this.strings.max_depth,
                dataIndex: 'max_depth',
                editor: new Ext.form.NumberField(),
                width: 80
            },
            {
                header: this.strings.flags,
                dataIndex: 'flags',
                editor: new Ext.form.NumberField(),
                width: 80,
                hidden: true
            },
            {
                header: this.strings.additional,
                dataIndex: 'additional',
                editor: new Ext.form.TextField(),
                width: 100,
                hidden: true
            },
            {
                xtype: 'actioncolumn',
                width: 60,
                items: [
                    {
                        iconCls: Phlexible.Icon.get('wrench'),
                        hideIndex: 'hide_config',
                        tooltip: '_configure',
                        handler: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            switch (r.get('handler')) {
                                case 'Siteroot':
                                    var w = Ext.create('Phlexible.siteroots.SiterootNavigationWindow', {
                                        record: r,
                                        siterootId: this.siterootId
                                    });

                                    w.show();

                                    break;
                            }
                        },
                        scope: this
                    },
                    {
                        iconCls: Phlexible.Icon.get('flag'),
                        tooltip: this.strings.flags,
                        handle: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            var w = Ext.create('Phlexible.siteroots.NavigationFlagsWindow', {
                                record: r
                            });

                            w.show();
                        },
                        scope: this
                    },
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.strings.delete,
                        handler: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            Ext.MessageBox.confirm(this.strings.remove, this.strings.sure, function (btn) {
                                if (btn === 'yes') {
                                    this.onDeleteNavigation(r);
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
                text: this.strings.add_navigation,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.onAddNavigation,
                scope: this
            }]
        }];
    },

    /**
     * Action if site
     */
    onAddNavigation: function () {
        // create new empty record
        var newRecord = Ext.create('Phlexible.siteroots.model.Navigation', {
            id: '',
            siteroot_id: this.siterootId,
            title: '',
            handler: '',
            supports: 0,
            start_eid: 0,
            max_depth: 0,
            flags: 0
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

        this.siterootId = id;

        this.store.loadData(data.navigations);
    },

    /**
     * Start deletion of record.
     *
     * @param {Object} btn
     * @param {String} text
     * @param {Object} r
     */
    onDeleteNavigation: function (r) {
        if (!this.deletedRecords) {
            this.deletedRecords = [];
        }

        // remember record -> they are deleted on save
        this.deletedRecords.push(r);

        // delete record from store
        this.store.remove(r);
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {

        // check data
        var valid = true;
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            if (r.data.title.length <= 0) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_title_empty);
                valid = false;
                return false;
            }

            /*
            if (!r.handler) {
                Ext.Msg.alert(this.strings.failure, this.strings.err_handler_empty);
                valid = false;
                return false;
            }
             */
        });

        if (!valid) {
            return false;
        }

        // fetch deleted records
        var deleted = [];
        Ext.each(this.deletedRecords || [], function (r) {
            if (r.data.id) {
                deleted.push(r.data.id);
            }
        });

        // fetch modified records
        var modified = [], created = [];
        Ext.each(this.store.getModifiedRecords() || [], function (r) {
            if (r.data.id) {
                modified.push(r.data);
            } else {
                created.push(r.data);
            }
        });

        return {
            navigations: {
                deleted: deleted,
                modified: modified,
                created: created
            }
        };
    }

});