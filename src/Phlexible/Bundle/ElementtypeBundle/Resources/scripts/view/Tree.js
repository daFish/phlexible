Ext.define('Phlexible.elementtype.view.Tree', {
    extend: 'Ext.tree.Panel',
    requires: [
        'Phlexible.elementtype.model.ElementtypeRootNode',
        'Phlexible.elementtype.model.ElementtypeNode'
    ],
    xtype: 'elementtype.tree',

    cls: 'p-elementtype-tree',
    iconCls: Phlexible.Icon.get('document-tree'),
    rootVisible: true,
    border: true,
    loadMask: true,
    margins: '0 0 0 0',
    useArrows: true,
    lines: false,
    autoScroll: true,
    collapseFirst: false,
    animate: false,
    enableDD: true,
    ddGroup: 'elementtypesDD',
    ddScroll: true,
    containerScroll: true,
    viewConfig: {
        plugins: { ptype: 'treeviewdragdrop' }
    },

    mode: 'edit',
    dirty: false,

    saveText: '_saveText',
    resetText: '_resetText',
    addBeforeText: '_addBeforeText',
    addAfterText: '_addAfterText',
    addChildText: '_addChildText',
    copyText: '_copyText',
    cutText: '_cutText',
    pasteText: '_pasteText',
    transformText: '_transformText',
    removeText: '_removeText',
    removeReferenceText: '_removeReferenceText',
    elementtypeText: '_elementtypeText',
    templateElementtypeText: '_templateElementtypeText',

    initComponent: function () {
        this.initMyStore();
        this.initMyDockedItems();
        this.initMyContextMenu();
        this.initMyListeners();
        //this.initMyEvents();

        this.uuidGenerator = new Ext.data.identifier.Uuid();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.TreeStore', {
            model: 'Phlexible.elementtype.model.ElementtypeNode',
            url: '',
            autoLoad: false,
            xroot: {
                text: 'root',
                expanded: false,
                leaf: true
            },
            listeners: {
                load: function (store, nodes) {
                    this.enable();
                    return; // TODO: repair
                    if (!node.hasChildNodes()) {
                        var data = Ext.decode(response.responseText);
                        if (data.error) {
                            this.disable();
                            Ext.MessageBox.alert('Failure', data.msg);
                        }
                    } else {
                        this.fireEvent('elementtypeload', this, this.root.firstChild);
                    }
                },
                scope: this
            }
        });

        /*
        this.root = new Ext.tree.AsyncTreeNode({
            text: 'ich_bin_eigentlich_gar_nicht_da',
            draggable: false,
            id: -1,
            expanded: false,
            uiProvider: Phlexible.elementtypes.ElementtypeStructureRootTreeNodeUI,
            listeners: {
                load: this.enable,
                scope: this
            }
        });
        */
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            itemId: 'tbar',
            items: [
                {
                    text: this.saveText,
                    itemId: 'saveBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.publish,
                    scope: this,
                    disabled: true,
                    hidden: this.mode !== 'edit'
                },
                '->',
                {
                    text: this.resetText,
                    itemId: 'resetBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RESET),
                    handler: function () {
                        Ext.Msg.confirm('Warning', 'Do you really want to reset? All changed will be lost.', function (btn) {
                            if (btn == 'yes') {
                                this.onReset();
                            }
                        }, this);
                    },
                    scope: this,
                    disabled: true,
                    hidden: this.mode !== 'edit'
                },
                {
                    text: this.reloadText,
                    itemId: 'reloadBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                    handler: this.onReset,
                    scope: this,
                    disabled: true,
                    hidden: this.mode === 'edit'
                }
            ]
        }];
    },

    initMyContextMenu: function() {
        // context menu only in edit mode
        if (this.mode !== 'edit') {
            return;
        }
        this.submenuAddBefore = new Ext.menu.Menu();
        this.submenuAddAfter = new Ext.menu.Menu();
        this.submenuAddChild = new Ext.menu.Menu();

        this.contextMenu = new Ext.menu.Menu({
            items: [
                {
                    cls: 'x-btn-text-icon-bold',
                    itemId: 'titleBtn',
                    text: '.',
                    focusable: false
                },
                '-',
                {
                    text: this.addBeforeText,
                    itemId: 'addBeforeBtn',
                    iconCls: Phlexible.Icon.get('node-insert-previous'),
                    menu: this.submenuAddBefore
                },
                {
                    text: this.addAfterText,
                    itemId: 'addAfterBtn',
                    iconCls: Phlexible.Icon.get('node-insert-next'),
                    menu: this.submenuAddAfter

                },
                {
                    text: this.addChildText,
                    itemId: 'addChildBtn',
                    iconCls: Phlexible.Icon.get('node-insert-child'),
                    menu: this.submenuAddChild

                },
                '-',
                {
                    text: this.copyText,
                    itemId: 'copyBtn',
                    iconCls: Phlexible.Icon.get('document-copy'),
                    handler: function () {
                        alert('copy');
                    },
                    disabled: true
                },
                {
                    text: this.cutText,
                    itemId: 'cutBtn',
                    iconCls: Phlexible.Icon.get('scissors'),
                    handler: function () {
                        alert('cut');
                    },
                    disabled: true
                },
                {
                    text: this.pasteText,
                    itemId: 'pasteBtn',
                    iconCls: Phlexible.Icon.get('clipboard-paste'),
                    handler: function () {
                        alert('paste');
                    },
                    disabled: true
                },
                '-',
                {
                    text: this.transformText,
                    itemId: 'transformBtn',
                    iconCls: Phlexible.Icon.get('arrow-transition'),
                    handler: function (item) {
                        this.transform(item.parentMenu.node);

                        this.setDirty();
                    },
                    scope: this
                },
                '-',
                {
                    text: this.removeText,
                    itemId: 'deleteBtn',
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                    handler: function (item) {
                        item.parentMenu.node.remove();

//                        Phlexible.msg('Element Type Action', 'Node "' + item.parentMenu.node.text + '" removed.');

                        this.setDirty();
                    },
                    scope: this
                }
            ]
        });
    },

    initMyListeners: function() {
        this.on({
            rowcontextmenu: this.onRowContextMenu,
            rowclick: this.onRowClick,
            scope: this
        });
    },

    initMyEvents: function () {
        this.dropZone = new Phlexible.elementtypes.ElementtypeStructureTreeDropZone(this, {
            ddGroup: this.ddGroup,
            appendOnly: this.ddAppendOnly === true
        });

        this.dropZone.setPadding(0, 0, this.getInnerHeight(), 0);

        Phlexible.elementtypes.ElementtypeStructureTree.superclass.initEvents.call(this);
    },

    onAddNode: function (item, event) {
        var activeNode = item.sourceNode,
            parentNode = activeNode.parentNode,
            newNode = new Phlexible.elementtype.model.ElementtypeNode({
                text: 'Neu: ' + item.fieldType,
                type: item.fieldType,
                dsId: this.uuidGenerator.generate(),
                cls: 'p-elementtypes-type-' + item.fieldType,
                iconCls: item.iconCls,
                leaf: true,
                editable: true,
                invalid: true,
                properties: {},
                configuration: {},
                validation: {},
                labels: {},
                title: ''
            });

        if (item.appendMode == 'child') {
            activeNode.appendChild(newNode);
            activeNode.expand();
        }

        if (item.appendMode == 'before') {
            parentNode.insertBefore(newNode, activeNode);
        }

        if (item.appendMode == 'after') {
            parentNode.insertBefore(newNode, activeNode.nextSibling);
        }

        this.getSelectionModel().select([newNode]);
        this.getView().addRowCls(newNode, 'dirty');
        this.getView().addRowCls(newNode, 'invalid');
        this.setDirty();
        this.nodeChange(newNode);
    },

    onRowContextMenu: function (tree, node, tr, rowIndex, event) {
        event.stopEvent();

        if (node.get('type') === 'root' && this.getStore().root.firstChild.get('properties').type === 'layout') {
            this.contextMenu.getComponent('addBeforeBtn').disable();
            this.contextMenu.getComponent('addAfterBtn').disable();
            this.contextMenu.getComponent('addChildBtn').disable();

            // CLEANING
            this.submenuAddBefore.removeAll();
            this.submenuAddAfter.removeAll();
            this.submenuAddChild.removeAll();
        } else if (node.get('reference')) {
            this.contextMenu.getComponent('addBeforeBtn').disable();
            this.contextMenu.getComponent('addAfterBtn').disable();
            this.contextMenu.getComponent('addChildBtn').disable();
            this.contextMenu.getComponent('transformBtn').disable();

            if (node.get('reference') === true) {
                this.contextMenu.getComponent('deleteBtn').disable();
            } else {
                this.contextMenu.getComponent('deleteBtn').enable();
            }
            this.contextMenu.getComponent('deleteBtn').setText(this.removeReferenceText);
        } else {
            // ###################### CREATE SUBMENU

            // INIT
            this.contextMenu.getComponent('addBeforeBtn').enable();
            this.contextMenu.getComponent('addAfterBtn').enable();
            this.contextMenu.getComponent('addChildBtn').enable();

            // CLEANING
            this.submenuAddBefore.removeAll();
            this.submenuAddAfter.removeAll();
            this.submenuAddChild.removeAll();

            // BEFORE AND AFTER
            var parentNode = node.parentNode,
                parentType = parentNode.get('type'),
                nodeType = node.get('type'),
                fieldTypeParentMatrix = Phlexible.fields.FieldTypes.get(parentType),
                fieldTypeMatrix = Phlexible.fields.FieldTypes.get(nodeType),
                hasSibling = false,
                hasChild = false,
                language = Phlexible.User.getProperty('interfaceLanguage', 'en');

            Phlexible.fields.FieldTypes.each(function(type, fieldType) {
                if (fieldTypeParentMatrix &&
                    node.get('type') !== 'root' &&
                    fieldType.allowedIn.indexOf(parentType) !== -1) {
                    if (node.parentNode.get('type') != 'referenceroot' || !node.parentNode.firstChild) {
                        hasSibling = true;
                        this.submenuAddBefore.add({
                            fieldType: type,
                            text: fieldType.titles[language],
                            iconCls: fieldType.iconCls,
                            sourceNode: node,
                            appendMode: 'before',
                            handler: this.onAddNode,
                            scope: this
                        });
                        this.submenuAddAfter.add({
                            fieldType: type,
                            text: fieldType.titles[language],
                            iconCls: fieldType.iconCls,
                            sourceNode: node,
                            appendMode: 'after',
                            handler: this.onAddNode,
                            scope: this
                        });
                    }
                }
                if (fieldTypeMatrix && fieldType.allowedIn.indexOf(nodeType) !== -1) {
                    if (node.get('type') === 'referenceroot' && node.firstChild) {
                        return;
                    }
                    hasChild = true;
                    this.submenuAddChild.add({
                        fieldType: type,
                        text: fieldType.titles[language],
                        iconCls: fieldType.iconCls,
                        sourceNode: node,
                        appendMode: 'child',
                        handler: this.onAddNode,
                        scope: this
                    });
                }
            }, this);

            if (!hasSibling) {
                this.contextMenu.getComponent('addBeforeBtn').disable();
                this.contextMenu.getComponent('addAfterBtn').disable();
            }
            if (!hasChild) {
                this.contextMenu.getComponent('addChildBtn').disable();
            }

            this.contextMenu.getComponent('transformBtn').enable();
            this.contextMenu.getComponent('deleteBtn').enable();
            this.contextMenu.getComponent('deleteBtn').setText(this.removeText);

            // ###################### END SUBMENU
        }

        this.contextMenu.getComponent('titleBtn').setText(node.get('text'));

        if (node.get('type') == 'root' || node.get('type') == 'referenceroot') {
            this.contextMenu.getComponent('transformBtn').disable();
            this.contextMenu.getComponent('deleteBtn').disable();
        }
        else if (this.getStore().root.firstChild.get('type') == 'referenceroot') {
            this.contextMenu.getComponent('transformBtn').disable();
            this.contextMenu.getComponent('deleteBtn').enable();
        }

        this.contextMenu.node = node;

        var coords = event.getXY();
        this.contextMenu.showAt([coords[0], coords[1]]);
    },

    setDirty: function () {
        this.dirty = true;

        this.fireEvent('dirty', this);
        this.onDirty();
    },

    onDirty: function () {
        this.getDockedComponent('tbar').getComponent('saveBtn').enable();
        this.getDockedComponent('tbar').getComponent('resetBtn').enable();
    },

    setClean: function () {
        this.dirty = false;

        this.fireEvent('clean', this);
        this.onClean();
    },

    onClean: function () {
        this.getDockedComponent('tbar').getComponent('saveBtn').disable();
        this.getDockedComponent('tbar').getComponent('resetBtn').disable();
    },

    onActivate: function (view, id, node, event) {
//        Phlexible.console.log(view);
//        Phlexible.console.log(id);
//        Phlexible.console.log(node);
//        Phlexible.console.log(event);
//        event.stopEvent();
//        Ext.get(node).addClass('x-view-selected');
//        Phlexible.console.log(node);
//        this.load(id);
    },

    load: function (elementtype) {
        this.disable();

        if (elementtype.get('title')) {
            if (this.mode == 'edit') {
                this.setTitle(elementtype.get('title'));
            } else {
                this.setTitle(elementtype.get('title'));
            }
        } else {
            if (this.mode == 'edit') {
                this.setTitle(this.elementtypeText);
            } else {
                this.setTitle(this.templateElementtypeText);
            }
        }

        this.elementtypeId = elementtype.id;

        this.getStore().removeAll();

        var rootNode = new Phlexible.elementtype.model.ElementtypeRootNode({
            id: 'root',
            elementtypeId: elementtype.id,
            text: elementtype.get('title'),
            type: 'root',
            icon: Phlexible.bundleAsset('/phlexibleelementtype/elementtypes/' + elementtype.get('icon')),
            mappings: elementtype.get('mappings'),
            properties: {
                title: elementtype.get('title'),
                referenceTitle: elementtype.get('referenceTitle'),
                uniqueId: elementtype.get('uniqueId'),
                icon: elementtype.get('icon'),
                hideChildren: elementtype.get('hideChildren'),
                defaultTab: elementtype.get('defaultTab'),
                defaultContentTab: elementtype.get('defaultContentTab'),
                type: elementtype.get('type'),
                template: elementtype.get('template'),
                metaset: elementtype.get('metaset'),
                comment: elementtype.get('comment')
            },
            parentId: null,
            expanded: false,
            leaf: false,
            cls: 'p-elementtypes-type-' + elementtype.get('type')
        });


        if (elementtype.get('new')) {
            this.getStore().getProxy().setUrl('');
            rootNode.set('leaf', true);
        } else {
            this.getStore().getProxy().setUrl(Phlexible.Router.generate('phlexible_api_elementtype_get_elementtype_tree', {
                elementtypeId: elementtype.id,
            }));
            rootNode.set('expanded', true);
        }

        this.setRootNode(rootNode);
        this.getView().refresh();

        if (this.mode == 'edit') {
            this.setClean();
        }

        this.enable();
    },

    onReset: function () {
        if (this.fireEvent('beforereset', this) === false) {
            return;
        }

        Phlexible.msg('Element Type Action', 'Element Type "' + this.root.firstChild.text + '" reset.');

        this.setClean();
        this.disable();
        this.root.reload();
        this.expandAll();

        this.fireEvent('reset', this);
    },

    transform: function (node) {
        if (this.fireEvent('beforetransform', this, node) === false) {
            return;
        }

        var parentNode = node.parentNode,
            dsId = new this.uuidGenerator.generate(),
            reference = new Phlexible.elementtype.model.ElementtypeNode({
                text: 'Reference ' + node.get('text') + ' [v1]',
                dsId: dsId,
                cls: 'p-elementtypes-node p-elementtypes-type-reference',
                leaf: false,
                expanded: true,
                type: 'reference',
                iconCls: Phlexible.elementtype.ICON_REFERENCE,
                reference: {new: true},
                allowDrag: true,
                allowDrop: false,
                editable: false,
                properties: {
                    title: '',
                    type: 'reference',
                    working_title: '',
                    comment: '',
                    image: ''
                },
                configuration: {},
                labels: {},
                validation: {}
            });

        parentNode.insertBefore(reference, node);
        reference.appendChild(node);
        reference.set('editable', false);
        reference.set('allowDrop', false);
        node.set('parentDsId', dsId);
        node.cascade(function(node) {
            node.getUI().addClass('p-elementtypes-reference');
            node.set('editable', false);
            node.set('allowDrag', false);
            node.set('allowDrop', false);
            node.set('draggable', false);
        });
        reference.expand();

        this.fireEvent('transform', this, node);
    },

    publish: function () {
        if (this.fireEvent('beforepublish', this) === false) {
            return;
        }

        var rootNode = this.getRootNode(),
            data;

        if (!this.validateSaveNodes(rootNode)) {
            Ext.Msg.alert('Invalid nodes', 'Tree contains invalid nodes. Please correct them and publish again.');
            return;
        }

        data = Ext.encode(this.processSaveNodes(rootNode));

        Ext.Ajax.request({
            url: Phlexible.Router.generate('elementtypes_tree_save'),
            timeout: 600000,
            params: {
                id: this.elementtypeId,
                data: data
            },
            success: this.onPublishSuccess,
            scope: this
        });
    },

    onPublishSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.setClean();

            this.cleanNodes(this.getRootNode());

            Phlexible.success(data.msg);

            this.fireEvent('publish', this);
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    validateSaveNodes: function(node) {
        var valid = true;
        node.cascade(function(node) {
            if (node.get('invalid')) {
                valid = false;
                return false;
            }
        });
        return valid;
    },

    /*
     Recursive function
     sweeps the tree in all levels
     */
    processSaveNodes: function (node) {
        var childNodes = node.childNodes,
            saveNodes = [],
            i, childNode, children;

        for (i = 0; i < childNodes.length; i++) {
            childNode = childNodes[i],
                nodeData = {
                    id: childNode.id,
                    dsId: childNode.get('dsId') || 0,
                    parentId: node.id,
                    parentDsId: node.get('dsId'),
                    type: childNode.get('type'),
                    reference: childNode.get('reference'),
                    properties: childNode.get('properties'),
                    configuration: childNode.get('configuration'),
                    validation: childNode.get('validation'),
                    labels: childNode.get('labels')
                };
            if (childNode.get('type') !== 'reference' || childNode.get('reference').new) {
                children = this.processSaveNodes(childNode);
                if (children.length) {
                    nodeData.children = children;
                }
            }
            saveNodes.push(nodeData);
        }

        return saveNodes;
    },

    onRowClick: function(tree, node) {
        this.nodeChange(node);
    },

    nodeChange: function (node) {
        //Phlexible.console.log(node);
        this.fireEvent('nodeChange', node, this.mode);
    },

    cleanNodes: function (node) {
        Ext.each(node.childNodes, function(childNode) {
            this.getView().removeRowCls(childNode, 'dirty');
            this.getView().removeRowCls(childNode, 'invalid');

            this.cleanNodes(child[i]);
        }, this);
    },

    findWorkingTitle: function (node, id, title) {
        var child = node.childNodes,
            i;

        for (i = 0; i < child.length; i++) {
            //Phlexible.console.log(child[i].attributes.type);
            if (child[i].get('type') != 'root' && child[i].get('type') != 'referenceroot' &&
                child[i].id !== id && child[i].get('title') === title) {
                return true;
            }
            if (this.findWorkingTitle(child[i], id, title)) {
                return true;
            }
        }

        return false;
    }
});
