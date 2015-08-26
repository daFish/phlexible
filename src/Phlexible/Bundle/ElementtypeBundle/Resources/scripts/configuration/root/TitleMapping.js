Ext.define('Phlexible.elementtype.configuration.root.TitleMapping', {
    extend: 'Ext.grid.Panel',
    xtype: 'elementtype.configuration.root.title-mapping',

    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',

    allowedTypes: [],

    actionsText: '_actionsText',
    removeText: '_removeText',
    indexText: '_indexText',
    dsIdText: '_dsIdText',
    fieldText: '_fieldText',
    clearText: '_clearText',

    initComponent: function () {
        this.allowedTypes = this.allowedTypes || [];

        this.viewConfig = {
            forceFit: true,
            emptyText: this.navigation_default_titleText,
            deferEmptyText: false
        };

        this.store = Ext.create('Ext.data.Store', {
            fields: ['dsId', 'title', 'index']
        });

        this.columns = [
            {
                header: this.indexText,
                dataIndex: 'index',
                width: 50,
                renderer: function (v) {
                    return '$' + v;
                }
            },
            {
                header: this.dsIdText,
                dataIndex: 'dsId',
                width: 200,
                hidden: true
            },
            {
                header: this.fieldText,
                dataIndex: 'title',
                width: 200
            },
            {
                xtype: 'actioncolumn',
                header: this.actionsText,
                width: 150,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.removeText,
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            this.getStore().remove(record);
                            this.fireChange();
                        },
                        scope: this
                    }
                ]
            }
        ];

        this.tbar = [
            {
                text: this.clearText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: function () {
                    this.getStore().removeAll();
                    this.fireChange();
                },
                scope: this
            }
        ];

        this.on({
            render: function (grid) {
                return; // TODO: fix
                var v = this.view;
                this.dropZone = new Ext.dd.DropZone(this.view.mainBody, {
                    ddGroup: 'elementtypesDD'
                });

                this.dropZone.getTargetFromEvent = function (e) {
                    return this.el.dom;
                };

                this.dropZone.onNodeDrop = function (node, dd, e, dragData) {
                    if (
                        !dragData.node.attributes.properties.field ||
                        this.store.find('id', dragData.node.attributes.id) !== -1 ||
                        this.allowedTypes.indexOf(dragData.node.attributes.properties.field.type) === -1
                    ) {
                        return;
                    }

                    var fieldTitle = dragData.node.attributes.properties.field.working_title;

                    var index = 0;
                    Ext.each(this.store.getRange(), function (r) {
                        r.set('index', ++index);
                        r.commit();
                    });

                    var r = new Ext.data.Record({
                        dsId: dragData.node.attributes.ds_id,
                        title: fieldTitle,
                        index: ++index
                    });

                    this.store.add(r);
                    this.fireChange();
                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (
                        dragData.node.attributes.properties.field &&
                        this.store.find('id', dragData.node.attributes.id) == -1 &&
                        this.allowedTypes.indexOf(dragData.node.attributes.properties.field.type) !== -1
                    ) {
                        return "x-dd-drop-ok";
                    }

                    return "x-dd-drop-nodrop";
                }.createDelegate(this);

                this.ddrow = new Ext.ux.dd.GridReorderDropTarget(grid, {
                    copy: false
                });
            },
            scope: this
        });

        this.callParent(arguments);
    },

    fireChange: function() {
        var fields = [];
        Ext.each(this.store.getRange(), function (r) {
            fields.push({dsId: r.get('dsId'), title: r.get('title'), index: r.get('index')});
        });
        this.fireEvent('change', fields);
    }
});
