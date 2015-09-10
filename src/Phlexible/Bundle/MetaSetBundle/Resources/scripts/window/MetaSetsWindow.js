Ext.define('Phlexible.metaset.window.MetaSetsWindow', {
    extend: 'Ext.window.Window',

    iconCls: Phlexible.Icon.get('weather-clouds'),
    width: 400,
    height: 300,
    layout: 'fit',

    baseParams: {},
    urls: {},

    metasetText: '_metasetText',
    actionsText: '_actionsText',
    removeText: '_removeText',
    selectText: '_selectText',
    addText: '_addText',
    cancelText: '_cancelText',
    saveText: '_saveText',
    noSelectedMetasetsText: '_noSelectedMetasetsText',

    initComponent: function () {
        if (!this.metasetModel) {
            throw 'metasetModel config missing';
        }
        if (!this.metasetUrl) {
            throw 'metasetUrl config missing';
        }

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);

        this.getComponent(0).getStore().getProxy().setUrl(this.metasetUrl);
        this.getComponent(0).getStore().load();
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'grid',
                border: false,
                emptyText: this.noSelectedMetasetsText,
                store: {
                    model: this.metasetModel,
                    autoLoad: false
                },
                columns: [
                    {
                        header: this.metasetText,
                        dataIndex: 'name',
                        flex: 1
                    },
                    {
                        xtype: 'actioncolumn',
                        header: this.actionsText,
                        width: 40,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.removeText,
                                handler: this.removeMetaSet,
                                scope: this
                            }
                        ]
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
                xtype: 'combo',
                itemId: 'addField',
                flex: 1,
                store: {
                    model: 'Phlexible.metaset.model.MetaSet'
                },
                emptyText: this.selectText,
                valueField: 'id',
                displayField: 'name',
                mode: 'remote',
                triggerAction: 'all',
                editable: false
            },
            {
                text: this.addText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.addMetaSet,
                scope: this
            }]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                xtype: 'component',
                flex: 1
            },{
                text: this.cancelText,
                handler: this.close,
                scope: this
            },{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.save,
                scope: this
            }]
        }];
    },

    addMetaSet: function () {
        var combo = this.getDockedComponent('tbar').getComponent('addField'),
            id = combo.getValue(),
            name = combo.getRawValue();

        if (!id || !id.length) {
            return;
        }

        var metaset = Ext.create(this.metasetModel, {name: name});
        this.getComponent(0).getStore().add(metaset);
        metaset.set('id', id);

        /*
        var idx = combo.getStore().find('id', id);
        if (idx !== -1) {
            combo.store.removeAt(idx);
        }
        */
        combo.setValue(null);
    },

    removeMetaSet: function (grid, record) {
        grid.getStore().remove(record);

        /*
        var combo = this.getDockedComponent('tbar').getComponent('addField');
        combo.getStore().add(Ext.create('Ext.data.Model', {id: record.get('id'), name: record.get('name')}));
        */
    },

    save: function() {
        this.fireEvent('save', this.getComponent(0).getStore().getRange());
    }
});
