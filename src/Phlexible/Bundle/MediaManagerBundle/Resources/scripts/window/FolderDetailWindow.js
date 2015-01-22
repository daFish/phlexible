Ext.define('Phlexible.mediamanager.window.FolderDetailWindow', {
    extend: 'Ext.window.Window',

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
            xtype: 'mediamanager-folder-properties',
            itemId: 'properties',
            folder: this.folder
        },{
            xtype: 'mediamanager-folder-meta',
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
            hidden: Phlexible.App.isGranted('ROLE_MEDIA_ACCESS_CONTROL'),
            rightType: 'internal',
            contentType: 'folder',
            strings: {
                users: this.strings.select_user,
                user: '_user',
                groups: this.strings.select_group,
                group: '_group'
            },
            urls: {
                subjects: Phlexible.Router.generate('mediamanager_rights_subjects'),
                add: Phlexible.Router.generate('mediamanager_rights_add')
            },
            listeners: {
                render: function (c) {
                    if (!c.disabled) c.doLoad('folder', this.folderId);
                },
                scope: this
            }
        }*/];
    }
});
