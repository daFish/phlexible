/**
 * Columns window
 */
Ext.define('Phlexible.dashboard.view.ColumnsWindow', {
    extend: 'Ext.Window',

    title: '_columns',
    width: 180,
    height: 100,
    modal: true,
    layout: 'fit',

    closeText: '_close',
    saveText: '_save',

    initComponent: function() {
        this.initMyItems();
        this.initDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'form',
            bodyPadding: 5,
            border: false,
            labelWidth: 150,
            items: [{
                xtype: 'numberfield',
                name: 'columns',
                minValue: 1,
                maxValue: 6,
                anchor: '100%',
                allowDecimals: false,
                value: Phlexible.App.getConfig().get('dashboard.columns')
            }]
        }];
    },

    initDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.cancelText,
                handler: this.close,
                scope: this
            },{
                text: this.saveText,
                handler: this.submit,
                scope: this
            }]
        }];
    },

    submit: function() {
        var values = this.getComponent(0).getForm().getValues();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('dashboard_portlets_columns'),
            success: function() {
                Phlexible.App.getConfig().set('dashboard.columns', values.columns);

                this.close();
            },
            scope: this
        });
    }
});
