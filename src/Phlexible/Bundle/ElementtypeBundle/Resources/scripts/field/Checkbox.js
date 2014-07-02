Phlexible.fields.Registry.addFactory('checkbox', function(parentConfig, item, valueStructure, pos, element, repeatablePostfix, forceAdd) {
	if (element.master) {
		element.prototypes.addFieldPrototype(item);
	}

	element.prototypes.incCount(item.dsId);

	var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatablePostfix, forceAdd);

	Ext.apply(config, {
		xtype: 'xcheckbox',
		boxLabel: (item.labels.boxlabel[Phlexible.Config.get('user.property.interfaceLanguage', 'en')] || ''),
		checked: item.rawContent,

		submitOffValue: '',
		submitOnValue: 'on',

		supportsPrefix: true,
		supportsSuffix: true,
		supportsDiff: true,
		supportsUnlink: {styleEl: 'imageEl'},
		supportsRepeatable: true
	});

	delete config.value;

	return config;
});

Phlexible.fields.FieldTypes.addField('checkbox', {
    titles: {
        de: 'Checkbox',
        en: 'Checkbox'
    },
    iconCls: 'p-elementtype-field_checkbox-icon',
    allowedIn: [
		'tab',
		'accordion',
		'group',
		'referenceroot'
	],
    defaultValueField: 'default_value_checkbox',
    config: {
        labels: {
            field: 1,
            box: 1,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
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
            default_select: 0,
            default_link: 0,
            default_checkbox: 1,
            default_table: 0,
            source: 0,
            source_single: 1,
            source_values: 1,
            source_function: 0,
            source_datasource: 0,
            text: 0
        },
        validation: {
            required: 1,
            text: 0,
            numeric: 0,
            content: 0
        }
    }
});