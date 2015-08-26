Ext.define('Phlexible.elementtype.configuration.field.Labels', {
    extend: 'Ext.form.Panel',
    xtype: 'elementtype.configuration.field.labels',

    iconCls: Phlexible.Icon.get('ui-label'),
    border: false,
    autoScroll: true,
    defaultType: 'textfield',
    labelWidth: 120,
    padding: 5,

    fieldLabelText: '_fieldLabelText',
    germanText: '_germanText',
    englishText: '_englishText',
    boxLabelText: '_boxLabelText',
    prefixText: '_prefixText',
    suffixText: '_suffixText',
    contextHelpText: '_contextHelpText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'fieldset',
                title: this.fieldLabelText,
                autoHeight: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'fieldlabel_de',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText,
                        allowBlank: false
                    },
                    {
                        xtype: 'textfield',
                        name: 'fieldlabel_en',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText,
                        allowBlank: false
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.boxLabelText,
                autoHeight: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'boxlabel_de',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText
                    },
                    {
                        xtype: 'textfield',
                        name: 'boxlabel_en',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.prefixText,
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'prefix_de',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText
                    },
                    {
                        xtype: 'textfield',
                        name: 'prefix_en',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.suffixText,
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'suffix_de',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText
                    },
                    {
                        xtype: 'textfield',
                        name: 'suffix_en',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: this.contextHelpText,
                autoHeight: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textarea',
                        name: 'context_help_de',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-de') + ' ' + this.germanText
                    },
                    {
                        xtype: 'textarea',
                        name: 'context_help_en',
                        fieldLabel: Phlexible.Icon.inlineDirect('p-icon-flag-en') + ' ' + this.englishText
                    }
                ]
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (fieldType) {
        // field
        if (fieldType.config.labels.field) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // prefix
        if (fieldType.config.labels.box) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }
        // prefix
        if (fieldType.config.labels.prefix) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }

        // suffix
        if (fieldType.config.labels.suffix) {
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).hide();
        }

        // context
        if (fieldType.config.labels.help) {
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).hide();
        }
    },

    loadLabels: function (labels, fieldType) {
        this.updateVisibility(fieldType);

        labels = Ext.merge(labels, {
            fieldLabel : {},
            boxLabel : {},
            prefix : {},
            suffix : {},
            contextHelp : {}
        });

        this.getForm().setValues([
            {id: 'fieldlabel_de', value: labels.fieldLabel.de},
            {id: 'fieldlabel_en', value: labels.fieldLabel.en},
            {id: 'boxlabel_de', value: labels.boxLabel.de},
            {id: 'boxlabel_en', value: labels.boxLabel.en},
            {id: 'prefix_de', value: labels.prefix.de},
            {id: 'prefix_en', value: labels.prefix.en},
            {id: 'suffix_de', value: labels.suffix.de},
            {id: 'suffix_en', value: labels.suffix.en},
            {id: 'context_help_de', value: labels.contextHelp.de},
            {id: 'context_help_en', value: labels.contextHelp.en}
        ]);

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues();

        return {
            fieldLabel: {
                de: values.fieldlabel_de,
                en: values.fieldlabel_en
            },
            boxLabel: {
                de: values.boxlabel_de,
                en: values.boxlabel_en
            },
            prefix: {
                de: values.prefix_de,
                en: values.prefix_en
            },
            suffix: {
                de: values.suffix_de,
                en: values.suffix_en
            },
            contextHelp: {
                de: values.context_help_de,
                en: values.context_help_en
            }
        };
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconCls(Phlexible.Icon.get('ui-label'));

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
        if (fieldType.config.labels) {
            this.active = true;
            this.tab.show();
            this.enable();
            this.loadLabels(node.get('labels'), fieldType);
        }
        else {
            this.active = false;
            this.tab.hide();
            this.disable();
        }
    }
});
