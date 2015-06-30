Phlexible.fields.Registry.register('multiselect', function (parentConfig, item, valueStructure, element, repeatableId) {
    var store, storeData, storeMode = 'remote', displayField = 'value';
    if (item.configuration.select_source === 'function' && item.configuration.select_function) {
        store = Ext.create('Ext.data.Store', {
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('elementtypes_selectfield_function'),
                extraParams: {
                    provider: item.configuration.select_function
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            fields: ['key', 'value'],
            sorters: [{
                property: 'value', direction: 'ASC'
            }],
            root: 'data',
            autoLoad: true,
            listeners: {
                load: function () {
                    //newItem.setValue(item.options.default_value);
                },
                scope: this
            }
        });
    } else if (item.configuration.select_source === 'list') {
        displayField = Phlexible.User.getProperty('interfaceLanguage', 'en');
        if (item.configuration.select_list && item.configuration.select_list.length) {
            storeData = item.configuration.select_list;
        } else {
            storeData = [{key: 'no_valid_data', de: 'Keine gültigen Daten', en: 'No valid data'}];
        }
        store = Ext.create('Ext.data.Store', {
            fields: ['key', 'de', 'en'],
            data: storeData
        });
        storeMode = 'local';
    } else {
        displayField = Phlexible.User.getProperty('interfaceLanguage', 'en');
        storeData = [{key: 'no_valid_source', de: 'Keine gültige Quelle', en: 'No valid source'}];
        store = Ext.create('Ext.data.Store', {
            fields: ['key', 'de', 'en'],
            data: storeData
        });
        storeMode = 'local';
    }

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'tagfield',
        hiddenName: config.name,
        width: (parseInt(item.configuration.width, 10) || 200),
        listWidth: (parseInt(item.configuration.width, 10) || 200) - 17,

        hideMode: 'offsets',
        store: store,
        valueField: 'key',
        displayField: displayField,
        mode: storeMode,
        typeAhead: false,
        editable: false,
        triggerAction: 'all',

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: true,
        supportsRepeatable: true
    });

    delete config.name;

    if (item.configuration.readonly) {
        config.editable = false;
        config.readOnly = true;
        config.hideTrigger = true;
        config.onTriggerClick = Ext.emptyFn;
        config.ctCls = 'x-item-disabled';
    }

    return config;
});

Phlexible.fields.FieldTypes.register({
    type: 'multiselect',
    titles: {
        de: 'Multiselect',
        en: 'Multiselect'
    },
    iconCls: Phlexible.Icon.get('ui-combo-box'),
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    defaultValueField: 'default_value_textfield',
    copyFields: [
        'list'
    ],
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
            required: 1,
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        values: {
            default_text: 0,
            default_number: 0,
            default_textarea: 0,
            default_date: 0,
            default_time: 0,
            default_select: 1,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 1,
            source_multi: 1,
            source_values: 1,
            source_function: 1,
            source_datasource: 0,
            text: 0
        }
    }
});
