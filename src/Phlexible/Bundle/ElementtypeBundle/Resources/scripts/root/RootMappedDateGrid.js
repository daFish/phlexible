Phlexible.elementtypes.RootMappedDateGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    strings: Phlexible.elementtypes.Strings,
    border: true,
    autoScroll: true,
    enableDragDrop: true,
    ddGroup: 'elementtypesDD',
    viewConfig: {
        forceFit: true,
        emptyText: Phlexible.elementtypes.Strings.navigation_default_date,
        deferEmptyText: false
    },
    autoExpandColumn: 'field',

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            fields: ['ds_id', 'field', 'type'],
            listeners: {
                datachanged: function(store) {
                    var fields = [];
                    Ext.each(store.getRange(), function(r) {
                        fields.push({ds_id: r.get('ds_id'), field: r.get('field'), type: r.get('type')});
                    });
                    this.fireEvent('change', fields);
                },
                scope: this
            }
        });

        this.columns = [{
            header: this.strings.ds_id,
            dataIndex: 'ds_id',
            width: 200,
            hidden: true
        },{
            id: 'field',
            header: this.strings.field,
            dataIndex: 'field',
            width: 200
        }];

        this.sm = new Ext.grid.RowSelectionModel({
            singleSelect : true
        });

        this.tbar = [{
            text: this.strings.clear,
            iconCls: 'p-elementtype-clear-icon',
            handler: function() {
                this.store.removeAll();
            },
            scope: this
        }];

        this.on({
            render: function(grid){
                this.addEvents("beforetooltipshow");

                var v = this.view;
                this.dropZone = new Ext.dd.DropZone(this.view.mainBody, {
                    ddGroup: 'elementtypesDD'
                });

                this.dropZone.getTargetFromEvent = function(e) {
                    return this.el.dom;
                };

                this.dropZone.onNodeDrop = function(node, dd, e, dragData) {
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
                    this.store.each(function(r) {
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

                    var fieldTitle = dragData.node.attributes.properties.labels.fieldlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] + ' (' + dragData.node.attributes.properties.field.working_title + ')';
                    var r = new Ext.data.Record({
                        ds_id: dragData.node.attributes.ds_id,
                        field: fieldTitle,
                        type: fieldType
                    });

                    //Phlexible.console.log('ADD');
                    this.store.add(r);

                    //this.layout();

                }.createDelegate(this);

                this.dropZone.onNodeOver = function(node, dd, e, dragData) {
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

        Phlexible.elementtypes.RootMappedDateGrid.superclass.initComponent.call(this);
    },

    loadData: function(date) {
        this.store.loadData(date);
    },

    getSaveValues: function() {
        var date = [];

        for (var i=0; i<this.store.getCount(); i++) {
            var r = this.store.getAt(i);
            navigation.push([r.get('id'), r.get('field'), r.get('type')]);
        }

        this.store.commitChanges();

        return date;
    }
});

Ext.reg('elementtypes-root-mapped-date', Phlexible.elementtypes.RootMappedDateGrid);