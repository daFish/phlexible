Ext.define('Phlexible.elementtype.configuration.field.ConfigurationLabel', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.label',

    iconCls: Phlexible.Icon.get('ui-label'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    germanText: '_germanText',
    englishText: '_englishText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'textarea',
                name: 'text_de',
                fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText
            },
            {
                xtype: 'textarea',
                name: 'text_en',
                fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        var isLabel = (fieldType.type === 'label' || fieldType.type === 'displayfield');
        this.getComponent(0).setDisabled(!isLabel);
        this.getComponent(1).setDisabled(!isLabel);
        this.setVisible(isLabel);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).setValue(configuration.text_de);
        this.getComponent(1).setValue(configuration.text_en);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            text_de: this.getComponent(0).getValue() || '',
            text_en: this.getComponent(1).getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid();
    }
});
