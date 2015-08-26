Ext.define('Phlexible.elementtype.configuration.field.Validations', {
    extend: 'Ext.form.Panel',
    xtype: 'elementtype.configuration.field.validations',

    iconCls: Phlexible.Icon.get('spell-check'),
    border: false,
    autoScroll: true,
    defaultType: 'textfield',
    labelWidth: 120,
    padding: 5,
    defaults: {
        anchor: '100%'
    },

    textValidationText: '_textValidationText',
    minLengthText: '_minLengthText',
    maxLengthText: '_maxLengthText',
    regularExpressionText: '_regularExpressionText',
    modifiersText: '_modifiersText',
    globalText: '_globalText',
    ignoreCaseText: '_ignoreCaseText',
    multilineText: '_multilineText',
    contentValidationText: '_contentValidationText',
    validatorText: '_validatorText',
    numberValidationText: '_numberValidationText',
    valueText: '_valueText',
    allowNegativeText: '_allowNegativeText',
    allowDecimalsText: '_allowDecimalsText',
    minValueText: '_minValueText',
    maxValueText: '_maxValueText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'fieldset',
                title: this.textValidationText,
                autoHeight: true,
                maskDisabled: false,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'numberfield',
                        name: 'min_length',
                        fieldLabel: this.minLengthText,
                        minValue: 0
                    },
                    {
                        xtype: 'numberfield',
                        name: 'max_length',
                        fieldLabel: this.maxLengthText,
                        minValue: 0
                    },
                    {
                        xtype: 'textfield',
                        name: 'regexp',
                        fieldLabel: this.regularExpressionText
                    },
                    {
                        xtype: 'checkboxgroup',
                        name: 'modifiers',
                        fieldLabel: this.modifiersText,
                        columns: 1,
                        items: [
                            {
                                name: 'global',
                                hideEmptyLabel: false,
                                boxLabel: this.globalText,
                                hidden: true
                            },
                            {
                                name: 'ignore',
                                hideEmptyLabel: false,
                                boxLabel: this.ignoreCaseText
                            },
                            {
                                name: 'multiline',
                                hideEmptyLabel: false,
                                boxLabel: this.multilineText
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.contentValidationText,
                autoHeight: true,
                maskDisabled: false,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'combo',
                        name: 'validator',
                        fieldLabel: this.validatorText,
                        store: Ext.create('Ext.data.Store', {
                            fields: ['key', 'value'],
                            data: [
                                ['', 'No validator'],
                                ['alpha', 'Alpha'],
                                ['alphanum', 'Alphanumeric'],
                                ['email', 'Email'],
                                ['url', 'Url']
                            ]
                        }),
                        editable: false,
                        mode: 'local',
                        displayField: 'value',
                        valueField: 'key',
                        triggerAction: 'all',
                        typeAhead: false,
                        value: ''
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.numberValidationText,
                autoHeight: true,
//            disabled: true,
                maskDisabled: false,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'checkbox',
                        name: 'allow_negative',
                        fieldLabel: this.valueText,
                        boxLabel: this.allowNegativeText
                    },
                    {
                        xtype: 'checkbox',
                        name: 'allow_decimals',
                        hideEmptyLabel: false,
                        boxLabel: this.allowDecimalsText,
                    },
                    {
                        xtype: 'numberfield',
                        name: 'min_value',
                        fieldLabel: this.minValueText,
                        minValue: 0
                    },
                    {
                        xtype: 'numberfield',
                        name: 'max_value',
                        fieldLabel: this.maxValueText,
                        minValue: 0
                    }
                ]
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (fieldType) {
        // text
        if (fieldType.config.validation.text) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // content
        if (fieldType.config.validation.content) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }

        // numeric
        if (fieldType.config.validation.numeric) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }
    },

    loadValidation: function (validation, fieldType) {
        this.updateVisibility(fieldType);

        var text = this.getComponent(0);
        var content = this.getComponent(1);
        var number = this.getComponent(2);

        text.getComponent(0).setValue(validation.min_length);
        text.getComponent(1).setValue(validation.max_length);
        text.getComponent(2).setValue(validation.regexp);
        text.getComponent(3).items.items[0].setValue(validation.global);
        text.getComponent(3).items.items[1].setValue(validation.ignore);
        text.getComponent(3).items.items[2].setValue(validation.multiline);
        content.getComponent(0).setValue(validation.validator || '');
        number.getComponent(0).setValue(validation.allow_negative);
        number.getComponent(1).setValue(validation.allow_decimals);
        number.getComponent(2).setValue(validation.min_value);
        number.getComponent(3).setValue(validation.max_value);
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        return {
            min_length: values.min_length,
            max_length: values.max_length,
            regexp: values.regexp,
            global: values.global,
            ignore: values.ignore,
            multiline: values.multiline,
            validator: values.validator,
            allow_negative: values.allow_negative,
            allow_decimals: values.allow_decimals,
            min_value: values.min_value,
            max_value: values.max_value
        };
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconCls(Phlexible.Icon.get('spell-check'));

            return true;
        } else {
            this.setIconCls(Phlexible.Icon.get('exclamation-red'));

            return false;
        }
    },

    isActive: function() {
        return !!this.active;
    },

    loadNode: function (node, fieldType) {
        if (fieldType.config.validation) {
            this.active = true;
            this.tab.show();
            this.enable();
            this.loadValidation(node.get('validation'), fieldType);
        }
        else {
            this.active = false;
            this.tab.hide();
            this.disable();
        }
    }
});
