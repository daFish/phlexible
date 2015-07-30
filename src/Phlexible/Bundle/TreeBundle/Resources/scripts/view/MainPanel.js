Ext.provide('Phlexible.tree.view.MainPanel');

Ext.require('Phlexible.tree.view.Tree');
Ext.require('Phlexible.tree.view.History');
Ext.require('Phlexible.tree.view.FileSearch');
Ext.require('Phlexible.tree.view.NodeSearch');
Ext.require('Phlexible.teasers.view.Tree');
Ext.require('Phlexible.tree.toolbar.TopToolbar');
Ext.require('Phlexible.element.window.FileLinkWindow');
Ext.require('Phlexible.element.Element');
Ext.require('Phlexible.tree.view.NodeProperties');
Ext.require('Phlexible.tree.view.accordion.QuickInfo');

Ext.require('Phlexible.element.view.tab.Data');
Ext.require('Phlexible.tree.view.tab.AccessControl');
Ext.require('Phlexible.tree.view.tab.Changes');
Ext.require('Phlexible.tree.view.tab.List');
Ext.require('Phlexible.tree.view.tab.Links');
Ext.require('Phlexible.tree.view.tab.Preview');

/**
 * Input params:
 * - id
 *   Siteroot ID
 * - title
 *   Siteroot Title
 */
