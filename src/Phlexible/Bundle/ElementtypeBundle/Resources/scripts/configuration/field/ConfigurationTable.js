Ext.define('Phlexible.elementtype.configuration.field.ConfigurationTable', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.table',

    iconCls: Phlexible.Icon.get('table'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    rowsText: '_rowsText',
    colsText: '_colsText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'numberfield',
                name: 'table_rows',
                fieldLabel: this.rowsText,
                minValue: 0,
                maxValue: 10
            },
            {
                xtype: 'numberfield',
                name: 'table_cols',
                fieldLabel: this.colsText,
                minValue: 0,
                maxValue: 10
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        var isTable = fieldType.type === 'table';
        this.getComponent(0).setDisabled(!isTable);
        this.getComponent(1).setDisabled(!isTable);
        this.setVisible(isTable);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).setValue(configuration.table_cols);
        this.getComponent(1).setValue(configuration.table_rows);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            table_cols: this.getComponent(0).getValue(),
            table_rows: this.getComponent(1).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid();


    }
});
