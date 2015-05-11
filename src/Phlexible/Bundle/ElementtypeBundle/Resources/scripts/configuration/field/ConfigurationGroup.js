Ext.define('Phlexible.elementtype.configuration.field.ConfigurationGroup', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.group',

    iconCls: Phlexible.Icon.get('ui-group-box'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    minRepeatText: '_minRepeatText',
    maxRepeatText: '_maxRepeatText',
    defaultRepeatText: '_defaultRepeatText',
    showBorderText: '_showBorderText',
    singleRowText: '_singleRowText',
    labelWidthText: '_labelWidthText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'numberfield',
                name: 'repeat_min',
                fieldLabel: this.minRepeatText,
                minValue: 0
            },
            {
                xtype: 'numberfield',
                name: 'repeat_max',
                fieldLabel: this.maxRepeatText,
                minValue: 0
            },
            {
                xtype: 'numberfield',
                name: 'repeat_default',
                fieldLabel: this.defaultRepeatText,
                minValue: 0
            },
            {
                xtype: 'checkbox',
                name: 'group_show_border',
                hideEmptyLabel: false,
                boxLabel: this.showBorderText
            },
            {
                xtype: 'checkbox',
                name: 'group_single_line',
                hideEmptyLabel: false,
                boxLabel: this.singleRowText
            },
            {
                xtype: 'numberfield',
                name: 'label_width',
                fieldLabel: this.labelWidthText
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        var isGroup = fieldType.type === 'group';
        this.getComponent(0).setDisabled(!isGroup);
        this.getComponent(1).setDisabled(!isGroup);
        this.getComponent(2).setDisabled(!isGroup);
        this.getComponent(3).setDisabled(!isGroup);
        this.getComponent(4).setDisabled(!isGroup);
        this.getComponent(5).setDisabled(!isGroup);
        this.setVisible(isGroup);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).setValue(configuration.repeat_min);
        this.getComponent(1).setValue(configuration.repeat_max);
        this.getComponent(2).setValue(configuration.repeat_default);
        this.getComponent(3).setValue(configuration.group_show_border);
        this.getComponent(4).setValue(configuration.group_single_line);
        this.getComponent(5).setValue(configuration.label_width);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            repeat_min: this.getComponent(0).getValue() || '',
            repeat_max: this.getComponent(1).getValue() || '',
            repeat_default: this.getComponent(2).getValue() || '',
            group_show_border: this.getComponent(3).getValue(),
            group_single_line: this.getComponent(4).getValue(),
            label_width: this.getComponent(5).getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid() &&
            this.getComponent(3).isValid() &&
            this.getComponent(4).isValid() &&
            this.getComponent(5).isValid();
    }
});
