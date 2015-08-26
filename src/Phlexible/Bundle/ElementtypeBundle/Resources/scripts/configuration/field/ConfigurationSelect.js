Ext.define('Phlexible.elementtype.configuration.field.ConfigurationSelect', {
    extend: 'Ext.form.FieldSet',
    requires: [
        'Phlexible.elementtypes.configuration.SelectValueGrid'
    ],
    xtype: 'elementtype.configuration.field.select',

    iconCls: Phlexible.Icon.get('ui-combo-box'),
    autoHeight: true,
    labelWidth: 139,
    defaults: {
        anchor: '100%'
    },

    sourceText: '_sourceText',
    editableListText: '_editableListText',
    callbackText: '_callbackText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'combo',
                itemId: 'source',
                name: 'select_source',
                fieldLabel: this.sourceText,
                hideMode: 'display',
                allowBlank: false,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'title'],
                    data: [
                        ['list', this.editableListText],
                        ['function', this.callbackText]
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                mode: 'local',
                typeAhead: false,
                triggerAction: 'all',
                listeners: {
                    select: function (combo, record) {
                        this.updateSelectSourceVisibility(record.get('key'));
                    },
                    scope: this
                }
            },
            {
                xtype: 'elementtype.configuration.selection-value',
                itemId: 'list',
                hidden: true
            },
            {
                xtype: 'combo',
                itemId: 'callback',
                name: 'source_function',
                fieldLabel: 'Function',
                hidden: true,
                editable: false,
                hideMode: 'display',
                allowBlank: false,
                store: Ext.create('Ext.data.Store', {
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('elementtypes_selectfield_providers'),
                        reader: {
                            type: 'json',
                            rootProperty: 'functions'
                        }
                    },
                    fields: ['name', 'title'],
                    autoLoad: false,
                    listeners: {
                        load: function() {
                            this.getComponent('callback').setValue(this.getComponent('callback').getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'title',
                valueField: 'name',
                mode: 'remote',
                typeAhead: false,
                triggerAction: 'all'
            }
        ];

        this.callParent(arguments);
    },

    getValueGrid: function () {
        return this.getComponent(1);
    },

    updateVisibility: function (configuration, fieldType) {
        var isSelect = (fieldType.type === 'select' || fieldType.type === 'multiselect');
        this.getComponent('source').setDisabled(!isSelect);
        this.getComponent('callback').setDisabled(!isSelect);
        this.setVisible(isSelect);

        this.updateSelectSourceVisibility(configuration.select_source);
    },

    updateSelectSourceVisibility: function (source) {
        switch (source) {
            case 'list':
                this.getComponent('list').enable();
                this.getComponent('list').show();
                this.getComponent('callback').disable();
                this.getComponent('callback').hide();
                break;

            case 'function':
                this.getComponent('list').disable();
                this.getComponent('list').hide();
                this.getComponent('callback').enable();
                this.getComponent('callback').show();
                break;

            default:
                this.getComponent('list').disable();
                this.getComponent('list').hide();
                this.getComponent('callback').disable();
                this.getComponent('callback').hide();
        }
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent('source').setValue(configuration.select_source);
        this.getComponent('callback').setValue(configuration.select_function);

        this.getValueGrid().loadData(configuration.select_list, configuration.default_value);

        this.isValid();
    },

    getSaveValues: function () {
        var data = {
            select_source: this.getComponent('source').getValue(),
            select_function: this.getComponent('callback').getValue(),
            select_list: null,
            default_value: null
        };

        if (this.getValueGrid().isVisible()) {
            data.source_function = null;

            var list = [];

            for (var i = 0; i < this.getValueGrid().store.getCount(); i++) {
                var r = this.getValueGrid().store.getAt(i);
                list.push({
                    key: r.get('key'),
                    de: r.get('value_de'),
                    en: r.get('value_en')
                });
            }

            this.getValueGrid().store.commitChanges();

            data.select_list = list;
            data.default_value = this.getValueGrid().getDefaultValue();
        }

        return data;
    },

    isValid: function () {
        return this.getComponent('source').isValid() &&
            this.getComponent('list').isValid() &&
            this.getComponent('callback').isValid();
    }
});
