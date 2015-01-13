Ext.define('Phlexible.metasets.SelectConfigurationWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.metasets.Strings.configure_select,
    strings: Phlexible.metasets.Strings,
    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

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
            autoExpandColumn: 'value',
            deferEmptyText: false,
            emptyText: this.strings.use_add,
            stripeRows: true,
            store: Ext.create('Ext.data.Store', {
                fields: ['value'],
                data: values
            }),
            columns: [{
                id: 'value',
                header: this.strings.value,
                dataIndex: 'value',
                flex: 1,
                editor: 'textfield'
            }, {
                xtype: 'actioncolumn',
                header: this.strings.actions,
                width: 30,
                items: [
                    {
                        iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                        tooltip: this.strings.remove_value,
                        handler: this.deleteValue,
                        scope: this
                    }
                ]
            }],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [
                    {
                        text: this.strings.add_value,
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
                text: this.strings.cancel,
                handler: this.close,
                scope: this
            },{
                text: this.strings.store,
                handler: function() {
                    var options = [],
                        records = this.getComponent(0).getStore().getRange();
                    if (!records.length) {
                        Ext.MessageBox.alert(this.strings.failure, this.strings.add_at_least_one_value);
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