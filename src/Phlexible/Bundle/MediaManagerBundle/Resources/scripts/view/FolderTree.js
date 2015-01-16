/*
Ext.define('Phlexible.mediamanager.FolderTreeLoader', {
    extend: 'Ext.tree.TreeLoader',

    createNode: function (attr) {
        // apply baseAttrs, nice idea Corey!
        if (this.baseAttrs) {
            Ext.applyIf(attr, this.baseAttrs);
        }
        if (this.applyLoader !== false) {
            attr.loader = this;
        }
        if (typeof attr.uiProvider == 'string') {
            attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        if (typeof attr.uiProvider == 'string') {
            attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }

        return new Ext.tree.AsyncTreeNode(attr);
    },

    getParams: function (node) {
        var buf = [], bp = this.baseParams;
        for (var key in bp) {
            if (typeof bp[key] != "function") {
                buf.push(encodeURIComponent(key), "=", encodeURIComponent(bp[key]), '&');
            }
        }
        buf.push("node=", encodeURIComponent(node.id));
        if (node.attributes.site_id) {
            buf.push('&', "site_id=", encodeURIComponent(node.attributes.site_id));
        }
        if (node.attributes.slot) {
            buf.push('&', "slot=", encodeURIComponent(node.attributes.slot));
        }
        return buf.join("");
    }
});
*/

