Ext.define('Phlexible.elementtype.configuration.field.ConfigurationLink', {
    extend: 'Ext.form.FieldSet',
    xtype: 'elementtype.configuration.field.link',

    iconCls: Phlexible.Icon.get('globe'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    linkTypesText: '_linkTypesText',
    internalText: '_internalText',
    intraText: '_intraText',
    externalText: '_externalText',
    emailText: '_emailText',
    elementtypesText: '_elementtypesText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'checkboxgroup',
                fieldLabel: this.linkTypesText,
                columns: 1,
                allowBlank: false,
                items: [
                    {
                        name: 'link_allow_internal',
                        boxLabel: this.internalText,
                        listeners: {
                            check: function (cb, value) {
                                this.getComponent(1).setDisabled(!value && !this.getComponent(0).items.items[1].getValue());
                            },
                            scope: this
                        }
                    },
                    {
                        name: 'link_allow_intra',
                        boxLabel: this.intraText,
                        listeners: {
                            check: function (cb, value) {
                                this.getComponent(1).setDisabled(!value && !this.getComponent(0).items.items[0].getValue());
                            },
                            scope: this
                        }
                    },
                    {
                        name: 'link_allow_external',
                        boxLabel: this.externalText
                    },
                    {
                        name: 'link_allow_email',
                        boxLabel: this.emailText
                    }
                ]
            },
            {
                xtype: 'tagfield',
                name: 'link_element_types',
                fieldLabel: this.elementtypesText,
                maxHeight: 200,
                store: Ext.create('Ext.data.Store', {
                    url: Phlexible.Router.generate('phlexible_api_elementtype_get_elementtypes'),
                    fields: ['id', 'title'],
                    id: 'id',
                    root: 'elementtypes',
                    autoLoad: true
                }),
                displayField: 'title',
                valueField: 'id',
                editable: false,
                triggerAction: 'all',
                selectOnFocus: false,
                mode: 'local'
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        var isLink = fieldType.type === 'link';
        this.getComponent(0).setDisabled(!isLink);
        this.getComponent(1).setDisabled(!isLink);
        this.setVisible(isLink);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).items.items[0].setValue(configuration.link_allow_internal);
        this.getComponent(0).items.items[1].setValue(configuration.link_allow_intra);
        this.getComponent(0).items.items[2].setValue(configuration.link_allow_external);
        this.getComponent(0).items.items[3].setValue(configuration.link_allow_email);
        this.getComponent(1).setValue(configuration.link_element_types || '');

        if (configuration.link_allow_internal || configuration.link_allow_intra) {
            this.getComponent(1).enable();
        }
        else {
            this.getComponent(1).disable();
        }

        this.isValid();
    },

    getSaveValues: function () {
        return {
            link_allow_internal: this.getComponent(0).items.items[0].getValue(),
            link_allow_intra: this.getComponent(0).items.items[1].getValue(),
            link_allow_external: this.getComponent(0).items.items[2].getValue(),
            link_allow_email: this.getComponent(0).items.items[3].getValue(),
            link_element_types: this.getComponent(1).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid();
    }
});