Phlexible.tree.view.MainPanel = Ext.extend(Ext.Panel, {
    title: Phlexible.elements.Strings.content,
    strings: Phlexible.elements.Strings,
    layout: 'border',
    closable: true,
    border: false,
    cls: 'p-elements-main-panel',
    iconCls: 'p-element-component-icon',

    baseTitle: '',

    loadParams: function (params) {
        if (params.diff) {
            this.getNodeTabs().getComponent(1).getComponent(1).diffParams = params.diff;
        }

        // load element
        if (params.id) {
            var loadParams = {
                id: params.id,
                version: null
            };
            if (params.language) {
                loadParams.language = params.language;
            }
            this.element.reload(loadParams);
        }
        // load tree
        if (params.startNodePath) {
            if (params.startNodePath && params.startNodePath.substr(0, 3) !== '/-1') {
                params.startNodePath = '/-1' + params.startNodePath;
            }
            var n = this.getTree().getSelectionModel().getSelectedNode();
            if (!n || n.getPath() !== params.startNodePath) {
                this.skipLoad = true;
                this.getTree().selectPath(params.startNodePath, 'id');
            }
        }
    },

    initComponent: function () {
        this.addEvents('load');

        if (this.params.start_tid_path) {
            this.skipLoad = true;
            if (this.params.startNodePath.substr(0, 3) !== '/-1') {
                this.params.startNodePath = '/-1' + this.params.startNodePath;
            }
        }

        if (this.params.title) {
            this.setTitle(this.params.title);
            this.baseTitle = this.params.title;
        }

        this.element = new Phlexible.element.Element({
            siterootId: this.params.siterootId,
            language: Phlexible.Config.get('language.frontend'),
            startParams: this.params
        });

        this.element.on({
            load: this.onLoadElement,
            scope: this
        });

        this.element.on({
            beforeload: this.disable,
            afterload: this.onAfterLoadElement,
            beforeSave: this.disable,
            beforeSetOffline: this.disable,
            beforeSetOfflineAdvanced: this.disable,
            load: this.enable,
            saveFailure: this.enable,
            publishFailure: this.enable,
            publishAdvancedFailure: this.enable,
            setOfflineFailure: this.enable,
            setOfflineAdvancedFailure: this.enable,
            scope: this
        });

        var dummyElement = new Phlexible.element.Element({});
        dummyElement.properties = {
            et_type: 'area'
        };

        this.elementPanelIndex = 0;
        this.layoutListPanelIndex = 1;

        this.items = [{
            region: 'west',
            header: false,
            width: 320,
            split: true,
            collapsible: true,
            collapseMode: 'mini',
            border: false,
            layout: 'border',
            tbar: new Phlexible.tree.toolbar.TopToolbar({
                element: this.element
            }),
            items: [
                {
                    xtype: 'tree-tree',
                    region: 'center',
                    title: this.strings.tree,
                    iconCls: 'p-element-tree-icon',
                    header: false,
                    element: this.element,
                    startNodePath: this.params.startNodePath || false,
                    listeners: {
                        nodeSelect: this.onNodeSelect,
                        newElement: function (node) {
                            this.element.showNewElementWindow(node);
                        },
                        newAlias: function (node) {
                            this.element.showNewAliasWindow(node);
                        },
                        scope: this
                    }
                },
                {
                    xtype: 'tabpanel',
                    region: 'south',
                    height: 280,
                    activeTab: 0,
                    border: true,
                    cls: 'p-elements-resource-tabs',
                    items: [
                        {
                            xtype: 'teasers-tree',
                            tabTip: this.strings.teasers,
                            title: '&nbsp;',
                            iconCls: 'p-teaser-teaser-icon',
                            element: this.element,
                            listeners: {
                                teaserselect: function (teaserId, node, language) {
                                    // this.dataPanel.disable();
                                    // this.catchPanel.disable();
                                    // this.getTopToolbar().enable();
                                    if (!language) language = null;

                                    this.element.setTeaserNode(node);

                                    this.getContentPanel().getLayout().setActiveItem(this.elementPanelIndex);
                                    this.element.loadTeaser(teaserId, false, language, true);
                                },
                                areaselect: function (areaId, node) {
                                    // this.dataPanel.disable();
                                    // this.catchPanel.disable();
                                    // this.getTopToolbar().enable();
                                    this.getContentPanel().getLayout().setActiveItem(this.layoutListPanelIndex);
                                    this.getTeaserList().element.id = this.element.id;
                                    this.getTeaserList().element.eid = this.element.eid;
                                    this.getTeaserList().element.language = this.element.language;
                                    this.getTeaserList().element.areaId = areaId;
                                    this.getTeaserList().element.treeNode = node.getOwnerTree().getRootNode();
                                    this.getTeaserList().element.sortMode = 'free';
                                    this.getTeaserList().element.sortDir = 'asc';
                                    this.getTeaserList().doLoad(this.getLayoutListPanel().element);
                                },
                                scope: this
                            }
                        },
                        {
                            xtype: 'tree-node-search',
                            tabTip: this.strings.element_search,
                            title: '&nbsp;',
                            element: this.element
                        },
                        {
                            xtype: 'tree-file-search',
                            tabTip: this.strings.media_search,
                            title: '&nbsp;',
                            element: this.element
                        },
                        {
                            xtype: 'tree-history',
                            tabTip: this.strings.history,
                            title: '&nbsp;',
                            border: false,
                            element: this.element
                        }
                    ]
                }
            ]
        },{
            xtype: 'panel',
            region: 'center',
            header: false,
            layout: 'card',
            activeItem: 0,
            border: false,
            hideMode: 'offsets',
            items: [{
                xtype: 'tabpanel',
                activeTab: 1,
                deferredRender: false,
                items: [
                    {
                        xtype: 'tree-tab-list',
                        tabKey: 'list',
                        element: this.element,
                        listeners: {
                            listLoadTeaser: function (teaserId) {
                                this.fireEvent('listLoadTeaser', teaserId);
                            },
                            listLoadNode: function (nodeId) {
                                this.fireEvent('listLoadNode', nodeId);
                            },
                            listReloadNode: function (nodeId) {
                                this.fireEvent('listReloadNode', nodeId);
                            },
                            scope: this
                        }
                    },
                    {
                        xtype: 'element-tab-data',
                        tabKey: 'data',
                        element: this.element,
                        accordionCollapsed: this.accordionCollapsed
                    },
                    {
                        xtype: 'tree-tab-preview',
                        tabKey: 'preview',
                        element: this.element
                    },
                    {
                        xtype: 'tree-tab-accesscontrol',
                        tabKey: 'rights',
                        element: this.element
                    },
                    {
                        xtype: 'tree-tab-links',
                        tabKey: 'links',
                        element: this.element
                    },
                    {
                        xtype: 'tree-tab-changes',
                        tabKey: 'history',
                        element: this.element
                    }
                ],
                listeners: {
                    listLoadTeaser: function (teaserId) {
                        this.getContentPanel().getLayout().setActiveItem(this.elementPanelIndex);
                        this.element.loadTeaser(teaserId, null, null, 1);
                    },
                    listLoadNode: function (nodeId) {
                        var node = this.getTree().getNodeById(nodeId);
                        if (node) {
                            // uber-node settings gedöns
                            node.select();
                            node.expand();
                            node.ensureVisible();
                            this.element.setTreeNode(node);
                        } else {
                            this.element.setTreeNode(null);
                        }
                        this.element.load(nodeId, null, null, 1);
                    },
                    listReloadNode: function (nodeId) {
                        var node = this.getTree().getNodeById(nodeId);
                        if (node) {
                            // uber-node settings gedöns
                            node.select();
                            node.expand();
                            node.ensureVisible();
                            this.element.setTreeNode(node);
                            node.reload();
                        } else {
                            this.element.setTreeNode(null);
                        }
                    },
                    scope: this
                }
            },{
                xtype: 'tree-tab-list',
                element: dummyElement,
                mode: 'teaser',
                listeners: {
                    listLoadTeaser: function (teaserId) {
                        this.getContentPanel().getLayout().setActiveItem(this.elementPanelIndex);
                        this.element.loadTeaser(teaserId, null, null, 1);
                    },
                    sortArea: function () {
                        this.getLayoutTree().getRootNode().reload();
                    },
                    scope: this
                }
            }]
        },{
            xtype: 'tree-node-properties',
            region: 'east',
            border: false,
            width: 320,
            minWidth: 320,
            collapsible: true,
            split: true,
            element: this.element
        }];

        Phlexible.tree.view.MainPanel.superclass.initComponent.call(this);

        this.on({
            render: function () {
                if (this.params.id) {
                    this.element.reload({
                        id: this.params.id,
                        lock: 1
                    });
                }
            },
            close: function () {
                // remove lock if element is currently locked by me
                if (this.element.getLockStatus() == 'edit') {
                    this.element.unlock(Ext.emptyFn);
                }
            },
            scope: this
        });

        //        this.on('render', function() {
        //            this.mask = new Ext.LoadMask(this.el,{
        //                msg: 'Loading Element',
        //                removeMask: false
        //            });
        //        }, this);

        //    this.elementsTree.on('render', function(tree) {
        //        tree.load();
        //    });
        //    this.elementsTree.root.on('load', function(node) {
        //        node.item(0).select();
        //        this.load(node.item(0).id)
        //    }, this);

    },

    getLeftPanel: function() {
        return this.getComponent(0);
    },

    getLeftTabs: function() {
        return this.getLeftPanel().getComponent(1);
    },

    getLayoutTree: function() {
        return this.getLeftTabs().getComponent(0);
    },

    getTree: function() {
        return this.getLeftPanel().getComponent(1);
    },

    getContentPanel: function() {
        return this.getComponent(1);
    },

    getNodeTabs: function() {
        return this.getContentPanel().getComponent(0);
    },

    getNodeList: function() {
        return this.getNodeTabs().getComponent(0);
    },

    getTeaserTabs: function() {
        return this.getContentPanel().getComponent(1);
    },

    getTeaserList: function() {
        return this.getTeaserTabs().getComponent(0);
    },

    onLoadElement: function (element) {
        //var properties = element.properties;

        // update element panel title
        switch (element.getElementtypeType()) {
            case 'part':
                //this.setTitle(this.baseTitle + ' :: ' + this.strings['part_element'] + ' "' + element.title + '" (Teaser ID: ' + element.properties.teaser_id + ' - ' + this.strings.language + ': ' + element.language + ' - ' + this.strings.version + ': ' + element.version + ')');
                break;

            case 'full':
            default:
                //this.setTitle(this.baseTitle + ' :: ' + this.strings[element.properties.et_type + '_element'] + ' "' + element.title + '" (' + this.strings.tid + ': ' + element.tid + ' - ' + this.strings.language + ': ' + element.language + ' - ' + this.strings.version + ': ' + element.version + ')');
                break;

        }

        /*
        this.setIconClass(null);
        if (this.header) {
            var el = Ext.get(this.header.query('img')[0]);
            el.dom.src = element.icon;
            el.addClass('element-icon');
        }
        */
        //this.setIcon(element.icon);
        //this.mask.hide();
    },

    onAfterLoadElement: function (element) {
        // set active tab to default value
        this.getNodeTabs().items.each(function (item) {
            //Phlexible.console.debug('xxx', element.data.default_tab, item.tabKey);
            if (element.getDefaultTab() === item.tabKey) {
                this.getNodeTabs().setActiveTab(item);
                return false;
            }
        }, this);

        // if current tab is disabled, select list tab (because it's always active)
        if (this.getNodeTabs().activeTab.disabled) {
            this.getNodeTabs().setActiveTab(1);

            if (this.getNodeTabs().activeTab.disabled) {
                this.getNodeTabs().setActiveTab(0);
            }
        }

        //if (this.activeTab && this.activeTab.xtype === 'elements-elementdatapanel') {
        //    element.fireEvent('enableSave');
        //}
    },

    onNodeSelect: function (node, doLock) {
        if (!node) {
            return;
        }

        this.getContentPanel().getLayout().setActiveItem(this.elementPanelIndex);

        if (this.skipLoad) {
            this.element.setTreeNode(node);
            this.skipLoad = false;
        } else {
            this.element.loadTreeNode(node, null, null, doLock);
            //this.element.load(node.id, null, null, doLock);
        }
        node.expand();
    }
});

Ext.reg('tree-main', Phlexible.tree.view.MainPanel);
