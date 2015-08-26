Phlexible.PluginManager.set('field', 'displayfield', function (parentConfig, item, valueStructure, element, repeatableId) {
    // labels
    var hideLabel,
        label,
        labelSeparator = ':',
        contextHelp = item.labels.contextHelp || {},
        prefix = item.labels.prefix || {},
        suffix = item.labels.suffix || {};

    if (parentConfig.singleLineLabel) {
        label = parentConfig.singleLineLabel;
        parentConfig.singleLineLabel = '';
        hideLabel = false;
    } else if (parentConfig.singleLine) {
        hideLabel = true;
        label = item.labels.fieldLabel[Phlexible.User.getProperty('interfaceLanguage', 'en')];
    } else if (item.configuration.hide_label) {
        hideLabel = false;
        label = '';
        labelSeparator = '';
    } else {
        hideLabel = false;
        label = item.labels.fieldLabel[Phlexible.User.getProperty('interfaceLanguage', 'en')];
    }

    var field_prefix = 'field_' + item.dsId + '_';
    if (item.data_id) {
        field_prefix += 'id-' + item.data_id;
    } else {
        field_prefix += Ext.id(null, 'new');
    }

    var config = {
        xtype: 'displayfield',
        name: field_prefix + (repeatableId ? '#' + repeatableId : ''),
        dsId: item.dsId,

        fieldLabel: label,
        helpText: contextHelp[Phlexible.User.getProperty('interfaceLanguage', 'en')] || '',
        prefix: prefix[Phlexible.User.getProperty('interfaceLanguage', 'en')] || '',
        suffix: suffix[Phlexible.User.getProperty('interfaceLanguage', 'en')] || '',
        labelSeparator: labelSeparator,
        hideLabel: hideLabel,
        value: item.content,
        width: item.configuration.width || 100,
        element: element,

        supportsPrefix: true,
        supportsSuffix: true
    };

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    return config;
});

Phlexible.PluginManager.set('type', 'displayfield', {
    type: 'displayfield',
    titles: {
        de: 'Anzeigefeld',
        en: 'Displayfield'
    },
    iconCls: Phlexible.Icon.get('ui-label'),
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
            prefix: 0,
            suffix: 0,
            help: 1
        },
        configuration: {
            required: 0,
            sync: 0,
            width: 1,
            height: 0,
            readonly: 0,
            hide_label: 1,
            sortable: 0
        }
    }
});
