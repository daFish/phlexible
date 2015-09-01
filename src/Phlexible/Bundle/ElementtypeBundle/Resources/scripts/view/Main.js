Ext.define('Phlexible.elementtype.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.element.Element',
        'Phlexible.elementtype.view.List',
        'Phlexible.elementtype.view.Tree',
        'Phlexible.elementtype.view.Usage',
        'Phlexible.elementtype.configuration.Root',
        'Phlexible.elementtype.configuration.Field'
    ],
    xtype: 'elementtype.main',

    iconCls: Phlexible.Icon.get('tree'),
    cls: 'p-elementtype-main',
    layout: 'border',
    border: false,

    structureText: '_structureText',
    templateText: '_templateText',

    loadParams: function (params) {
        if (params.type && params.elementtypeId && params.version) {
            this.getListGrid().load(params.type, params.elementtypeId, params.version);
        }
    },

    initComponent: function () {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        var element = Ext.create('Phlexible.element.Element');
        element.lockinfo = {status: 'edit'};

        this.items = [
            {
                xtype: 'elementtype.list',
                itemId: 'list',
                region: 'west',
                width: 300,
                margin: '5 0 5 5',
                collapsible: true,
                params: this.params,
                listeners: {
                    changes: function(hasChanges) {
                        this.getDockedComponent('tbar').setVisible(hasChanges);
                    },
                    elementtypeChange: this.onElementtypeChange,
                    elementtypeTemplateChange: this.onElementtypeTemplateChange,
                    scope: this
                }
            },{
                xtype: 'tabpanel',
                region: 'center',
                itemId: 'tabs',
                margin: 5,
                activeTab: 0,
                border: true,
                plain: true,
                deferredRender: false,
                items: [
                    {
                        title: this.structureText,
                        itemId: 'structure',
                        iconCls: Phlexible.Icon.get('document-tree'),
                        layout: 'border',
                        border: false,
                        items: [
                            {
                                xtype: 'elementtype.tree',
                                region: 'west',
                                itemId: 'templateTree',
                                title: this.templateText,
                                iconCls: Phlexible.Icon.get('blue-folder-tree'),
                                width: 300,
                                margin: '5 0 5 5',
                                split: true,
                                collapsible: true,
                                collapsed: true,
                                disabled: true,
                                mode: 'template'
                            },
                            {
                                region: 'center',
                                itemId: 'structure2',
                                layout: 'border',
                                frame: false,
                                border: false,
                                items: [
                                    {
                                        xtype: 'elementtype.tree',
                                        region: 'west',
                                        itemId: 'editTree',
                                        width: 300,
                                        margin: '5 0 5 0',
                                        split: true,
                                        collapsible: false,
                                        disabled: true,
                                        listeners: {
                                            nodeChange: this.onNodeChange,
                                            beforemovenode: this.onBeforeMoveNode,
                                            beforenodedrop: this.onBeforeNodeDrop,
                                            nodedrop: this.onNodeDrop,
                                            publish: this.onElementtypePublish,
                                            beforereset: this.onElementtypeReset,
                                            dirty: function () {
                                                this.dirty = true;
                                            },
                                            clean: function () {
                                                this.dirty = false;
                                            },
                                            elementtypeload: this.onElementtypeLoad,
                                            scope: this
                                        }
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'card',
                                        itemId: 'cards',
                                        region: 'center',
                                        margin: '5 5 5 0',
                                        border: false,
                                        frame: false,
                                        items: [
                                            {
                                                xtype: 'elementtype.configuration.root',
                                                itemId: 'root-config',
                                                border: true,
                                                plain: true,
                                                listeners: {
                                                    saveRoot: this.onSaveRoot,
                                                    scope: this
                                                }
                                            },
                                            {
                                                xtype: 'elementtype.configuration.field',
                                                itemId: 'field-config',
                                                border: true,
                                                plain: true,
                                                listeners: {
                                                    saveField: this.onSaveField,
                                                    scope: this
                                                }
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'elementtype.usage',
                        itemId: 'usage',
                        disabled: true
                    },
                    {
                        cls: 'p-elements-data-panel',
                        itemId: 'previewWrap',
                        iconCls: Phlexible.Icon.get('magnifier'),
                        border: false,
                        layout: 'fit',
                        autoScroll: true,
                        disabled: true,
                        items: [
                            {
                                xtype: 'elements-elementcontentpanel',
                                itemId: 'preview',
                                autoScroll: true,
                                element: element
                            }
                        ],
                        dockedItems: [{
                            xtype: 'toolbar',
                            dock: 'top',
                            items: [{
                                text: 'master',
                                enableToggle: true,
                                pressed: true,
                                handler: function () {
                                    this.preview(true);
                                },
                                scope: this
                            }]
                        }],
                        listeners: {
                            show: this.preview,
                            scope: this
                        }
                    }
                ]
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'tbar',
            hidden: true,
            items: [{
                region: 'north',
                itemId: 'changes',
                height: 30,
                html: 'Committable elementtype changes detected.',
                plain: true,
                bodyStyle: 'padding-top: 7px; background-color: #EE2C2C; color: white; text-align: center; font-weight: bolder;'
            }]
        }];
    },

    getListGrid: function () {
        return this.getComponent('list');
    },

    getMainTabPanel: function () {
        return this.getComponent('tabs');
    },

    getStructurePanel: function () {
        return this.getMainTabPanel().getComponent('structure');
    },

    getStructure2Panel: function () {
        return this.getStructurePanel().getComponent('structure2');
    },

    getUsageTab: function () {
        return this.getMainTabPanel().getComponent('usage');
    },

    getPropertyCards: function () {
        return this.getStructure2Panel().getComponent('cards');
    },

    getRootTabs: function () {
        return this.getPropertyCards().getComponent('root-config');
    },

    getFieldTabs: function () {
        return this.getPropertyCards().getComponent('field-config');
    },

    getEditTreePanel: function () {
        return this.getStructure2Panel().getComponent('editTree');
    },

    getTemplateTreePanel: function () {
        return this.getStructurePanel().getComponent('templateTree');
    },

    getPreviewWrap: function () {
        return this.getMainTabPanel().getComponent('previewWrap');
    },

    getPreviewPanel: function () {
        return this.getPreviewWrap().getComponent('preview');
    },

    onElementtypeChange: function (elementtype) {
        //this.getRootTabs().clear();
        //this.getFieldTabs().clear();
        this.getEditTreePanel().load(elementtype);
        this.getUsageTab().load(elementtype);
    },

    onElementtypeTemplateChange: function (elementtype) {
        this.getTemplateTreePanel().expand();
        this.getTemplateTreePanel().load(elementtype);
    },

    onElementtypeReset: function (id, title) {
        this.setClean();
    },

    onNodeChange: function (node, mode) {
        if (mode === 'edit') {
            if (node.get('type') === 'root' || node.get('type') === 'referenceroot') {
                this.getPropertyCards().getLayout().setActiveItem(0);
                this.getRootTabs().loadNode(node);
                this.getRootTabs().enable();
            } else {
                this.getPropertyCards().getLayout().setActiveItem(1);
                this.getFieldTabs().loadNode(node);
                this.getFieldTabs().enable();
            }
        } else {
            this.getRootTabs().disable();
            this.getFieldTabs().disable();
        }
    },

    onSaveField: function (node) {
        node.ui.addClass('dirty');
        this.getEditTreePanel().setDirty();

        //this.getPreviewPanel().ownerCt.doLayout();
//        Phlexible.msg('Element Type Action', 'Properties of field "' + node.text + '" saved.');

        this.needPreviewRefresh = true;
    },

    onSaveRoot: function (node) {
        this.getEditTreePanel().setDirty();

//        Phlexible.msg('Element Type Action', 'Properties of root node saved.');

        this.needPreviewRefresh = true;
    },

    onElementtypePublish: function (tree) {
        this.getListGrid().getStore().reload();
        this.getEditTreePanel().getRootNode().reload();
        if (this.getTemplateTreePanel().getRootNode().isLoaded()) {
            this.getTemplateTreePanel().getRootNode().reload();
        }
    },

    onElementtypeLoad: function (tree, node) {
        //Phlexible.console.log(node.id);
        //if (node.id <= 0) {
        //    // reset loaded flag
        //    this.firstElementTreeNodeLoaded = false;
        //}
        //else if (!this.firstElementTreeNodeLoaded) {
        // show properties accordeon after elementtype load
        //    this.firstElementTreeNodeLoaded = true;
        this.getPropertyCards().getLayout().setActiveItem(0);
        if (!tree.disabled) {
            this.getRootTabs().loadProperties(node);
            //this.getFieldTabs().loadProperties(node);
        }
        else {
            this.getRootTabs().clear();
        }
        this.getFieldTabs().clear();

        this.needPreviewRefresh = true;
        if (this.getMainTabPanel().getActiveTab() === this.getPreviewWrap()) {
            this.preview();
        }
    },

    onBeforeMoveNode: function (tree, node, oldParent, newParent, index) {
        // Phlexible.msg('Element Type Action', 'Node "' + node.text + '" moved.');

        this.getEditTreePanel().setDirty();
    },

    onBeforeNodeDrop: function (e) {
        // check if source node comes from another tree
        if (e.dropNode.ownerTree !== e.target.ownerTree) {
            e.dropNode = e.dropNode.clone();

            if (e.dropNode.attributes.type == 'referenceroot') {
                e.dropNode.text = e.dropNode.attributes.properties.root.reference_title;
                e.dropNode.allowChildren = true;
                e.dropNode.attributes.type = 'reference';
                e.dropNode.attributes.ds_id = new Ext.ux.GUID().toString();
                e.dropNode.attributes.reference = {
                    refID: e.dropNode.attributes.element_type_id,
                    refVersion: e.dropNode.attributes.element_type_version
                };
                e.dropNode.attributes.editable = false;
                e.dropNode.attributes.cls = 'p-elementtypes-type-reference';
                e.dropNode.attributes.iconCls = 'p-elementtype-field_reference-icon';

                e.dropNode.firstChild.cascade(function (node) {
                    node.allowChildren = true;
                    node.attributes.reference = true;
                    node.attributes.editable = false;
                    node.attributes.cls += ' p-elementtypes-reference';
                });
//                                                    Phlexible.msg('Element Type Action', 'Reference Element Type "' + e.dropNode.text + '" added from Template Tree to Edit Tree.');
            } else {
                e.dropNode.allowChildren = true;
                e.dropNode.attributes.ds_id = new Ext.ux.GUID().toString();
                e.dropNode.cascade(function (node) {
                    node.allowChildren = true;
                    node.attributes.ds_id = new Ext.ux.GUID().toString();
                });
//                                                    Phlexible.msg('Element Type Action', 'Node "' + e.dropNode.text + '" copied from Template Tree to Edit Tree.');
            }
            this.getEditTreePanel().setDirty();
        }
    },

    onNodeDrop: function(e) {
        var hasInvalidWorkingTitle = function(node) {
            var invalid = false;
            node.getOwnerTree().getRootNode().cascade(function(cascadeNode) {
                if (node.id !== -1 &&
                    node.id !== cascadeNode.id &&
                    node.attributes.properties &&
                    cascadeNode.attributes.properties &&
                    node.attributes.properties.field &&
                    cascadeNode.attributes.properties.field &&
                    node.attributes.properties.field.working_title == cascadeNode.attributes.properties.field.working_title) {
                    invalid = true;
                    return false;
                }
            });
            return invalid;
        };

        e.dropNode.attributes.invalid = hasInvalidWorkingTitle(e.dropNode);
        e.dropNode.attributes.editable = true;

        e.dropNode.ui.addClass('dirty');
        if (e.dropNode.attributes.invalid) {
            e.dropNode.ui.addClass('invalid');
        }

        e.dropNode.cascade(function(node) {
            node.attributes.invalid = hasInvalidWorkingTitle(e.dropNode);
            e.dropNode.attributes.editable = true;

            node.ui.addClass('dirty');
            if (node.attributes.invalid) {
                node.ui.addClass('invalid');
            }
        });
    },

    preview: function (needPreviewRefresh) {
        if (!needPreviewRefresh && !this.needPreviewRefresh) {
            return;
        }

        var rootNode = this.getEditTreePanel().getRootNode(),
            previewPanel = this.getPreviewPanel();

        previewPanel.element.data = {
            default_content_tab: rootNode.firstChild.attributes.properties.root.default_content_tab,
            properties: {
                et_id: rootNode.firstChild.attributes.element_type_id
            }
        };
        previewPanel.element.master = this.getPreviewWrap().getTopToolbar().items.items[0].pressed;
        previewPanel.structure = Phlexible.elements.ElementDataTabHelper.fixStructure(this.processPreviewNodes(rootNode));
        previewPanel.valueStructure = {
            structures: [],
            values: {}
        };
        previewPanel.removeAll();
        previewPanel.lateRender();

        this.needPreviewRefresh = false;
    },

    processPreviewNodes: function (node) {
        if (!node.childNodes || !node.childNodes.length) {
            return [];
        }

        var childNodes = node.childNodes,
            childNode,
            data = [];

        for (var i = 0; i < childNodes.length; i++) {
            childNode = childNodes[i];
            data.push({
                id: childNode.attributes.id,
                dsId: childNode.attributes.ds_id,
                type: childNode.attributes.type,
                configuration: childNode.attributes.properties.configuration,
                labels: childNode.attributes.properties.labels,
                options: childNode.attributes.properties.options,
                validation: childNode.attributes.properties.validation,
                children: this.processPreviewNodes(childNode)
            });
        }

        return data;
    }
});
