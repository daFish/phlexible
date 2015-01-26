Ext.define('Phlexible.elementtype.ElementtypeViability', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.elementtype-viability',

    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
    border: true,
    autoScroll: true,
    viewConfig: {
        forceFit: true
    },

    emptyText: '_emptyText',
    idText: '_idText',
    elementtypeText: '_elementtypeText',
    actionsText: '_actionsText',
    saveText: '_saveText',
    addElementtypeText: '_addElementtypeText',
    removeText: '_removeText',

    initComponent: function () {
        this.initMyStore();
        this.initMyColumns();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.elementtype.model.Elementtype',
            proxy: {
                type: 'ajax',
                url: '',//Phlexible.Router.generate('elementtypes_list_viability'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'viability',
                    idProperty: 'id'
                }
            },
            sorters: [{
                property: 'title',
                direction: 'ASC'
            }]
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.idText,
                dataIndex: 'id',
                width: 30,
                hidden: true,
                sortable: true
            },
            {
                id: 'title',
                header: this.elementtypeText,
                dataIndex: 'title',
                flex: 1,
                sortable: true,
                renderer: function (value, meta, r) {
                    return '<img src="' + Phlexible.bundleAsset('/phlexibleelementtype/elementtypes/' + r.get('icon')) + '" width="18" height="18" style="vertical-align: middle;" /> ' + value;
                }
            },
            {
                xtype: 'actioncolumn',
                header: this.actionsText,
                width: 40,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.removeText,
                        handler: function (grid, record, action, row, col) {
                            var r = grid.store.getAt(row);

                            this.store.remove(r);
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
            itemId: 'tbar',
            dock: 'top',
            items: [{
                itemId: 'saveBtn',
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: function () {
                    var records = this.store.getRange(),
                        ids = [];
                    Ext.each(records, function (r) {
                        ids.push(r.get('id'));
                    });
                    Ext.Ajax.request({
                        url: Phlexible.Router.generate('elementtypes_viability_save', {id: this.elementtypeId}),
                        params: {
                            "ids[]": ids
                        },
                        success: function () {

                        },
                        scope: this
                    });
                },
                scope: this
            },
            '-',
            {
                xtype: 'combo',
                itemId: 'addBtn',
                emptyText: this.addElementtypeText,
                triggerAction: 'all',
                listClass: 'x-combo-list-big',
                editable: false,
                tpl: '<tpl for="."><div class="x-combo-list-item"><img src="{[Phlexible.bundleAsset(\"/phlexibleelementtype/elementtypes/\" + values.icon)]}" width="18" height="18" style="vertical-align: middle;" /> {title}</div></tpl>',
                width: 250,
                listWidth: 250,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.elementtype.model.Elementtype',
                    proxy: {
                        type: 'ajax',
                        url: '',//Phlexible.Router.generate('elementtypes_list_for_type'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            rootProperty: 'elementtypes',
                            idProperty: 'id'
                        }
                    },
                    sorters: [{property: 'title', direction: 'ASC'}],
                    listeners: {
                        load: function (comboStore) {
                            var r = this.store.getRange();

                            for (var i = 0; i < r.length; i++) {
                                if (comboStore.indexOfId(r[i].id) !== -1) {
                                    comboStore.remove(comboStore.getById(r[i].id));
                                }
                            }
                        },
                        scope: this
                    }
                }),
                displayField: 'title',
                valueField: 'id',
                listeners: {
                    select: this.onAdd,
                    scope: this
                }
            }]
        }];
    },

    getCombo: function () {
        return this.getDockedItem('tbar').getComponent('addBtn');
    },

    onAdd: function (combo, record, index) {
        if (!this.store.getById(record.id)) {
            this.store.add(new Phlexible.elementtype.model.Elementtype({
                id: record.id,
                title: record.data.title,
                icon: record.data.icon
            }, record.id));
            this.store.sort('title', 'ASC');
            combo.store.remove(record);
        }

        combo.setValue('');
    },

    empty: function () {
        this.getCombo().lastQuery = null;
        this.store.removeAll();
    },

    load: function (id, title, version, type) {
        this.elementtypeId = id;
        this.getCombo().lastQuery = null;
        this.store.proxy.conn.url = Phlexible.Router.generate('elementtypes_viability_list', {id: id});
        this.store.reload();
        this.setType(type);
    },

    setType: function (type) {
        this.getCombo().store.proxy.conn.url = Phlexible.Router.generate('elementtypes_viability_for_type', {type: type});
        if (this.getCombo().lastQuery) {
            this.getCombo().getStore().reload();
        }
    }
});
