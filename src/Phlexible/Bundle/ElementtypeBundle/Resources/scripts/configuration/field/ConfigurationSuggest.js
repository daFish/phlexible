Ext.define('Phlexible.elementtype.configuration.field.ConfigurationSuggest', {
    extend: 'Ext.form.FieldSet',
    requires: [
        'Phlexible.datasource.model.Datasource'
    ],
    xtype: 'elementtype.configuration.field.suggest',

    iconCls: Phlexible.Icon.get('ui-text-field-suggestion'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    initComponent: function () {
        this.items = [
            {
                xtype: 'combo',
                itemId: 'datasource',
                name: 'suggest_source',
                editable: false,
                fieldLabel: 'Source',
                hideMode: 'display',
                allowBlank: false,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.datasource.model.Datasource',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_api_datasource_get_datasources'),
                        reader: {
                            type: 'json',
                            rootProperty: 'datasources'
                        }
                    },
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            this.getComponent('datasource').setValue(this.getComponent('datasource').getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'title',
                valueField: 'id',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all'
            }
        ];

        this.callParent(arguments);
    },

    updateVisibility: function (configuration, fieldType) {
        this.getComponent('datasource').setDisabled(fieldType.type !== 'suggest');
        this.setVisible(fieldType.type === 'suggest');
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent('datasource').setValue(configuration.suggest_source);

        this.isValid();
    },

    getSaveValues: function () {
        return {
            suggest_source: this.getComponent('datasource').getValue() || ''
        };
    },

    isValid: function () {
        return this.getComponent('datasource').isValid();
    }
});
