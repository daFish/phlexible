Ext.define('Phlexible.elementtype.configuration.Root', {
    extend: 'Ext.tab.Panel',
    requires: [
        'Phlexible.elementtype.configuration.root.Properties',
        'Phlexible.elementtype.configuration.root.Mappings'
    ],
    xtype: 'elementtype.configuration.root',

    cls: 'p-elementtypes-accordion',
    disabled: true,
    autoScroll: true,
    deferredRender: false,
    activeTab: 0,

    storeText: '_storeText',
    resetText: '_resetText',
    propertiesText: '_propertiesText',
    propertiesOfText: '_propertiesOfText',
    checkInputText: '_checkInputText',

    /**
     * @event beforeSaveRoot
     * Fires before root is saved
     * @param {Ext.tree.TreeNode} node The node that the properties will be saved to.
     */
    /**
     * @event saveRoot
     * Fires after root is saved
     * @param {Ext.tree.TreeNode} node The node that the properties have been saved to.
     */

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'elementtype.configuration.root.properties',
                isRootAccordion: true
            },
            {
                xtype: 'elementtype.configuration.root.mappings',
                border: false,
                isRootAccordion: true
            }
        ];
    },

    initMyDockedItems: function() {
        this.tbar = [
            {
                text: this.storeText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: function () {
                    this.saveProperties();
                },
                scope: this
            },
            '-',
            {
                text: this.resetText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                handler: function () {
                    this.loadNode(this.node);
                },
                scope: this
            }
        ];
    },

    getRootPropertyPanel: function () {
        return this.getComponent(0);
    },

    getRootMappingsPanel: function () {
        return this.getComponent(1);
    },

    clear: function () {
        this.disable();

        //this.setTitle(this.propertiesText);
    },

    loadNode: function (node) {
        this.node = node;
        //this.setTitle(this.propertiesOfText + ' "' + node.get('text') + '" [' + node.get('type') + ']');

        this.items.each(function (panel) {
            if (panel.isFieldAccordion) {
                panel.hide();
            }
        });

        this.getRootPropertyPanel().loadNode(node);
        this.getRootMappingsPanel().loadNode(node);

        this.enable();
    },

    saveProperties: function () {
        if (this.node.attributes.type == 'root') {
            if (!this.saveRootProperties()) {
                return;
            }
        } else if (this.node.attributes.type == 'referenceroot') {
            if (!this.saveReferenceRootProperties()) {
                return;
            }
        }

        this.loadProperties(this.node);
    },

    saveRootProperties: function () {
        var valid = true;

        valid = (this.getRootPropertyPanel().hidden || this.getRootPropertyPanel().isValid()) && valid;
        valid = (this.getRootMappingsPanel().tab.hidden || this.getRootMappingsPanel().isValid()) && valid;

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

        var properties = {
            root: this.getRootPropertyPanel().getSaveValues(),
            mappings: this.getRootMappingsPanel().getSaveValues()
        };

        if (!this.fireEvent('beforeSaveRoot', this.node, properties)) {
            return;
        }

        if (this.node.attributes.properties.root.icon !== properties.root.icon) {
            this.node.getUI().getIconEl().src = '/bundles/phlexibleelementtype/elementtypes/' + properties.root.icon;
        }


        this.node.attributes.properties = properties;

        this.fireEvent('saveRoot', this.node);
    },

    saveReferenceRootProperties: function () {
        var propertiesValid = this.getRootPropertyPanel().isValid();

        if (!propertiesValid) {
            Ext.MessageBox.alert('Error', this.checkInputText);
            return;
        }

        var properties = {
            root: this.getRootPropertyPanel().getSaveValues(),
            mappings: {}
        };

        if (!this.fireEvent('beforeSaveRoot', this.node, properties)) {
            return;
        }

        this.node.attributes.properties = properties;

        this.fireEvent('saveRoot', this.node);
    }
});
