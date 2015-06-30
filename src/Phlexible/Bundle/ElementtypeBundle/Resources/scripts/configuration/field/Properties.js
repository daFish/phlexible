Ext.define('Phlexible.elementtype.configuration.field.Properties', {
    extend: 'Ext.form.Panel',
    xtype: 'elementtype.configuration.field.properties',

    iconCls: Phlexible.Icon.get('property'),
    border: false,
    autoScroll: true,
    defaultType: 'textfield',
    labelWidth: 120,
    padding: 5,
    defaultFocus: 'title',
    defaults: {
        anchor: '100%'
    },

    typeText: '_typeText',
    titleText: '_titleText',
    titleHelpText: '_titleHelpText',
    imageText: '_imageText',
    commentText: '_commentText',

    initComponent: function () {
        var sortedTypes = [],
            types = [],
            language = Phlexible.User.getProperty('interfaceLanguage', 'en');

        Phlexible.fields.FieldTypes.each(function(name, fieldType) {
            if (name === 'root' || name === 'referenceroot') {
                return;
            }
            sortedTypes.push(name);
        });
        sortedTypes.sort();

        Ext.each(sortedTypes, function(name) {
            var fieldType = Phlexible.fields.FieldTypes.get(name);
            if (fieldType.allowedIn.length) {
                types.push({
                    key: name,
                    title: fieldType.titles[language],
                    iconCls: fieldType.iconCls
                });
            }
        });

        var dev = Phlexible.User.isGranted('ROLE_SUPER_ADMIN');

        var typeCombo = {
            xtype: 'iconcombo',
            readOnly: true,
            editable: false,
            fieldLabel: this.typeText,
            field: 1,
            name: 'type',
            allowBlank: true,
            store: Ext.create('Ext.data.Store', {
                id: 'key',
                fields: ['key', 'title', 'iconCls'],
                data: types
            }),
            displayField: 'title',
            valueField: 'key',
            mode: 'local',
            iconClsField: 'iconCls',
            triggerAction: 'all',
            hideTrigger: true,
            ctCls: 'x-item-disabled',
            onTriggerClick: Ext.emptyFn,
            helpText: '',
            listeners: {
                select: this.onSelect,
                scope: this
            }
        };

        if (dev) {
            typeCombo.readOnly = false;
            typeCombo.hideTrigger = false;
            typeCombo.ctCls = '';
            delete typeCombo.onTriggerClick;
            typeCombo.helpText = Phlexible.Icon.inline('exclamation') + ' Change this value only if you know what you\'re doing. It might result in data loss.';
        }

        this.items = [
            {
                name: 'title',
                fieldLabel: this.titleText,
                allowBlank: false,
                flex: 1,
                regex: /^[0-9a-z-_]+$/,
                helpText: this.titleHelpText
            },
            typeCombo,
            {
                name: 'image',
                fieldLabel: this.imageText,
                flex: 1,
                hidden: true
            },
            {
                xtype: 'textarea',
                name: 'comment',
                fieldLabel: this.commentText,
                flex: 1,
                height: 100
            }
        ];

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.items.push({
                xtype: 'fieldset',
                title: 'Debug',
                autoHeight: true,
                collapsible: true,
                collapsed: true,
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'textfield',
                        name: 'debugDsId',
                        fieldLabel: 'DS ID',
                        flex: 1,
                        readOnly: true
                    },
                    {
                        xtype: 'textarea',
                        name: 'debugDump',
                        fieldLabel: 'Dump',
                        flex: 1,
                        height: 300,
                        readOnly: true,
                        fieldStyle: {
                            'font-family': 'Courier, Courier New, monospace'
                        }
                    }
                ]
            });
        }

        this.callParent(arguments);
    },

    onSelect: function (combo, record, index) {
    },

    loadProperties: function (properties, node) {
        var values = {
                title: properties.title || '',
                type: properties.type || '',
                comment: properties.comment || '',
                image: properties.image || ''
            };

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            values.debugDsId = node.get('dsId');
            values.debugDump = JSON.stringify(node.data, null, '  ');
        }

        this.getForm().setValues(values);

        this.isValid();
    },

    getSaveValues: function () {
        return this.getForm().getValues();
    },

    isValid: function () {
        if (this.getForm().isValid()) {
            this.setIconCls(Phlexible.Icon.get('property'));

            return true;
        } else {
            this.setIconCls(Phlexible.Icon.get('exclamation-red'));

            return false;
        }
    },

    isActive: function() {
        return true;
    },

    loadNode: function (node, fieldType) {
        this.tab.show();
        this.enable();
        this.loadProperties(node.get('properties'), node);
    }
});
