Ext.define('Phlexible.elementtype.ElementtypesList', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.elementtype-list',

    collapsible: true,
    cls: 'p-elementtypes-list',
    selectedRecord: 0,
    collapseFirst: false,
    loadMask: true,

    mode: Phlexible.elementtypes.TYPE_FULL,

    idText: '_idText',
    nameText: '_nameText',
    versionText: '_versionText',
    fullText: '_fullText',
    structureText: '_structureText',
    referenceText: '_referenceText',
    layoutText: '_layoutText',
    partText: '_partText',
    createText: '_createText',
    deleteText: '_deleteText',
    duplicateText: '_duplicateText',
    loadInTemplateTreeText: '_loadInTemplateTreeText',

    /**
     * @event beforeElementtypeChange
     * Fires before the active ElementType has been changed
     * @param {Number} elementtype_id The ID of the selected ElementType.
     * @param {String} elementtype_title The Title of the selected ElementType.
     */

    /**
     * @event elementtypeChange
     * Fires after the active ElementType has been changed
     * @param {Number} elementtype_id The ID of the selected ElementType.
     * @param {String} elementtype_title The Title of the selected ElementType.
     */

    initComponent: function () {
        if (!this.params) {
            this.params = {};
        }

        if (!this.params.type) {
            this.params.type = 'full';
        }

        this.initMyStore();
        this.initMyColumns();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            fields: Phlexible.elementtypes.model.Elementtype,
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('elementtypes_list', {type: this.params.type}),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'elementtypes',
                    idProperty: 'id',
                    totalProperty: 'total'
                }
            },
            autoLoad: true,
            sorters: [{
                field: 'title',
                direction: 'ASC'
            }],
            listeners: {
                load: function (store) {
                    this.fireEvent('changes', !!store.reader.jsonData.changes);

                    if (this.params.elementtype_id && this.params.version) {
                        var r = store.getById(this.params.elementtype_id);
                        if (!r) return;
                        var index = store.indexOf(r);

                        this.getSelectionModel().selectRecords([r], false);
                        this.elementtypeSelect(index, r, this.params.version);

                        this.params.elementtype_id = null;
                        this.params.version = null;
                    }
                },
                scope: this
            }
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                dataIndex: 'id',
                width: 34,
                resizable: false,
                hidden: true
            },
            {
                header: this.nameText,
                dataIndex: 'title',
                width: 120,
                resizable: false,
                renderer: Phlexible.elementtypes.Format.title
            },
            {
                header: this.versionText,
                dataIndex: 'version',
                width: 35,
                resizable: false//,
            }
        ];

        this.viewConfig = {
            listeners: {
                refresh: this.onViewRefresh,
                scope: this
            }
        };

        this.tbar = [
            {
                xtype: 'cycle',
                showText: true,
                changeHandler: function (btn, item) {
                    this.mode = item.source;
                    this.store.proxy.conn.url = Phlexible.Router.generate('elementtypes_list', {type: this.mode});
                    this.store.reload();
                },
                scope: this,
                items: [
                    {
                        text: this.fullText,
                        iconCls: 'p-elementtype-type_full-icon',
                        checked: this.params.type == Phlexible.elementtypes.TYPE_FULL,
                        source: Phlexible.elementtypes.TYPE_FULL,
                        scope: this
                    },
                    {
                        text: this.structureText,
                        iconCls: 'p-elementtype-type_structure-icon',
                        checked: this.params.type == Phlexible.elementtypes.TYPE_STRUCTURE,
                        source: Phlexible.elementtypes.TYPE_STRUCTURE,
                        scope: this
                    },
                    {
                        text: this.referenceText,
                        iconCls: 'p-elementtype-type_reference-icon',
                        checked: this.params.type == Phlexible.elementtypes.TYPE_REFERENCE,
                        source: Phlexible.elementtypes.TYPE_REFERENCE,
                        scope: this
                    },
                    {
                        text: this.layoutText,
                        iconCls: 'p-elementtype-type_layoutarea-icon',
                        checked: this.params.type == Phlexible.elementtypes.TYPE_LAYOUTAREA,
                        source: Phlexible.elementtypes.TYPE_LAYOUTAREA,
                        scope: this
                    },
                    {
                        text: this.partText,
                        iconCls: 'p-elementtype-type_part-icon',
                        checked: this.params.type == Phlexible.elementtypes.TYPE_PART,
                        source: Phlexible.elementtypes.TYPE_PART,
                        scope: this
                    }
                ]
            },
            '-',
            {
                text: this.createText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: function () {
                    var w = Ext.create('Phlexible.elementtypes.window.NewElementtypeWindow', {
                        type: this.mode,
                        listeners: {
                            success: this.onElementtypeCreate,
                            scope: this
                        }
                    });
                    w.show();
                },
                scope: this
            }
        ];

        this.contextMenu = new Ext.menu.Menu({
            items: [
                {
                    itemId: 'name',
                    cls: 'x-btn-text-icon-bold',
                    text: '.'
                },
                '-',
                {
                    itemId: 'deleteBtn',
                    text: this.deleteText,
                    iconCls: 'p-elementtype-delete-icon',
                    handler: function (item) {
                        Ext.MessageBox.confirm('Confirm', 'Do you really want to delete this Element Type?<br /><br /><br /><b>PLEASE NOTE:</b><br /><br />All Elements based on this Element Type will also be deleted.', function (btn, text, x, item) {
                            if (btn == 'yes') {
                                this.onDelete(item.parentMenu.record);
                            }
                        }.createDelegate(this, [item], true));
                    },
                    scope: this
                },
                '-',
                {
                    itemId: 'duplicateBtn',
                    text: this.duplicateText,
                    iconCls: 'p-elementtype-duplicate-icon',
                    handler: function (item) {
                        this.onDuplicate(item.parentMenu.record);
                    },
                    scope: this
                },
                '-',
                {
                    text: this.loadInTemplateTreeText,
                    iconCls: 'p-elementtype-tree_template-icon',
                    handler: function (item) {
                        this.onElementtypeTemplateSelect(this, item.parentMenu.index);
                    },
                    scope: this
                }
            ]
        });

        this.addListener({
            rowcontextmenu: function (grid, index, event) {
                event.stopEvent();

                var r = grid.store.getAt(index);

                this.contextMenu.index = index;
                this.contextMenu.record = r;

                this.contextMenu.items.items[0].setText(this.contextMenu.record.get('title'));

                var coords = event.getXY();
                this.contextMenu.showAt([coords[0], coords[1]]);
            },
            rowdblclick: this.onElementtypeSelect,
            scope: this
        });

        Phlexible.elementtypes.ElementtypesList.superclass.initComponent.call(this);
    },

    load: function (type, elementtype_id, version) {
        var cycle = this.getTopToolbar().items.items[0];
        //Phlexible.console.log(cycle);
        var foundBtn = false;
        cycle.menu.items.each(function (btn) {
            if (type == btn.source) {
                foundBtn = btn;
                return false;
            }
        }, this);
        if (foundBtn) {
            this.params = {
                elementtype_id: elementtype_id,
                version: version
            };
            cycle.setActiveItem(foundBtn);
        }
    },

    onLoad: function (store) {
        if (!this.selectedRecord) {
            if (this.store.getCount()) {
                this.selModel.selectFirstRow();
                var index = this.store.indexOf(this.selModel.getSelected());
                this.onElementtypeSelect(this, index);
            }
        }
        /* else {
         var index = this.store.indexOf(this.selModel.getSelected());
         this.onElementtypeSelect(this, index)
         this.selModel.selectRecords([this.selectedRecord]);
         }*/
    },

    onElementtypeCreate: function (values) {
        this.store.reload();

        // activate to switch to created ElementType
        //this.nextSelectedID = id;
    },

    onElementtypeSelect: function (grid, index, e, version, force) {
        var r = this.store.getAt(index);

        if (!version) {
            version = r.get('version');
        }
        if (force || !this.selectedRecord || this.selectedRecord.id != r.id) {

            if (this.fireEvent('beforeElementtypeChange', r.id, r.get('title'), this.elementtypeSelect.createDelegate(this, [index, r, version], false)) === false) {
                return;
            }

            this.elementtypeSelect(index, r, null);
        }
    },

    elementtypeSelect: function (index, r, version) {
        if (this.selectedRecord) {
            var selectedIndex = this.store.indexOf(this.selectedRecord);
            if (selectedIndex != -1) {
                var row = Ext.get(this.view.getRow(selectedIndex));
                row.removeClass('p-elementtype-grid-selected');
            }
        }

        var row = Ext.get(this.view.getRow(index));
        row.addClass('p-elementtype-grid-selected');

        this.selectedRecord = r;

        this.fireEvent('elementtypeChange', r.id, r.get('title'), version, r.get('type'));
    },

    onElementtypeTemplateSelect: function (grid, index) {
        var r = this.store.getAt(index);
        if (this.fireEvent('beforeElementtypeTemplateChange', r.id, r.get('title')) === false) {
            return;
        }

        this.fireEvent('elementtypeTemplateChange', r.id, r.get('title'));
    },

    onViewRefresh: function (view) {
        if (this.selectedRecord) {
            var selectedIndex = this.store.indexOf(this.selectedRecord);
            if (selectedIndex != -1) {
                var row = Ext.get(view.getRow());
                row.addClass('p-elementtype-grid-selected');
            }
        }
    },

    onDelete: function (r) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_list_delete', {id: r.id}),
            success: this.onDeleteSuccess,
            scope: this
        });
    },

    onDeleteSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            Phlexible.success(data.msg);

            if (this.selectedRecord.id == data.id) {
                this.selectedRecord = false;
            }
        } else {
            Ext.MessageBox.alert('Failure', data.msg);
        }

        this.store.reload();
    },

    onDuplicate: function (r) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_list_duplicate', {id: r.id}),
            success: this.onDuplicateSuccess,
            scope: this
        });
    },

    onDuplicateSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.store.reload();
        } else {
            Ext.MessageBox.alert('Failure', data.msg);
        }
    }
});
