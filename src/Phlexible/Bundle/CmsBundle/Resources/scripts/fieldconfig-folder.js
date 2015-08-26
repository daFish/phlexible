Ext.require('Phlexible.cms.field.FolderField');

Phlexible.PluginManager.set('field', 'folder', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, valueStructure, element, repeatableId);

    // TODO: wie?
    item.media = item.media || {};

    Ext.apply(config, {
        xtype: 'field.folder',
        hiddenName: config.name,
        data_id: item.data_id,

        width: (parseInt(item.configuration.width, 10) || 200),

        folder_id: item.media.folder_id || false,
        folder_path: item.media.folder_path || false,
        fileTitle: item.media.name,
        menuConfig: {
            minWidth: 300
        },

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsUnlink: true,
        supportsRepeatable: true
    });

    delete config.name;

    return config;
});

Phlexible.PluginManager.set('type', 'folder', {
    type: 'folder',
    titles: {
        de: 'Ordner',
        en: 'Folder'
    },
    iconCls: Phlexible.Icon.get('folder'),
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