Ext.define('Phlexible.mediamanager.FolderTree', {
    extend: 'Ext.tree.TreePanel',
    alias: 'widget.mediamanager-folders',

    strings: Phlexible.mediamanager.Strings,
    cls: 'p-mediamanager-folders',
    enableDD: true,
    containerScroll: true,
    ddGroup: 'mediamanager',
    ddAppendOnly: true,
    ddScroll: true,
    rootVisible: false,
    autoScroll: true,
    useArrows: true,
    lines: false,

    /**
     * @event folderChange
     * Fires after a Folder has been selected
     * @param {Number} folderId The ID of the selected Folder.
     * @param {String} folderName The Name of the selected Folder.
     * @param {Phlexible.mediamanager.model.Folder} node The TreeNode of the selected Folder.
     */

    /**
     * @event reload
     */

    // private
    initComponent: function () {
        this.initMyStore();
        this.initMyFolderContextMenuItems();
        this.initMyContextMenus();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.TreeStore', {
            model: 'Phlexible.mediamanager.model.Folder',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('mediamanager_folder_list'),
                reader: {
                    type: 'json'
                }
            },
            root: {
                id: 'root',
                text: 'root',
                expanded: true,
                iconCls: Phlexible.Icon.get('folder')
            },
            preloadChildren: true,
            listeners: {
                load: function (loader, node) {
                    if (this.startFolderPath) {
                        this.selectPath(this.startFolderPath);
                    }
                },
                scope: this
            }
        });

        /*
         this.root.appendChild(new Ext.tree.AsyncTreeNode({
         text: this.strings.root,
         draggable: false,
         id: -1,
         expanded: true
         }));
         this.root.appendChild(new Ext.tree.AsyncTreeNode({
         text: this.strings.trash,
         draggable: false,
         id: 'trash',
         cls: 'p-trash-node',
         expanded: true
         }));
         */
    },

    initMyContextMenus: function() {
        this.folderContextMenu = new Ext.menu.Menu({
            items: this.contextMenuItems
        });
    },

    initMyListeners: function() {
        this.on({
            load: this.onLoad,
            rowcontextmenu: this.onContextMenu,
            movenode: this.onMove,
            nodedragover: function (e) {
                // target node is no site
                if (!e.target.data.volumeId) {
                    return false;
                }

                // from grid
                if (e.data.selections) {
                    if (e.data.selections[0].data.volumeId != e.target.attributes.volumeId) {
                        return false;
                    }
                }
                // from tree
                else if (e.dropNode) {
                    if (!e.dropNode.attributes.volumeId || e.dropNode.attributes.volumeId != e.target.attributes.volumeId) {
                        return false;
                    }
                }

                console.log('nodedragover ok');
                return true;
            },
            selectionchange: function (grid, selected) {
                if (!selected.length) {
                    return;
                }
                this.onClick(selected[0]);
            },
            scope: this
        });
    },

    initMyFolderContextMenuItems: function () {
        this.contextMenuItems = [
            {
                itemId: 'nameBtn',
                iconCls: Phlexible.Icon.get('folder'),
                text: '.',
                canActivate: false
            },
            '-',
            {
                itemId: 'reloadBtn',
                text: this.strings.reload,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                handler: this.onReload,
                scope: this
            },
            {
                itemId: 'expandBtn',
                text: this.strings.expand_all,
                iconCls: Phlexible.Icon.get('chevron-expand'),
                handler: this.onExpandAll,
                scope: this
            },
            {
                itemId: 'collapseBtn',
                text: this.strings.collapse_all,
                iconCls: Phlexible.Icon.get('chevron'),
                handler: this.onCollapseAll,
                scope: this
            },
            '-',
            {
                itemId: 'createBtn',
                text: this.strings.new_folder,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.showCreateFolderWindow,
                scope: this
            },
            {
                itemId: 'renameBtn',
                text: this.strings.rename_folder,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                handler: this.showRenameFolderWindow,
                scope: this
            },
            {
                itemId: 'deleteBtn',
                text: this.strings.delete_folder,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: this.showDeleteFolderWindow,
                scope: this
            },
            '-',
            {
                itemId: 'rightsBtn',
                text: this.strings.folder_rights,
                iconCls: Phlexible.Icon.get('folder-share'),
                handler: this.showRightsWindow,
                scope: this
            },
            {
                itemId: 'propertiesBtn',
                text: this.strings.properties,
                iconCls: Phlexible.Icon.get('property'),
                handler: this.showPropertiesWindow,
                scope: this
            }
        ];
    },

    onDestroy: function() {
        this.folderContextMenu.destroy();

        this.callParent(arguments);
    },

    checkRights: function (right) {
        var selection = this.getSelectionModel().getSelection();

        if (!selection.length) {
            return false;
        }

        if (selection[0].data.rights && selection[0].data.rights.indexOf(right) !== -1) {
            return true;
        }

        return false;
    },

    onLoad: function (node) {
        var selection = this.getSelectionModel().getSelection();

        if (!this.startFolderPath && !selection.length) {
            if (this.getRootNode().firstChild) {
                //this.getRootNode().firstChild.select();
            }
        }
    },

    onClick: function (node) {
        this.fireEvent('folderChange', node);
    },

    onCreateFolder: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        this.store.load({
            node: folder,
            callback: function () {
                this.onClick(folder);
            },
            scope: this
        });
    },

    onReload: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        this.store.load({
            node: folder,
            callback: function () {
                this.onClick(folder);
            },
            scope: this
        });
    },

    onExpandAll: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        folder.expand(true);
    },

    onCollapseAll: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        folder.collapse();
    },

    onMove: function (tree, node, oldParent, newParent, index) {
        var targetID = newParent.id;
        var sourceID = node.id;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_move'),
            params: {
                volumeId: node.data.volumeId,
                targetId: targetID,
                id: sourceID
            },
            method: 'post',
            success: this.onMoveSuccess.createDelegate(this, [node], true),
            scope: this
        });
    },

    onMoveSuccess: function (response, e, node) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            node.select();
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    showCreateFolderWindow: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        var w = Ext.create('Phlexible.mediamanager.CreateFolderWindow', {
            submitParams: {
                parentId: folder.id
            },
            listeners: {
                success: this.onCreateFolder,
                scope: this
            }
        });

        w.show();
    },

    showRenameFolderWindow: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        var w = Ext.create('Phlexible.mediamanager.RenameFolderWindow', {
            folderId: folder.id,
            folderName: folder.data.name,
            listeners: {
                success: function (data) {
                    folder.set('text', data.name);
                    folder.set('name', data.name);
                },
                scope: this
            }
        });

        w.show();
    },

    showDeleteFolderWindow: function () {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        Ext.MessageBox.confirm(
            'Confirm',
            'Do you really want to delete the folder "' + folder.text + '" with all files and subfolders?',
            function (btn) {
                if (btn == 'yes') {
                    this.deleteFolder(folder.data.volumeId, folder.id);
                }
            },
            this
        );
    },

    showRightsWindow: function () {
        this.showPropertiesWindow('rights');
    },

    showPropertiesWindow: function (activeTabId) {
        var nodes = this.getSelectionModel().getSelection(),
            folder;
        if (!nodes.length) {
            return;
        }
        folder = nodes[0];

        var w = Ext.create('Phlexible.mediamanager.FolderDetailWindow', {
            folderId: folder.id,
            folderName: folder.data.text,
            folderRights: folder.data.rights,
            activeTabId: activeTabId
        });
        w.show();
    },

    deleteFolder: function (volumeId, folderId) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_delete'),
            params: {
                volumeId: volumeId,
                folderId: folderId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);
                if (data.success) {
                    var parent_id = data.data.parent_id,
                        node;
                    this.root.cascade(function (n) {
                        if (n.id === parent_id) {
                            node = n;
                            return false;
                        }
                    });
                    if (!node) return;
                    node.select();
                    node.attributes.children = false;
                    //this.onClick(node);
                    node.reload();
                } else {
                    Ext.MessageBox.alert('Status', 'Delete failed: ' + data.msg);
                }
            },
            scope: this
        });
    },

    onContextMenu: function (grid, record, tr, rowIndex, event) {
        event.stopEvent();

        /*
        if (record.data.slot) {
            if (record.data.slot == 'search') {
                var cm = new Ext.menu.Menu({
                    items: [
                        {
                            text: 'Delete',
                            handler: function (btn) {
                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('extendedsearch_data_delete'),
                                    params: {
                                        id: node.data.slotId
                                    },
                                    success: function (response) {
                                        var node = this.getRootNode().findChild('slot', 'searches');
                                        if (node) {
                                            node.attributes.children = false;
                                            node.reload(function () {
                                                if (node) {
                                                    if (node.hasChildNodes()) {
                                                        node.ui.wrap.style.display = 'block';
                                                    } else {
                                                        node.ui.wrap.style.display = 'none';
                                                    }
                                                }
                                            });
                                        }
                                    },
                                    scope: this
                                });
                            },
                            scope: this
                        }
                    ]
                });

                cm.showAt([coords[0], coords[1]]);

                return;
            }

            return;
        }
        */

        var contextmenu = this.folderContextMenu;

        /*
        if (!record.isSelected()) {
            record.select();
            //this.onClick(record);
        }
        */

        contextmenu.getComponent('nameBtn').setText(record.get('text'));
        contextmenu.getComponent('nameBtn').setIconCls(Phlexible.Icon.get('folder'));

        var isRoot = record.parentNode.id == 'root';

        // collapse
        if (!record.isLeaf()) {
            contextmenu.getComponent('expandBtn').enable();
        }
        else {
            contextmenu.getComponent('expandBtn').disable();
        }

        // expand
        if (!record.isLeaf() && record.isExpanded()) {
            contextmenu.getComponent('collapseBtn').enable();
        }
        else {
            contextmenu.getComponent('collapseBtn').disable();
        }

        // rename
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_MODIFY)) {
            contextmenu.getComponent('renameBtn').disable();
        }
        else {
            contextmenu.getComponent('renameBtn').enable();
        }

        // create
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_CREATE)) {
            contextmenu.getComponent('createBtn').disable();
        }
        else {
            contextmenu.getComponent('createBtn').enable();
        }

        // delete
        var deletePolicy = Phlexible.App.getConfig().get('mediamanager.delete_policy');
        var used = record.data.used;

        if (isRoot || !this.checkRights(Phlexible.mediamanager.Rights.FOLDER_DELETE)) {
            contextmenu.getComponent('deleteBtn').disable();
        }
        else {
            if (deletePolicy == Phlexible.mediamanager.DeletePolicy.HIDE_OLD && !used) {
                contextmenu.getComponent('deleteBtn').enable();
            }
            else if (deletePolicy == Phlexible.mediamanager.DeletePolicy.DELETE_OLD && used < 4) {
                contextmenu.getComponent('deleteBtn').enable();
            }
            else if (deletePolicy == Phlexible.mediamanager.DeletePolicy.DELETE_ALL) {
                contextmenu.getComponent('deleteBtn').enable();
            }
            else {
                contextmenu.getComponent('deleteBtn').disable();
            }
        }

        // rights
        if (!this.checkRights(Phlexible.mediamanager.Rights.FOLDER_RIGHTS)) {
            contextmenu.getComponent('rightsBtn').disable();
        }
        else {
            contextmenu.getComponent('rightsBtn').enable();
        }

        contextmenu.showAt(event.getXY());
    }
});
