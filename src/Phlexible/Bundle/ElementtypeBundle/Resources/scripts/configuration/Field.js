Ext.define('Phlexible.elementtype.configuration.Field', {
    extend: 'Ext.tab.Panel',
    requires: [
        'Phlexible.elementtype.configuration.field.Configurations',
        'Phlexible.elementtype.configuration.field.Labels',
        'Phlexible.elementtype.configuration.field.Properties',
        'Phlexible.elementtype.configuration.field.Validations'
    ],
    xtype: 'elementtype.configuration.field',

    cls: 'p-elementtypes-accordion',
    disabled: true,
    autoScroll: true,
    deferredRender: false,
    enableTabScroll: true,
    activeTab: 0,

    storeText: '_storeText',
    resetText: '_resetText',
    propertiesText: '_propertiesText',
    checkInputText: '_checkInputText',

    /**
     * @event beforeSaveField
     * Fires before field is saved
     * @param {Ext.tree.TreeNode} node The node that the properties will be saved to.
     */

    /**
     * @event saveField
     * Fires after field is saved
     * @param {Ext.tree.TreeNode} node The node that the properties have been saved to.
     */

    /**
     * @private
     */
    initComponent: function () {
        this.initMyItems();

        this.tbar = [
            {
                text: this.storeText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: this.saveProperties,
                scope: this
            },
            '-',
            {
                text: this.resetText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                handler: this.reset,
                scope: this
            }
        ];

        this.callParent(arguments);
    },

    initMyItems: function () {
        this.items = [
            {
                xtype: 'elementtype.configuration.field.properties',
                key: 'field'
            },
            {
                xtype: 'elementtype.configuration.field.labels',
                key: 'labels'
            },
            {
                xtype: 'elementtype.configuration.field.configurations',
                key: 'configuration'
            },
            {
                xtype: 'elementtype.configuration.field.validations',
                key: 'validation'
            }
        ];
    },

    getFieldPropertyPanel: function () {
        return this.getComponent(2);
    },

    clear: function () {
        this.disable();

        this.setTitle(this.propertiesText);

        /*
         this.items.each(function(panel) {
         panel.hide();
         });
         */
    },

    reset: function() {
        this.loadFieldProperties(this.node);
        this.enable();
    },

    loadNode: function (node) {
        this.node = node;

        var fieldType = Phlexible.fields.FieldTypes.get(node.get('type'));

        this.items.each(function (panel) {
            panel.loadNode(node, fieldType);
        });

        if (!this.getActiveTab() || this.getActiveTab().hidden) {
            this.setActiveTab(this.getFieldPropertyPanel());
        }

        this.enable();
    },

    saveProperties: function () {
        if (!this.saveFieldProperties()) {
            return;
        }

        this.loadNode(this.node);
    },

    saveFieldProperties: function () {
        var valid = true;
        this.items.each(function (panel) {
            if (valid && !panel.isValid() && panel.isActive()) {
                valid = false;
                return false;
            }
        });

        var properties = {
            field: {},
            configuration: {},
            validation: {},
            labels: {},
            options: {}
        };

        this.items.each(function (panel) {
            Ext.apply(properties[panel.key], panel.getSaveValues());
        });

        // TODO: only disallow for siblings
        /*
        var root = this.node.getOwnerTree().getRootNode();
        if (this.node.getOwnerTree().findWorkingTitle(root, this.node.id, properties.field.working_title)) {
            valid = false;
        }
        */

        if (this.fireEvent('beforeSaveField', this.node, properties, valid) === false) {
            return false;
        }

        if (!valid) {
            this.node.ui.removeClass('valid');
            this.node.ui.addClass('invalid');
            this.node.attributes.invalid = true;
            Ext.MessageBox.alert('Error', this.checkInputText);
            return;
        }

        this.node.ui.removeClass('invalid');
        this.node.ui.addClass('valid');
        this.node.attributes.invalid = false;

        this.node.attributes.properties = properties;
        this.node.attributes.type = properties.field.type;

        var fieldType = Phlexible.fields.FieldTypes[this.node.attributes.type];

        this.node.setText(properties.labels.fieldLabel[Phlexible.User.getProperty('interfaceLanguage', 'en')] + ' (' + properties.field.working_title + ')');
        this.node.ui.getIconEl().className = 'x-tree-node-icon ' + fieldType.iconCls;

        this.fireEvent('saveField', this.node);

        return true;
    }
});
