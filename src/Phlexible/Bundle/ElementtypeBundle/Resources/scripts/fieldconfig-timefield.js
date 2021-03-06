Phlexible.Storage.set('field', 'time', function (parentConfig, item, valueStructure, element, repeatableId) {
    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    Ext.apply(config, {
        xtype: 'timefield',
        format: 'H:i:s',

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: {unlinkEl: 'trigger'},
        supportsRepeatable: true
    });

    return config;
});

Phlexible.Storage.set('type', 'time', {
    type: 'time',
    titles: {
        de: 'Uhrzeit',
        en: 'Time'
    },
    iconCls: Phlexible.Icon.get('clock'),
    allowedIn: [
        'tab',
        'accordion',
        'group',
        'referenceroot'
    ],
    defaultValueField: 'default_value_timefield',
    config: {
        properties: {
        },
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
            default_time: 1,
            default_select: 0,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 0,
            source_values: 0,
            source_function: 0,
            source_datasource: 0,
            text: 0
        }
    }
});
