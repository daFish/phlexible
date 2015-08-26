Ext.define('Phlexible.siteroot.view.Navigations', {
    extend: 'Ext.grid.Panel',

    xtype: 'siteroot.navigations',

    border: false,
    emptyText: '_emptyText',

    titleText: '_titleText',
    handlerText: '_handlerText',
    startTidText: '_startTidText',
    maxDepthText: '_maxDepthText',
    flagsText: '_flagsText',
    additionalText: '_additionalText',
    removeText: '_removeText',
    removeDescriptionText: '_removeDescriptionText',
    addNavigationText: '_addNavigationText',
    emptyTitleText: '_emptyTitleText',
    actionsText: '_actionsText',

    initComponent: function () {
        this.initMyColumns();
        this.initMyPlugins();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.titleText,
                dataIndex: 'title',
                editor: {
                    xtype: 'textfield'
                },
                flex: 1
            },
            {
                header: this.handlerText,
                dataIndex: 'handler',
                width: 150,
                hidden: true
            },
            {
                header: this.startTidText,
                dataIndex: 'startTreeId',
                editor: {
                    xtype: 'numberfield'
                },
                width: 80
            },
            {
                header: this.maxDepthText,
                dataIndex: 'maxDepth',
                editor: {
                    xtype: 'numberfield'
                },
                width: 80
            },
            {
                header: this.flagsText,
                dataIndex: 'flags',
                editor: {
                    xtype: 'numberfield'
                },
                width: 80,
                hidden: true
            },
            {
                header: this.additionalText,
                dataIndex: 'additional',
                editor: {
                    xtype: 'textfield'
                },
                width: 100,
                hidden: true
            },
            {
                xtype: 'actioncolumn',
                header: this.actionsText,
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
                                    var w = Ext.create('Phlexible.siteroot.window.SiterootNavigationWindow', {
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
                        tooltip: this.flagsText,
                        handle: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            var w = Ext.create('Phlexible.siteroot.window.NavigationFlagsWindow', {
                                record: r
                            });

                            w.show();
                        },
                        scope: this
                    },
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.removeText,
                        handler: function (grid, rowIndex, colIndex) {
                            var r = grid.store.getAt(rowIndex);

                            Ext.MessageBox.confirm(this.removeText, this.removeDescriptionText, function (btn) {
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
                text: this.addNavigationText,
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
        var navigation = Ext.create('Phlexible.siteroot.model.Navigation', {
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
        this.store.insert(0, navigation);
        this.selModel.selectFirstRow();
        this.startEditing(0, 0);
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
                Phlexible.Notify.failure(this.emptyTitleText);
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