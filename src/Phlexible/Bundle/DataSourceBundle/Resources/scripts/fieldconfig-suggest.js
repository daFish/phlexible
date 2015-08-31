Phlexible.Storage.set('field', 'suggest', function (parentConfig, item, valueStructure, element, repeatableId) {
    var store, storeMode = 'local';

    if (item.configuration.suggest_source) {
        /*
         store = new Ext.data.SimpleStore({
         fields: ['key', 'value'],
         data: item.options
         });
         */
        storeMode = 'remote';
        store = Ext.create('Ext.data.Store', {
            fields: ['key', 'value'],
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('elementtypes_selectfield_suggest'),
                extraParams: {
                    id: item.configuration.suggest_source,
                    ds_id: item.dsId,
                    language: element.language
                },
                reader: {
                    type: 'json',
                    rootProperty: 'data'
                }
            },
            autoLoad: false
        });
    } else {
        store = new Ext.data.SimpleStore({
            fields: ['key', 'value'],
            data: [
                ['no_valid_data', 'no_valid_data']
            ]
        });
    }

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'tag',
        name: config.name + '[]',
        width: (parseInt(item.configuration.width, 10) || 200),

        allowAddNewData: true,
        source_id: item.source_id,
        valueDelimiter: Phlexible.Config.get('suggest.seperator'),

        regex: /^[^,]+$/,
        hideMode: 'offsets',
        store: store,
        valueField: 'key',
        displayField: 'value',
        mode: storeMode,
        triggerAction: 'all',
        editable: true,
        selectOnFocus: true,
        minChars: 2,
        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsUnlink: {styleEl: 'outerWrapEl', unlinkEl: 'wrap'},
        supportsRepeatable: true,
        listeners: {
            newitem: function (bs, v) {
                var newObj = {
                    key: v,
                    value: v
                };
                bs.addNewItem(newObj);
            },
            scope: this
        }
    });

    if (config.readOnly) {
        config.editable = false;
        config.hideTrigger = true;
        config.onTriggerClick = Ext.emptyFn;
    }

    return config;
});

Phlexible.Storage.set('type', 'suggest', {
    type: 'suggest',
    titles: {
        de: 'Suggest',
        en: 'Suggest'
    },
    iconCls: Phlexible.Icon.get('ui-text-field-suggestion'),
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
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
        }
    }
});
