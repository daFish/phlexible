Ext.define('Phlexible.elementtype.configuration.field.ConfigurationAccordion', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.accordion',

    iconCls: Phlexible.Icon.get('ui-accordion'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    defaultCollapsedText: '_defaultCollapsedText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkbox',
                name: 'default_collapsed',
                hideEmptyLabel: false,
                boxLabel: this.defaultCollapsedText
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        var isAccordion = fieldType.type === 'accordion';
        this.getComponent(0).setDisabled(!isAccordion);
        this.setVisible(isAccordion);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).setValue(configuration.default_collapsed);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            default_collapsed: this.getComponent(0).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid();
    }
});
