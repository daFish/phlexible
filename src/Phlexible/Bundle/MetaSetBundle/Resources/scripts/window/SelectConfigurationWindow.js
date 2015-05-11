Ext.define('Phlexible.metaset.window.SelectConfigurationWindow', {
    extend: 'Ext.window.Window',

    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

    useAddText: '_useAddText',
    valueText: '_valueText',
    actionsText: '_actionsText',
    removeValueText: '_removeValueText',
    addValueText: '_addValueText',
    cancelText: '_cancelText',
    storeText: '_storeText',
    addAtLeastOneValueText: '_addAtLeastOneValueText',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var values = [];
        if (this.options) {
            Ext.each(this.options.split(','), function (value) {
                values.push([value]);
            });
        }

        this.items = [{
            xtype: 'grid',
            border: false,
            viewConfig: {
                deferEmptyText: false
            },
            emptyText: this.useAddText,
            stripeRows: true,
            store: Ext.create('Ext.data.Store', {
                fields: ['value'],
                data: values
            }),
            columns: [{
                id: 'value',
                header: this.valueText,
                dataIndex: 'value',
                flex: 1,
                editor: {xtype: 'textfield'}
            }, {
                xtype: 'actioncolumn',
                header: this.actionsText,
                width: 30,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.removeValueText,
                        handler: this.deleteValue,
                        scope: this
                    }
                ]
            }],
            plugins: {
                ptype: 'cellediting',
                clicksToEdit: 1
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        text: this.addValueText,
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                        handler: this.addValue,
                        scope: this
                    }
                ]
            }]
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                '->',
            {
                text: this.cancelText,
                handler: this.close,
                scope: this
            },{
                text: this.storeText,
                handler: function() {
                    var options = [],
                        records = this.getComponent(0).getStore().getRange();
                    if (!records.length) {
                        Phlexible.Notify.failure(this.addAtLeastOneValueText);
                        return;
                    }
                    Ext.each(records, function(r) {
                        options.push(r.get('value'));
                    });
                    this.fireEvent('store', options.join(','));
                    this.close();
                },
                scope: this
            }]
        }];
    },

    addValue: function() {
        this.getComponent(0).getStore().add(new Ext.data.Record({value: Ext.id(null, 'value-')}));
    },

    deleteValue: function(grid, rowIndex, colIndex, item, e, record) {
        grid.getStore().remove(record);
    }
});