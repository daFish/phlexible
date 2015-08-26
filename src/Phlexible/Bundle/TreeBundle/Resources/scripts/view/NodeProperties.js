Ext.provide('Phlexible.tree.view.NodeProperties');

Ext.require('Phlexible.tree.view.accordion.Comment');
Ext.require('Phlexible.tree.view.accordion.Configuration');
Ext.require('Phlexible.tree.view.accordion.Diff');
Ext.require('Phlexible.tree.view.accordion.Instances');
Ext.require('Phlexible.tree.view.accordion.Versions');

Phlexible.tree.view.NodeProperties = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.properties,
    strings: Phlexible.elements.Strings,
    cls: 'p-tree-node-properties',

    //autoScroll: true,
    layout: 'border',

    initComponent: function () {
        this.element.on({
            getlock: this.onGetLock,
            islocked: this.onIsLocked,
            removelock: this.onRemoveLock,
            scope: this
        });

        this.populateTabItems();

        this.items = [{
            xtype: 'tree-accordion-quickinfo',
            region: 'north',
            height: 120,
            element: this.element
        },{
            xtype: 'tabpanel',
            region: 'center',
            deferredRender: false,
            activeTab: 0,
            border: false,
            items: this.tabItems
        }];

        delete this.tabItems;

        this.buttons = [{
            text: this.strings.save,
            iconCls: 'p-element-save-icon',
            handler: this.onSave,
            scope: this
        }];

        Phlexible.tree.view.NodeProperties.superclass.initComponent.call(this);
    },

    populateTabItems: function () {
        this.tabItems = [{
            xtype: 'tree-accordion-configuration',
            saveKey: 'configuration',
            element: this.element
        }];

        if (Phlexible.User.isGranted('ROLE_ELEMENT_VERSIONS')) {
            this.tabItems.push({
                xtype: 'tree-accordion-versions',
                element: this.element,
                listeners: {
                    loadVersion: function (version) {
                        this.element.reload({
                            version: version
                        });
                    },
                    scope: this
                }
            });
        }

        if (Phlexible.User.isGranted('ROLE_ELEMENT_INSTANCES')) {
            this.tabItems.push({
                xtype: 'tree-accordion-instances',
                element: this.element,
                listeners: {
                    loadElement: function (id) {
                        this.element.reload({
                            id: id
                        });
                    },
                    loadTeaser: function (id) {
                        this.element.reload({
                            teaserId: id
                        });
                    },
                    scope: this
                }
            });
        }

        if (Phlexible.User.isGranted('ROLE_ELEMENT_COMMENT')) {
            this.tabItems.push({
                xtype: 'tree-accordion-comment',
                saveKey: 'comment',
                element: this.element
            });
        }

        /*
        this.tabItems.push({
            xtype: 'tree-accordion-diff',
            element: this.element
        });
        */

        Ext.each(this.tabItems, function(item) {
            item.title = '&nbsp;';
        });
    },

    onSave: function() {
        var data = this.getData();

        Ext.Ajax.request({
            url: Phlexible.Router.generate('tree_save'),
            params: {
                id: this.element.getNodeId(),
                language: this.element.getLanguage(),
                comment: data.comment,
                configuration: Ext.encode(data.configuration),
            }
        })
    },

    getData: function () {
        var data = {};

        this.getComponent(1).items.each(function (acc) {
            if (!acc.disabled && acc.saveKey && acc.getData && typeof acc.getData === 'function') {
                data[acc.saveKey] = acc.getData();
            }
        });

        return data;
    },

    isValid: function () {
        var valid = true;

        this.getComponent(1).items.each(function (acc) {
            if (!acc.disabled && acc.key && acc.isValid && typeof acc.isValid === 'function') {
                valid &= !!acc.isValid();
            }
        });

        return valid;
    },

    saveData: function () {
        alert("saveData");return;
        this.getComponent(1).items.each(function (acc) {
            if (!acc.disabled && acc.saveData && typeof acc.saveData === 'function') {
                alert('acc.saveData');
                acc.saveData();
            }
        });
    },

    onGetLock: function () {
        this.getComponent(1).items.each(function (acc) {
            if ((acc.getData && typeof acc.getData === 'function') || (acc.saveData && typeof acc.saveData === 'function')) {
                acc.enable();
            }
        });
    },


    onIsLocked: function () {
        this.getComponent(1).items.each(function (acc) {
            if ((acc.getData && typeof acc.getData === 'function') || (acc.saveData && typeof acc.saveData === 'function')) {
                acc.disable();
            }
        });
    },


    onRemoveLock: function () {
        this.getComponent(1).items.each(function (acc) {
            if ((acc.getData && typeof acc.getData === 'function') || (acc.saveData && typeof acc.saveData === 'function')) {
                acc.disable();
            }
        });
    }
});

Ext.reg('tree-node-properties', Phlexible.tree.view.NodeProperties);
