Ext.provide('Phlexible.teasers.view.Tree');

Ext.require('Phlexible.teasers.tree.TreeNodeUI');
Ext.require('Phlexible.teasers.tree.TreeLoader');
Ext.require('Phlexible.teasers.window.NewTeaserWindow');
Ext.require('Phlexible.teasers.window.NewTeaserInstanceWindow');

Phlexible.teasers.view.Tree = Ext.extend(Ext.tree.TreePanel, {
    title: Phlexible.teasers.Strings.layout,
    strings: Phlexible.teasers.Strings,
    rootVisible: false,
    disabled: true,
    autoScroll: true,
    enableDD: true,
    cls: 'p-teaser-tree',

    initComponent: function () {
        this.element.on({
            load: this.onLoadElement,
            setLanguage: this.onSetLanguage,
            publishAdvanced: function (element) {
                if (element.getTeaserId()) {
                    element.setTeaserNode(null);
                    this.getRootNode().reload();
                }
            },
            setOffline: function (element) {
                if (element.getTeaserId()) {
                    if (element.getTeaserNode() && 'function' == typeof element.getTeaserNode().setText) {
                        var iconEl = element.getTeaserNode().getUI().getIconEl();
                        if (iconEl.src.match(/\/status\/[a-z]+/)) {
                            iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                        }
                    }
                }
            },
            setOfflineAdvanced: function (element) {
                if (element.getTeaserId()) {
                    element.setTeaserNode(null);
                    this.getRootNode().reload();
                }
            },
            save: function (element, result) {
                if (element.getTeaserId()) {
                    if (element.getTeaserNode() && 'function' == typeof element.getTeaserNode().setText) {
                        var data = result.data;
                        element.getTeaserNode().setText(data.title);
                        var iconEl = element.getTeaserNode().getUI().getIconEl();
                        if (data.status) {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '/status/' + data.status);
                            } else {
                                iconEl.src += '?status=' + data.status;
                            }
                        } else {
                            if (iconEl.src.match(/\/status\/[a-z]+/)) {
                                iconEl.src = iconEl.src.replace(/\/status\/[a-z]+/, '');
                            }
                        }
                    } else {
                        Phlexible.console.warn('element.teaserNode is undefined');
                    }
                }
            },
            scope: this
        });

        this.loader = new Phlexible.teasers.tree.TreeLoader({
            dataUrl: Phlexible.Router.generate('teasers_layout_tree'),
            baseParams: {
                language: this.element.getLanguage()
            },
            preloadChildren: true,
            listeners: {
                load: function (loader, rootNode) {
                    if (this.selectId) {
                        var targetNode = null;
                        rootNode.cascade(function (currentNode) {
                            if (currentNode.id == this.selectId) {
                                //Phlexible.console.info('loader.select()');
                                currentNode.select();
                                targetNode = currentNode;
                                return false;
                            }
                        }, this);
                        this.fireEvent('teaserselect', this.selectId, targetNode, this.selectLanguage);
                        this.selectId = null;
                        this.selectLanguage = null;
                    }
                },
                scope: this
            }
        });

        this.root = new Ext.tree.TreeNode({
            text: 'Root',
            id: -1,
            cls: 'node_level_0',
            type: 'root',
            expanded: true,
            allowDrag: false,
            allowDrop: false
        });

        /*this.selModel = new Ext.tree.DefaultSelectionModel({
         listeners: {
         selectionchange: {
         fn: function(sm, node) {
         if(node.attributes.type == 'teaser' && !node.attributes.inherit) {
         this.fireEvent('teaserselect', node.attributes.eid);
         }
         },
         scope: this
         }
         }
         });*/

        this.contextMenu = new Ext.menu.Menu({
            element: this.element,
            items: [
                {
                    // 0
                    text: '.',
                    cls: 'x-btn-text-icon-bold',
                    iconCls: 'p-teaser-layoutarea-icon'
                },
                '-',
                {
                    // 2
                    text: this.strings.add_teaser,
                    iconCls: 'p-teaser-teaser_add-icon',
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        var w = new Phlexible.teasers.NewTeaserWindow({
                            submitParams: {
                                tree_id: node.attributes.parentId,
                                areaId: node.attributes.areaId
                            },
                            listeners: {
                                success: function (window, result) {
                                    this.element.setLanguage(result.data.language, true);

                                    this.selectId = result.id;
                                    this.selectLanguage = result.data.language;

                                    this.loader.baseParams.language = this.selectLanguage;
                                    this.root.reload();
                                },
                                scope: this
                            }
                        });
                        w.show();
                    },
                    scope: this
                },
                {
                    // 3
                    text: this.strings.add_teaser_reference,
                    iconCls: 'p-teaser-teaser_reference-icon',
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        var w = new Phlexible.teasers.NewTeaserInstanceWindow({
                            element: this.element,
                            listeners: {
                                teaserSelect: function (forTeaserId, areaId, tid) {
                                    Ext.Ajax.request({
                                        url: Phlexible.Router.generate('teasers_layout_createinstance'),
                                        params: {
                                            for_teaser_id: forTeaserId,
                                            id: areaId,
                                            tid: tid
                                        },
                                        success: function (response) {
                                            var data = Ext.decode(response.responseText);

                                            if (data.success) {
                                                this.getRootNode().reload();

                                                Phlexible.success(data.msg);
                                            } else {
                                                Ext.MessageBox.alert('Failure', data.msg);
                                            }
                                        },
                                        scope: this
                                    });
                                },
                                scope: this
                            }
                        });
                        w.show();
                    },
                    scope: this
                },
                '-',
                {
                    // 5
                    text: this.strings.inherited,
                    checked: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        if (!node.attributes.inherit) {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_inherit'),
                                params: {
                                    nodeId: node.attributes.parentId,
                                    id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        } else {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_stop'),
                                params: {
                                    nodeId: node.attributes.parentId,
                                    id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                    //                          node.parentNode.reload();
                                }.createDelegate(this, [node], true)
                            });
                        }
                    },
                    scope: this
                },
                {
                    // 6
                    text: this.strings.shown_here,
                    checked: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        if (!node.attributes.hide) {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_hide'),
                                params: {
                                    id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                }.createDelegate(this, [node], true)
                            });
                        } else {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('teasers_layout_show'),
                                params: {
                                    id: node.id
                                },
                                success: function (response, options, node) {
                                    var data = Ext.decode(response.responseText);

                                    if (data.success) {
                                        Phlexible.success(data.msg);

                                        node.getOwnerTree().getRootNode().reload();
                                    } else {
                                        Ext.MessageBox.alert('Failure', data.msg);
                                    }
                                }.createDelegate(this, [node], true)
                            });
                        }
                    },
                    scope: this
                },
                // 7
                '-',
                {
                    // 8
                    text: Phlexible.elements.Strings.copy,
                    iconCls: 'p-element-copy-icon',
                    handler: function (menu) {
                        var node = menu.parentMenu.node;
                        Phlexible.Clipboard.set(node.text, node, 'teaser');
                    }
                },
                {
                    // 9
                    text: Phlexible.elements.Strings.paste,
                    iconCls: 'p-element-paste-icon',
                    menu: [
                        {
                            text: '-',
                            cls: 'x-btn-text-icon-bold',
                            canActivate: false
                        },
                        '-',
                        {
                            text: Phlexible.elements.Strings.paste_alias,
                            iconCls: 'p-teaser-teaser_reference-icon',
                            handler: function (menu) {
                                if (Phlexible.Clipboard.isInactive() || Phlexible.Clipboard.getType() != 'teaser') {
                                    return;
                                }

                                var forNode = Phlexible.Clipboard.getItem();
                                var node = menu.parentMenu.parentMenu.node;

                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('teasers_layout_createinstance'),
                                    params: {
                                        id: forNode.id,
                                        areaId: node.attributes.areaId,
                                        targetId: node.attributes.parentId
                                    },
                                    success: function (response) {
                                        var data = Ext.decode(response.responseText);

                                        if (data.success) {
                                            node.getOwnerTree().getRootNode().reload();

                                            Phlexible.success(data.msg);
                                        } else {
                                            Ext.MessageBox.alert('Failure', data.msg);
                                        }
                                    },
                                    scope: this
                                });
                            },
                            scope: this
                        }
                    ],
                    disabled: true
                },
                // 10
                '-',
                {
                    // 11
                    text: this.strings.delete_teaser,
                    iconCls: 'p-teaser-teaser_delete-icon',
                    hidden: true,
                    handler: function (item) {
                        var node = item.parentMenu.node;
                        Ext.MessageBox.confirm('Confirm', 'Are you sure?', function (btn, text, x, node) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('teasers_layout_delete'),
                                    params: {
                                        id: node.id,
                                        type: node.attributes.type
                                    },
                                    success: function (node) {
                                        // reload full element if current teaser is deleted
                                        if (this.element.getTeaserNode() &&
                                            this.element.getTeaserNode().id &&
                                            this.element.getTeaserNode().id == node.id) {
                                            this.element.load(node.attributes.parentIid, null, null, 1);
                                        }
                                        else {
                                            // reload layout panel only
                                            node.getOwnerTree().getRootNode().reload();
                                        }

//                                  node.parentNode.reload();
                                    }.createDelegate(this, [node], false)
                                });
                            }
                        }.createDelegate(this, [node], true));
                    },
                    scope: this
                }
            ]
        });

        this.on({
            beforeclick: function () {
                return false;
            },
            dblclick: function (node, e) {
                if (node.attributes.type == 'area' || node.attributes.type == 'layout') {
                    this.fireEvent('areaselect', node.attributes.areaId, node);
                    return false;
                }

                node.select();
                if (node.attributes.type == 'element' && !node.attributes.inherited) {
                    this.fireEvent('teaserselect', node.attributes.id, node);
                }

                e.stopEvent();

                return true;
            },
            scope: this,
            contextmenu: {
                fn: function (node, event) {
                    event.stopEvent();
                    var coords = event.getXY();

                    this.node = node;

                    var type = node.attributes.type;

                    if (type === 'area' || type === 'layout') {
                        this.items.items[0].setText('[Layoutarea]');
                        this.items.items[0].setIconClass('p-teaser-layoutarea-icon');

                        this.items.items[2].show();
                        this.items.items[3].show();
                        this.items.items[4].show();

                        this.items.items[5].hide();
                        this.items.items[6].hide();
                        this.items.items[7].show();
                        this.items.items[8].hide();
                        this.items.items[9].show();
                        this.items.items[10].hide();
                        this.items.items[11].hide();

                        if (!Phlexible.Clipboard.isInactive() && Phlexible.Clipboard.getType() === 'teaser') {
                            this.items.items[9].menu.items.items[0].setText(String.format(Phlexible.elements.Strings.paste_as, Phlexible.Clipboard.getText()));
                            this.items.items[9].enable();
                        } else {
                            this.items.items[9].disable();
                        }
                    }
                    else if (type === 'teaser' || type === 'element') {
                        this.items.items[0].setText('[Teaser]');
                        this.items.items[0].setIconClass('p-teaser-teaser-icon');

                        this.items.items[2].hide();
                        this.items.items[3].hide();
                        this.items.items[4].hide();

                        this.items.items[5].show();
                        if (node.attributes.inherit) {
                            this.items.items[5].setChecked(true);
                        } else {
                            this.items.items[5].setChecked(false);
                        }

                        this.items.items[6].show();
                        if (node.attributes.hide) {
                            this.items.items[6].setChecked(false);
                        }
                        else {
                            this.items.items[6].setChecked(true);
                        }

                        if (node.attributes.inherited) {
                            this.items.items[7].hide();
                            this.items.items[8].hide();
                            this.items.items[9].hide();
                            this.items.items[10].hide();
                            this.items.items[11].hide();
                        }
                        else {

                            this.items.items[7].show();
                            this.items.items[8].show();
                            this.items.items[9].hide();
                            this.items.items[10].show();
                            this.items.items[11].setText(Phlexible.teasers.Strings.delete_teaser);
                            this.items.items[11].setIconClass('p-teaser-teaser_delete-icon');
                            this.items.items[11].show();
                        }

                        this.items.items[5].show();
                    }
                    else if (type == 'catch') {
                        // legacy
                        this.items.items[0].setText('[Catch]');
                        this.items.items[0].setIconClass('p-teaser-catch-icon');

                        this.items.items[2].hide();
                        this.items.items[3].hide();
                        this.items.items[4].hide();

                        this.items.items[5].hide();

                        this.items.items[6].hide();
                        this.items.items[7].hide();
                        this.items.items[8].hide();
                        this.items.items[9].hide();

                        this.items.items[10].hide();
                        this.items.items[11].setText(Phlexible.teasers.Strings.delete_catch);
                        this.items.items[11].setIconClass('p-teaser-catch_delete-icon');
                        this.items.items[11].show();
                    }
                    else {
                        return;
                    }

                    if (this.element.isGranted('CREATE')) {
                        this.items.items[2].enable();
                        this.items.items[3].enable();
                        this.items.items[4].enable();
                    }
                    else {
                        this.items.items[2].disable();
                        this.items.items[3].disable();
                        this.items.items[4].disable();
                        this.items.items[9].disable();
                    }

                    if (this.element.isGranted('DELETE')) {
                        this.items.items[11].enable();
                    }
                    else {
                        this.items.items[11].disable();
                    }

                    this.showAt([coords[0], coords[1]]);
                },
                scope: this.contextMenu
            }
        });

        Phlexible.teasers.view.Tree.superclass.initComponent.call(this);
    },

    onSetLanguage: function (element, language) {
        if (element.getTreeNode().attributes.type != 'part') {
            return;
        }

        this.doLoad(element, language);
    },

    onLoadElement: function (element) {
        if (element.getTreeNode().attributes.type != 'page' && element.getTreeNode().attributes.type != 'structure') {
            return;
        }

        this.doLoad(element);
    },

    doLoad: function (element, language) {
        this.disable();

        this.loader.baseParams = {
            tid: element.getNodeId(),
            eid: element.getEid(),
            siteroot_id: element.getSiterootId(),
            language: language || element.getLanguage()
        };

        var root = new Ext.tree.AsyncTreeNode({
            text: 'Root',
            draggable: false,
            id: -1,
            cls: 'node_level_0',
            type: 'root',
            expanded: true,
            listeners: {
                load: this.enable,
                scope: this
            }
        });

        this.setRootNode(root);

        root.reload(function (node) {
            if (!node.hasChildNodes()) {
                this.collapse();
            } else {
                this.expand();
            }
        }.createDelegate(this));
    }
});

Ext.reg('teasers-tree', Phlexible.teasers.view.Tree);
