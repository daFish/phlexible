Ext.define('Phlexible.elementtype.view.List', {
    extend: 'Ext.grid.Panel',
    requires: [
        'Phlexible.elementtype.model.Elementtype'
    ],
    xtype: 'elementtype.list',

    iconCls: Phlexible.Icon.get('tree'),
    collapsible: true,
    cls: 'p-elementtypes-list',
    selectedRecord: 0,
    collapseFirst: false,
    loadMask: true,

    filterType: Phlexible.elementtype.TYPE_FULL,
    filterDeleted: false,

    idText: '_idText',
    nameText: '_nameText',
    versionText: '_versionText',
    deletedText: '_deletedText',
    fullText: '_fullText',
    structureText: '_structureText',
    referenceText: '_referenceText',
    layoutText: '_layoutText',
    partText: '_partText',
    createText: '_createText',
    deleteText: '_deleteText',
    duplicateText: '_duplicateText',
    loadInTemplateTreeText: '_loadInTemplateTreeText',
    confirmDeleteText: '_confirmDeleteText',

    /**
     * @event beforeElementtypeChange
     * Fires before the active ElementType has been changed
     * @param {Number} elementtypeId The ID of the selected ElementType.
     * @param {String} elementtypeTitle The Title of the selected ElementType.
     */

    /**
     * @event elementtypeChange
     * Fires after the active ElementType has been changed
     * @param {Number} elementtypeId The ID of the selected ElementType.
     * @param {String} elementtypeTitle The Title of the selected ElementType.
     */

    /**
     * @private
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
        this.initMyDockedItems();
        this.initMyContextMenu();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.elementtype.model.Elementtype',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('phlexible_api_elementtype_get_elementtypes'),
                reader: {
                    type: 'json',
                    rootProperty: 'elementtypes',
                    idProperty: 'id',
                    totalProperty: 'total'
                }
            },
            autoLoad: true,
            sorters: [{
                property: 'title',
                direction: 'ASC'
            }],
            filters: [{
                property: 'type',
                value: this.filterType
            },{
                property: 'deleted',
                value: this.filterDeleted
            }],
            listeners: {
                load: function (store) {
                    this.fireEvent('changes', !!store.getProxy().getReader().rawData.changes);

                    if (this.params.elementtypeId && this.params.version) {
                        var elementtype = store.getById(this.params.elementtypeId);
                        if (!elementtype) {
                            return;
                        }

                        this.getSelectionModel().select([elementtype]);
                        this.elementtypeSelect(elementtype);

                        this.params.elementtypeId = null;
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
                width: 100,
                resizable: false,
                hidden: true
            },
            {
                header: this.nameText,
                dataIndex: 'title',
                flex: 1,
                resizable: false,
                renderer: Phlexible.elementtype.Format.title
            },
            {
                header: this.versionText,
                dataIndex: 'revision',
                width: 50,
                resizable: false
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                text: this.createText,
                itemId: 'createBtn',
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: function () {
                    var id = 'new_' + Ext.id(),
                        elementtype = new Phlexible.elementtype.model.Elementtype({
                            new: 1,
                            uniqueId: id,
                            titles: {
                                de: id,
                                en: id
                            },
                            revision: 1,
                            type: this.filterType,
                            createdAt: new Date(),
                            createUser: Phlexible.Config.get('user.username'),
                            modifiedAt: new Date(),
                            modifyUser: Phlexible.Config.get('user.username')
                        });

                    this.getStore().add(elementtype);
                    this.getSelectionModel().select([elementtype], false);
                    this.elementtypeSelect(elementtype);
                },
                scope: this
            },
                '->',
            {
                xtype: 'button',
                itemId: 'deletedBtn',
                text: this.deletedText,
                iconCls: Phlexible.Icon.get('bin'),
                enableToggle: true,
                toggleHandler: function(btn, state) {
                    this.filterDeleted = state;
                    this.getStore().clearFilter();
                    this.getStore().addFilter({
                        property: 'type',
                        value: this.filterType
                    });
                    this.getStore().addFilter({
                        property: 'deleted',
                        value: this.filterDeleted
                    });
                },
                scope: this
            },{
                xtype: 'cycle',
                itemId: 'types',
                showText: true,
                changeHandler: function (btn, item) {
                    this.filterType = item.filterType;
                    this.getStore().clearFilter();
                    this.getStore().addFilter({
                        property: 'type',
                        value: this.filterType
                    });
                    this.getStore().addFilter({
                        property: 'deleted',
                        value: this.filterDeleted
                    });
                },
                scope: this,
                menu: {
                    items: [
                        {
                            text: this.fullText,
                            iconCls: Phlexible.elementtype.ICON_FULL,
                            checked: this.params.type == Phlexible.elementtype.TYPE_FULL,
                            filterType: Phlexible.elementtype.TYPE_FULL,
                            scope: this
                        },
                        {
                            text: this.structureText,
                            iconCls: Phlexible.elementtype.ICON_STRUCTURE,
                            checked: this.params.type == Phlexible.elementtype.TYPE_STRUCTURE,
                            filterType: Phlexible.elementtype.TYPE_STRUCTURE,
                            scope: this
                        },
                        {
                            text: this.referenceText,
                            iconCls: Phlexible.elementtype.ICON_REFERENCE,
                            checked: this.params.type == Phlexible.elementtype.TYPE_REFERENCE,
                            filterType: Phlexible.elementtype.TYPE_REFERENCE,
                            scope: this
                        },
                        {
                            text: this.layoutText,
                            iconCls: Phlexible.elementtype.ICON_LAYOUTAREA,
                            checked: this.params.type == Phlexible.elementtype.TYPE_LAYOUTAREA,
                            filterType: Phlexible.elementtype.TYPE_LAYOUTAREA,
                            scope: this
                        },
                        {
                            text: this.partText,
                            iconCls: Phlexible.elementtype.ICON_PART,
                            checked: this.params.type == Phlexible.elementtype.TYPE_PART,
                            filterType: Phlexible.elementtype.TYPE_PART,
                            scope: this
                        }
                    ]
                }
            }]
        }];
    },

    initMyContextMenu: function() {
        this.contextMenu = Ext.create('Ext.menu.Menu', {
            items: [
                {
                    itemId: 'name',
                    focusable: false,
                    text: '.'
                },
                '-',
                {
                    itemId: 'deleteBtn',
                    text: this.deleteText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    handler: function (item) {
                        Ext.MessageBox.confirm('Confirm', this.confirmDeleteText, function (btn, text, x, item) {
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
                    iconCls: Phlexible.Icon.get('document-copy'),
                    handler: function (item) {
                        this.onDuplicate(item.parentMenu.record);
                    },
                    scope: this
                },
                '-',
                {
                    text: this.loadInTemplateTreeText,
                    iconCls: Phlexible.Icon.get('blue-folder-tree'),
                    handler: function (item) {
                        this.elementtypeTemplateSelect(item.parentMenu.elementtype);
                    },
                    scope: this
                }
            ]
        });
    },

    initMyListeners: function() {
        this.on({
            rowcontextmenu: function (grid, elementtype, tr, rowIndex, event) {
                event.stopEvent();

                this.contextMenu.elementtype = elementtype;

                this.contextMenu.getComponent('name').setText(elementtype.get('title'));

                var coords = event.getXY();
                this.contextMenu.showAt([coords[0], coords[1]]);
            },
            rowdblclick: this.onElementtypeDblClick,
            scope: this
        });
    },

    load: function (type, elementtypeId, version) {
        var cycle = this.getDockedComponent('types');
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
                elementtypeId: elementtypeId,
                version: version
            };
            cycle.setActiveItem(foundBtn);
        }
    },

    onLoad: function (store) {
        if (!this.selectedRecord) {
            if (this.store.getCount()) {
                this.selModel.selectFirstRow();
                var elementtype = this.selModel.getSelected();
                this.elementtypeSelect(elementtype);
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
    },

    onElementtypeDblClick: function (grid, elementtype) {
        this.elementtypeSelect(elementtype);
    },

    elementtypeSelect: function (elementtype) {
        this.fireEvent('elementtypeChange', elementtype);
    },

    elementtypeTemplateSelect: function (elementtype) {
        this.fireEvent('elementtypeTemplateChange', elementtype);
    },

    onDelete: function (elementtype) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_list_delete', {id: elementtype.id}),
            success: this.onDeleteSuccess,
            scope: this
        });
    },

    onDeleteSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            Phlexible.success(data.msg);
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
