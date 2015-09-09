Ext.define('Phlexible.mediamanager.window.FolderDetailWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'Phlexible.mediamanager.view.FolderProperties',
        'Phlexible.mediamanager.view.FolderMeta'
    ],

    title: '_FolderDetailWindow',
    iconCls: Phlexible.Icon.get('folder'),
    width: 840,
    height: 495,
    layout: 'fit',
    cls: 'p-mediamanager-folder-detail-window',
    modal: true,
    constrainHeader: true,
    maximizable: true,
    border: false,

    activeTabId: 'properties',

    folder: null,

    pathText: '_pathText',
    idText: '_idText',
    nameText: '_nameText',

    initComponent: function () {
        if (!this.folder) {
            throw new Error('Folder ID missing.');
        }

        this.title = this.folder.get('name');

        this.initMyTabs();

        var activeTab = 0;
        if (this.activeTabId) {
            var len = this.tabs.length;
            for (var i = 0; i < len; i++) {
                if (this.tabs[i].itemId == this.activeTabId) {
                    activeTab = i;
                    break;
                }
            }
        }

        this.initMyItems(activeTab);
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function(activeTab) {
        this.items = [{
            xtype: 'tabpanel',
            deferredRender: false,
            activeTab: activeTab,
            items: this.tabs
        }];
    },

    initMyTabs: function() {
        this.tabs = [{
            xtype: 'mediamanager.folder-properties',
            itemId: 'properties',
            folder: this.folder
        },{
            xtype: 'mediamanager.folder-meta',
            itemId: 'meta',
            border: false,
            stripeRows: true,
            rights: this.folder.get('rights'),
            params: {
                folderId: this.folder.get('id')
            }
        }/*,{
            xtype: 'accesscontrol-rights',
            itemId: 'rights',
            title: this.strings.folder_rights,
            iconCls: 'p-mediamanager-folder_rights-icon',
            disabled: this.folderRights.indexOf(Phlexible.mediamanager.Rights.FOLDER_RIGHTS) === -1,
            hidden: Phlexible.User.isGranted('ROLE_MEDIA_ACCESS_CONTROL'),
            objectType: 'Phlexible\\Bundle\\MediaManagerBundle\\Entity\\Folder',
            strings: {
                users: this.strings.select_user,
                user: '_user',
                groups: this.strings.select_group,
                group: '_group'
            },
            urls: {
                identities: Phlexible.Router.generate('accesscontrol_identities'),
                add: Phlexible.Router.generate('mediamanager_rights_add')
            },
            listeners: {
                render: function (c) {
                    if (!c.disabled) c.doLoad('Phlexible\\Bundle\\MediaManagerBundle\\Entity\\Folder', this.folderId);
                },
                scope: this
            },
            createIconCls: function(permission) {
                return 'p-mediamanager-permission_' + permission.name.toLowerCase() + '-icon';
            }
        }*/];
    },


    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'tbar',
            dock: 'top',
            items: [
                this.pathText,
                {
                    xtype: 'textfield',
                    itemId: 'pathField',
                    value: this.folder.get('path'),
                    width: 200
                },
                ' ',
                this.nameText,
                {
                    xtype: 'textfield',
                    itemId: 'nameField',
                    value: this.folder.get('name'),
                    flex: 1
                },
                ' ',
                this.idText,
                {
                    xtype: 'textfield',
                    itemId: 'idField',
                    value: this.folder.get('id'),
                    width: 240
                }
            ]
        }];
    }
});
