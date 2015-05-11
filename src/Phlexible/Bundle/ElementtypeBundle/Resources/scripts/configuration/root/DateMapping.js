Ext.define('Phlexible.elementtype.configuration.root.DateMapping', {
    extend: 'Ext.grid.Panel',
    xtype: 'elementtype.configuration.root.date-mapping',

    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',

    actionsText: '_actionsText',
    removeText: '_removeText',
    dsIdText: '_dsIdText',
    fieldText: '_fieldText',
    clearText: '_clearText',

    initComponent: function () {
        this.viewConfig = {
            forceFit: true,
            emptyText: this.navigation_default_dateText,
            deferEmptyText: false
        };

        this.store = Ext.create('Ext.data.Store', {
            fields: ['dsId', 'title', 'type'],
            listeners: {
                datachanged: function (store) {
                    var fields = [];
                    Ext.each(store.getRange(), function (r) {
                        fields.push({dsId: r.get('dsId'), title: r.get('title'), type: r.get('type')});
                    });
                    this.fireEvent('change', fields);
                },
                scope: this
            }
        });

        this.columns = [
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
                return false; // TODO: fix
                var v = this.view;
                this.dropZone = new Ext.dd.DropZone(this.view.mainBody, {
                    ddGroup: 'elementtypesDD'
                });

                this.dropZone.getTargetFromEvent = function (e) {
                    return this.el.dom;
                };

                this.dropZone.onNodeDrop = function (node, dd, e, dragData) {
                    if (!dragData.node.attributes.properties.field) {
                        //Phlexible.console.log('NO FIELD INFO FOUND');
                        return;
                    }

                    var fieldType = dragData.node.attributes.properties.field.type;
                    var found = this.store.find('id', dragData.node.attributes.id) != -1;

                    if (found) {
                        //Phlexible.console.log('ALREADY PRESENT');
                        return;
                    }

                    //Phlexible.console.log(dragData.node);
                    //Phlexible.console.log(fieldType);
                    switch (fieldType) {
                        case 'date':
                        case 'time':
                            break;

                        default:
                            //Phlexible.console.log('INVALID TYPE: ' + fieldType);
                            return;
                    }

                    var datePresent = false;
                    this.store.each(function (r) {
                        if (r.data.type == fieldType) {
                            //Phlexible.console.log('REMOVE');
                            this.store.remove(r);
                        }
                        if (r.data.type == 'date') {
                            datePresent = true;
                        }
                        if (!r.data.type) {
                            datePresent = true;
                            r.set('type', 'date');
                        }
                    }, this);

                    if (fieldType == 'time' && !datePresent) {
                        //Phlexible.console.log('TIME INVALID WITHOUT DATE');
                        return;
                    }

                    var fieldTitle = dragData.node.attributes.properties.labels.fieldLabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + dragData.node.attributes.properties.field.working_title + ')';
                    var r = new Ext.data.Record({
                        dsId: dragData.node.attributes.ds_id,
                        title: fieldTitle,
                        type: fieldType
                    });

                    //Phlexible.console.log('ADD');
                    this.store.add(r);
                    this.fireChange();
                }.createDelegate(this);

                this.dropZone.onNodeOver = function (node, dd, e, dragData) {
                    if (dragData.node.attributes.properties.field && this.store.find('id', dragData.node.attributes.id) == -1) {
                        switch (dragData.node.attributes.properties.field.type) {
                            case 'date':
                            case 'time':
                                return "x-dd-drop-ok";
                                break;
                        }
                    }

                    return "x-dd-drop-nodrop";
                }.createDelegate(this);
            },
            scope: this
        });

        this.callParent(arguments);
    },

    fireChange: function() {
        var fields = [];
        Ext.each(this.store.getRange(), function (r) {
            fields.push({dsId: r.get('dsId'), title: r.get('title'), type: r.get('type')});
        });
        this.fireEvent('change', fields);
    }
});
