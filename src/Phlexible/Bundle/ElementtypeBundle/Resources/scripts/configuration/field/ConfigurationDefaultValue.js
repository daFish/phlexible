Ext.define('Phlexible.elementtype.configuration.field.ConfigurationDefaultValue', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.default-value',

    iconCls: Phlexible.Icon.get('ui-text-field-select'),
    autoHeight: true,
    defaultType: 'textfield',
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    defaultValueText: '_defaultValueText',

    initComponent: function () {
        this.items = [
            {
                name: 'default_value_textfield',
                fieldLabel: this.defaultValueText,
                hidden: true,
                disabled: true
            },
            {
                xtype: 'numberfield',
                name: 'default_value_numberfield',
                fieldLabel: this.defaultValueText,
                hidden: true,
                disabled: true
            },
            {
                xtype: 'textarea',
                name: 'default_value_textarea',
                fieldLabel: this.defaultValueText,
                hidden: true,
                disabled: true,
                height: 100
            },
            {
                xtype: 'datefield',
                name: 'default_value_datefield',
                fieldLabel: this.defaultValueText,
                hidden: true,
                disabled: true,
                format: 'Y-m-d'
            },
            {
                xtype: 'timefield',
                name: 'default_value_timefield',
                fieldLabel: this.defaultValueText,
                format: 'H:i:s',
                hidden: true,
                disabled: true
            },
            {
                xtype: 'checkbox',
                name: 'default_value_checkbox',
                fieldLabel: this.defaultValueText,
                boxLabel: 'checked',
                hidden: true,
                disabled: true
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        if (!fieldType.config.values) {
            this.getComponent(0).disable();
            this.hide();
            return;
        }

        this.getComponent(0).enable();
        this.show();

        // default_text
        if (fieldType.config.values.default_text || fieldType.config.values.default_select || fieldType.config.values.default_link) {
            this.getComponent(0).enable();
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).disable();
            this.getComponent(0).hide();
        }

        // default_number
        if (fieldType.config.values.default_number) {
            this.getComponent(1).enable();
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).disable();
            this.getComponent(1).hide();
        }

        // default_textarea
        if (fieldType.config.values.default_textarea || fieldType.config.values.default_editor) {
            this.getComponent(2).enable();
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).disable();
            this.getComponent(2).hide();
        }

        // default_date
        if (fieldType.config.values.default_date) {
            this.getComponent(3).enable();
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).disable();
            this.getComponent(3).hide();
        }

        // default_time
        if (fieldType.config.values.default_time) {
            this.getComponent(4).enable();
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).disable();
            this.getComponent(4).hide();
        }

        // default_checkbox
        if (fieldType.config.values.default_checkbox) {
            this.getComponent(5).enable();
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).disable();
            this.getComponent(5).hide();
        }
    },

    loadConfiguration: function (configuration, fieldType) {
        this.defaultValueField = null;

        this.items.each(function (item) {
            if (!item.isFormField) {
                return;
            }

            var name = item.getName();
            if (name == fieldType.defaultValueField) {
                item.setValue(configuration.default_value);
                this.defaultValueField = configuration.defaultValueField;
            }
            else {
                item.setValue('');
            }
        });

        this.isValid();
    },

    getSaveValues: function () {
        if (!this.defaultField) {
            return {};
        }

        var defaultValue = '';

        this.items.each(function(item) {
            if (item.name === this.defaultValueField) {
                defaultValue = item.getValue();
                return false;
            }
        }, this);

        return {
            default_value: defaultValue
        };
    },

    isValid: function () {
        return true;
    }
});
