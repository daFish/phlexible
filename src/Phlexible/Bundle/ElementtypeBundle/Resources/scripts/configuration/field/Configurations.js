Ext.define('Phlexible.elementtype.configuration.field.Configurations', {
    extend: 'Ext.form.Panel',
    requires: [
        'Phlexible.elementtype.configuration.field.ConfigurationAccordion',
        'Phlexible.elementtype.configuration.field.ConfigurationDefaultValue',
        'Phlexible.elementtype.configuration.field.ConfigurationGroup',
        'Phlexible.elementtype.configuration.field.ConfigurationLabel',
        'Phlexible.elementtype.configuration.field.ConfigurationLink',
        'Phlexible.elementtype.configuration.field.ConfigurationSelect',
        'Phlexible.elementtype.configuration.field.ConfigurationTable'
    ],
    xtype: 'elementtype.configuration.field.configurations',

    iconCls: Phlexible.Icon.get('wrench'),
    border: false,
    autoScroll: true,
    defaultType: 'textfield',
    labelWidth: 150,
    padding: 5,
    defaults: {
        anchor: '100%'
    },

    requiredText: '_requiredText',
    notRequiredText: '_notRequiredText',
    onPublishText: '_onPublishText',
    alwaysText: '_alwaysText',
    languageText: '_languageText',
    notSynchronizedText: '_notSynchronizedText',
    synchronizedText: '_synchronizedText',
    synchronizedWithUnlinkText: '_synchronizedWithUnlinkText',
    widthText: '_widthText',
    heightText: '_heightText',
    readonlyText: '_readonlyText',
    hideLabelText: '_hideLabelText',
    childItemsText: '_childItemsText',
    sortableText: '_sortableText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function () {
        this.items = [
            {
                // 0
                xtype: 'combo',
                name: 'required',
                fieldLabel: this.requiredText,
                flex: 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'value'],
                    data: [
                        ['no', this.notRequiredText],
                        ['on_publish', this.onPublishText],
                        ['always', this.alwaysText]
                    ]
                }),
                displayField: 'value',
                valueField: 'key',
                editable: false,
                mode: 'local',
                triggerAction: 'all',
                typeAhead: false,
                value: 'no'
            },{
                // 1
                xtype: 'combo',
                name: 'synchronized',
                fieldLabel: this.languageText,
                flex: 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'title'],
                    data: [
                        ['no', this.notSynchronizedText],
                        ['synchronized', this.synchronizedText],
                        ['synchronized_unlink', this.synchronizedWithUnlinkText]
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all'
            },
            {
                // 2
                xtype: 'numberfield',
                name: 'width',
                fieldLabel: this.widthText,
                flex: 1,
                allowDecimals: false,
                allowNegative: false,
                minValue: 20
            },
            {
                // 3
                xtype: 'numberfield',
                name: 'height',
                fieldLabel: this.heightText,
                flex: 1,
                allowDecimals: false,
                allowNegative: false,
                minValue: 20
            },
            {
                // 4
                xtype: 'checkbox',
                name: 'readonly',
                hideEmptyLabel: false,
                boxLabel: this.readonlyText
            },
            {
                // 5
                xtype: 'checkbox',
                name: 'hide_label',
                hideEmptyLabel: false,
                boxLabel: this.hideLabelText
            },
            {
                // 6
                xtype: 'checkbox',
                name: 'sortable',
                fieldLabel: this.childItemsText,
                boxLabel: this.sortableText
            },
            {
                xtype: 'elementtype.configuration.field.default-value',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.accordion',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.group',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.label',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.link',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.select',
                additional: true
            },
            {
                xtype: 'elementtype.configuration.field.table',
                additional: true
            }
        ];
    },

    updateVisibility: function (configuration, fieldType) {
        // language sync
        if (fieldType.config.configuration.required) {
            this.getComponent(0).show();
        }
        else {
            this.getComponent(0).hide();
        }

        // language sync
        if (fieldType.config.configuration.sync) {
            this.getComponent(1).show();
        }
        else {
            this.getComponent(1).hide();
        }

        // width
        if (fieldType.config.configuration.width) {
            this.getComponent(2).show();
        }
        else {
            this.getComponent(2).hide();
        }

        // height
        if (fieldType.config.configuration.height) {
            this.getComponent(3).show();
        }
        else {
            this.getComponent(3).hide();
        }

        // readonly
        if (fieldType.config.configuration.readonly) {
            this.getComponent(4).show();
        }
        else {
            this.getComponent(4).hide();
        }

        // hide label
        if (fieldType.config.configuration.hide_label) {
            this.getComponent(5).show();
        }
        else {
            this.getComponent(5).hide();
        }

        // children sortable
        if (fieldType.config.configuration.sortable) {
            this.getComponent(6).show();
        }
        else {
            this.getComponent(6).hide();
        }

        this.items.each(function (panel) {
            if (panel.additional) {
                panel.updateVisibility(configuration, fieldType);
            }
        });
    },

    loadConfiguration: function (configuration, fieldType) {
        this.updateVisibility(configuration, fieldType);

        this.getForm().setValues({
            required: configuration.required || 'no',
            synchronized: configuration.synchronized || 'no',
            width: configuration.width,
            height: configuration.height,
            sortable: configuration.sortable,
            readonly: configuration.readonly,
            hideLabel: configuration.hideLabel
        });

        this.items.each(function (panel) {
            if (panel.additional) {
                panel.loadConfiguration(configuration, fieldType);
            }
        });

        this.isValid();
    },

    getSaveValues: function () {
        var values = this.getForm().getValues(),
            data = {
                required: values.required,
                synchronized: values.synchronized,
                width: values.width,
                height: values.height,
                sortable: values.sortable,
                readonly: values.readonly,
                hide_label: values.hide_label
            };


        this.items.each(function (panel) {
            if (panel.additional && !panel.hidden) {
                Ext.apply(data, panel.getSaveValues());
            }
        });

        return data;
    },

    isValid: function () {
        var valid = this.getForm().isValid();

        if (valid) {
            this.items.each(function (panel) {
                if (panel.additional && panel.isVisible()) {
                    valid = valid && panel.isValid();
                    if (!valid) {
                        return false;
                    }
                }
            });
        }

        if (valid) {
            this.setIconCls(Phlexible.Icon.get('wrench'));

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
        if (fieldType.config.configuration) {
            this.active = true;
            this.tab.show();
            this.enable();
            this.loadConfiguration(node.get('configuration'), fieldType);
        }
        else {
            this.active = false;
            this.tab.hide();
            this.disable();
        }
    }
});
