Ext.define('Phlexible.mediamanager.view.Folders', {
    extend: 'Ext.tree.Panel',
    xtype: 'mediamanager.folders',

    cls: 'p-mediamanager-folders',
    enableDD: true,
    containerScroll: true,
    rootVisible: false,
    autoScroll: true,
    useArrows: true,
    lines: false,

    reloadText: '_reloadText',
    expandAllText: '_expandAllText',
    collapseAllText: '_collapseAllText',
    createFolderText: '_createFolderText',
    renameFolderText: '_renameFolderText',
    deleteFolderText: '_deleteFolderText',
    folderRightsText: '_folderRightsText',
    propertiesText: '_propertiesText',

    /**
     * @event reload
     */

    // private
    initComponent: function () {
        this.initMyView();
        this.initMyStore();
        this.initMyFolderContextMenuItems();
        this.initMyContextMenus();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyView: function() {
        this.viewConfig = {
            plugins: {
                ptype: 'treeviewdragdrop',
                containerScroll: true,
                ddGroup: 'p-mediamanager-folders',
                appendOnly: true
            },
            listeners: {
                beforedrop: this.onBeforeFileDrop,
                scope: this
            }
        };
    },

    initMyStore: function() {
        return;
        this.store.on({
            load: function (loader, node) {
                if (this.startFolderPath) {
                    this.selectPath(this.startFolderPath);
                }
            },
            scope: this
        });
    },

    initMyContextMenus: function() {
        this.folderContextMenu = Ext.create('Ext.menu.Menu', {
            items: this.contextMenuItems
        });
    },

    initMyListeners: function() {
        this.on({
            load: this.onLoad,
            rowcontextmenu: this.onContextMenu,
            beforedrop: function() {
                debugger;
            },
            drop: function() {
                debugger;
            },
            itemmove: this.onFolderMove,
            beforeitemmove: this.onBeforeFolderMove,
            selectionchange: this.onSelectionChange,
            scope: this
        });
    },

    initMyFolderContextMenuItems: function () {
        this.contextMenuItems = [
            {
                itemId: 'nameBtn',
                iconCls: Phlexible.Icon.get('folder'),
                text: '.',
                focusable: false
            },
            '-',
            {
                itemId: 'reloadBtn',
                text: this.reloadText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                handler: this.onReload,
                scope: this
            },
            {
                itemId: 'expandBtn',
                text: this.expandAllText,
                iconCls: Phlexible.Icon.get('chevron-expand'),
                handler: this.onExpandAll,
                scope: this
            },
            {
                itemId: 'collapseBtn',
                text: this.collapseAllText,
                iconCls: Phlexible.Icon.get('chevron'),
                handler: this.onCollapseAll,
                scope: this
            },
            '-',
            {
                itemId: 'createBtn',
                text: this.createFolderText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                handler: this.showCreateFolderWindow,
                scope: this
            },
            {
                itemId: 'renameBtn',
                text: this.renameFolderText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                handler: this.showRenameFolderWindow,
                scope: this
            },
            {
                itemId: 'deleteBtn',
                text: this.deleteFolderText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: this.showDeleteFolderWindow,
                scope: this
            },
            '-',
            {
                itemId: 'rightsBtn',
                text: this.folderRightsText,
                iconCls: Phlexible.Icon.get('folder-share'),
                handler: this.showRightsWindow,
                scope: this
            },
            {
                itemId: 'propertiesBtn',
                text: this.propertiesText,
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

    folderChange: function(node) {
        this.fireEvent('folderChange', node);
    },

    onSelectionChange: function (grid, selected) {
        if (!selected.length) {
            return;
        }
        this.folderChange(selected[0]);
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
                this.folderChange(folder);
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
                this.folderChange(folder);
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

    onBeforeFileDrop: function(node, data, overModel, dropPosition, dropHandlers, eOpts) {
        dropHandlers.wait = true;

        this.fireEvent('fileMove', overModel, data.records);

        return true;
    },

    onBeforeFolderMove: function (node, oldParent, newParent) {
        // target node is no site
        if (!node.data.volumeId) {
            return false;
        }
        if (!newParent.data.volumeId) {
            return false;
        }

        // from grid
        // TODO:
        /*
         if (e.data.selections) {
         if (e.data.selections[0].data.volumeId != e.target.attributes.volumeId) {
         return false;
         }
         }
         // from tree
         else if (e.dropNode) {*/
        if (node.data.volumeId != newParent.data.volumeId) {
            return false;
        }
        //}

        return true;
    },

    onFolderMove: function (node, oldParent, newParent) {
        var targetId = newParent.id;
        var folderId = node.id;

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_patch', {folderId: folderId}),
            method: 'PATCH',
            params: {
                targetId: targetId
            },
            success: this.onFolderMoveSuccess,
            scope: this
        });
    },

    onFolderMoveSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            Phlexible.Notify.success(data.msg);
        } else {
            Phlexible.Notify.failure(data.msg);
        }
    },

    showCreateFolderWindow: function () {
        var folders = this.getSelectionModel().getSelection(),
            folder;
        if (!folders.length) {
            return;
        }
        folder = folders[0];

        var w = Ext.create('Phlexible.mediamanager.window.FolderCreateWindow', {
            folder: folder,
            listeners: {
                success: this.onCreateFolder,
                scope: this
            }
        });

        w.show();
    },

    showRenameFolderWindow: function () {
        var folders = this.getSelectionModel().getSelection(),
            folder;
        if (!folders.length) {
            return;
        }
        folder = folders[0];

        var w = Ext.create('Phlexible.mediamanager.window.FolderRenameWindow', {
            folder: folder,
            listeners: {
                success: function (data) {
                },
                scope: this
            }
        });

        w.show();
    },

    showDeleteFolderWindow: function () {
        var folders = this.getSelectionModel().getSelection(),
            folder;
        if (!folders.length) {
            return;
        }
        folder = folders[0];

        Ext.MessageBox.confirm(
            'Confirm',
            'Do you really want to delete the folder "' + folder.get('name') + '" with all files and subfolders?',
            function (btn) {
                if (btn == 'yes') {
                    this.deleteFolder(folder);
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

        var w = Ext.create('Phlexible.mediamanager.window.FolderDetailWindow', {
            folder: folder,
            activeTabId: activeTabId
        });
        w.show();
    },

    deleteFolder: function (folder) {
        this.getSelectionModel().select(folder.parentNode);
        folder.drop();
    },

    onContextMenu: function (grid, record, tr, rowIndex, event) {
        event.stopEvent();

        var contextmenu = this.folderContextMenu;

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
        var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');
        var usageStatus = record.data.usageStatus;

        if (isRoot || !this.checkRights(Phlexible.mediamanager.Rights.FOLDER_DELETE)) {
            contextmenu.getComponent('deleteBtn').disable();
        }
        else {
            if (deletePolicy == Phlexible.mediamanager.DeletePolicy.HIDE_OLD && !usageStatus) {
                contextmenu.getComponent('deleteBtn').enable();
            }
            else if (deletePolicy == Phlexible.mediamanager.DeletePolicy.DELETE_OLD && usageStatus < 4) {
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
