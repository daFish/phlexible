Ext.define('Phlexible.frontendmedia.configuration.FieldConfigurationFile', {
    extend: 'Ext.form.FieldSet',
    xtype: 'frontendmedia-configuration-field-configuration-file',

    iconCls: 'p-frontendmedia-field_file-icon',
    autoHeight: true,
    labelWidth: 139,

    assetTypeText: '_assetTypeText',
    documenttypesText: '_documenttypesText',
    viewModeText: '_viewModeText',

    initComponent: function () {
        this.items = [
            {
                xtype: 'combobox',
                itemId: 'assettype',
                name: 'assettype',
                fieldLabel: this.assetTypeText,
                flex: 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'title'],
                    data: [
                        {key: 'image', title: 'Image'},
                        {key: 'audio', title: 'Audio'},
                        {key: 'video', title: 'Video'},
                        {key: 'document', title: 'Document'},
                        {key: 'flash', title: 'Flash'},
                        {key: 'archive', title: 'Archive'},
                        {key: 'other', title: 'Other'}
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                selectOnFocus: false,
                triggerAction: 'all',
                mode: 'local',
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            },
            {
                xtype: 'tagfield',
                itemId: 'documenttypes',
                name: 'documenttypes',
                fieldLabel: this.documenttypesText,
                flex: 1,
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.mediatype.model.MediaType',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_api_mediatype_get_mediatypes'),
                        reader: {
                            type: 'json',
                            rootProperty: 'mediatypes',
                            idProperty: 'name'
                        }
                    },
                    sorters: [{
                        property: 'name',
                        direction: 'asc'
                    }],
                    autoLoad: true,
                    listeners: {
                        load: function() {
                            this.getComponent('documenttypes').setValue(this.getComponent('documenttypes').getValue());
                        },
                        scope: this
                    }
                }),
                displayField: 'name',
                valueField: 'name',
                editable: false,
                selectOnFocus: false,
                triggerAction: 'all',
                mode: 'remote',
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            },
            {
                xtype: 'combobox',
                itemId: 'viewMode',
                name: 'viewMode',
                fieldLabel: this.viewModeText,
                flex: 1,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'title'],
                    data: [
                        {key: 'extralarge', title: 'Extra Large'},
                        {key: 'large', title: 'Large'},
                        {key: 'medium', title: 'Medium'},
                        {key: 'small', title: 'Small'},
                        {key: 'tile', title: 'Tile'},
                        {key: 'detail', title: 'Detail'}
                    ]
                }),
                displayField: 'title',
                valueField: 'key',
                editable: false,
                triggerAction: 'all',
                mode: 'local',
                selectOnFocus: false,
                triggers: {
                    clear: {
                        type: 'clear'
                    }
                }
            }
        ];

        Phlexible.frontendmedia.configuration.FieldConfigurationFile.superclass.initComponent.call(this);
    },

    updateVisibility: function (configuration, fieldType) {
        var isFile = fieldType.type === 'file';
        this.getComponent(0).setDisabled(!isFile);
        this.getComponent(1).setDisabled(!isFile);
        this.getComponent(2).setDisabled(!isFile);
        this.setVisible(isFile);
    },

    loadConfiguration: function (configuration, fieldType) {
        this.getComponent(0).setValue(configuration.assetType || null);
        this.getComponent(1).setValue(configuration.documenttypes || '');
        this.getComponent(2).setValue(configuration.viewMode || '');

        this.isValid();
    },

    getSaveValues: function () {
        return {
            assetType: this.getComponent(0).getValue(),
            documenttypes: this.getComponent(1).getValue(),
            viewMode: this.getComponent(2).getValue()
        };
    },

    isValid: function () {
        return this.getComponent(0).isValid() &&
            this.getComponent(1).isValid() &&
            this.getComponent(2).isValid();
    }
});
