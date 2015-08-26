Ext.require('Phlexible.fields.Registry');
Ext.require('Phlexible.fields.FieldTypes');
Ext.require('Phlexible.cms.field.FileField');

Phlexible.fields.Registry.register('file', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    // TODO: wie?
    item.media = item.media || {};

    Ext.apply(config, {
        xtype: 'field.file',
        data_id: item.data_id,

        fileId: item.media.fileId || false,
        folder_id: item.media.folder_id || false,
        folder_path: item.media.folder_path || false,
        fileTitle: item.media.name,

        assetType: item.configuration.assetType || '',
        documenttypes: item.configuration.documenttypes || '',
        viewMode: item.configuration.viewMode || 'tile',

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsRepeatable: true
    });

    delete config.width;
    delete config.height;

    return config;
});

Phlexible.fields.FieldTypes.register({
    type: 'file',
    titles: {
        de: 'Datei',
        en: 'File'
    },
    iconCls: 'p-cms-field_file-icon',
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
            width: 0,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        }
    }
});
