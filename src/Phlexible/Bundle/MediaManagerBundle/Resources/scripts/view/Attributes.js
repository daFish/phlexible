Ext.define('Phlexible.mediamanager.view.Attributes', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediamanager.view.FileMeta',
        'Phlexible.mediamanager.view.FilePreview',
        'Phlexible.mediamanager.view.FileVersions',
        'Phlexible.mediamanager.view.FolderMeta'
    ],
    xtype: 'mediamanager.attributes',

    iconCls: Phlexible.Icon.get('document'),
    autoScroll: true,

    folderRights: {},
    mode: '',

    noFileSelectedText: '_noFileSelected',
    attributesText: '_attributesText',
    noAttributesText: '_noAttributesText',

    initComponent: function () {
        this.initMyTemplates();
        this.initMyAccordions();
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.setTitle(this.noFileSelectedText);

        this.items = [
            {
                xtype: 'mediamanager.file-preview',
                itemId: 'preview',
                header: false,
                border: false
            },
            {
                itemId: 'details',
                header: false,
                border: false,
                autoHeight: true,
                tpl: this.detailsTpl,
                data: {}
            },
            {
                xtype: 'panel',
                itemId: 'accordions',
                header: false,
                border: false,
                layout: {
                    type: 'accordion',
                    titleCollapse: true,
                    fill: true
                },
                items: this.accordionPanels
            }
        ];

        delete this.accordionPanels;
    },

    initMyTemplates: function() {
        this.detailsTpl = new Ext.XTemplate(
            '<div>',
            '<div style="color: grey;">{[Phlexible.mediamanager.Strings.name]}:</div>',
            '<div>{[Ext.String.ellipsis(values.name, 40)]}</div>',
            '<div style="color: grey; padding-top: 5px;">{[Phlexible.mediamanager.Strings.type]}:</div>',
            '<div>{mediaType}</div>',
            '<div style="color: grey; padding-top: 5px;">{[Phlexible.mediamanager.Strings.size]}:</div>',
            '<div>{[Phlexible.Format.size(values.size)]}</div>',
            '<div style="color: grey; padding-top: 5px;">{[Phlexible.mediamanager.Strings.created_by]}:</div>',
            '<div>{createUser}</div>',
            '<div style="color: grey; padding-top: 5px;">{[Phlexible.mediamanager.Strings.create_date]}:</div>',
            '<div>{[Phlexible.Format.date(values.createTime)]}</div>',
            '</div>'
        );
    },

    initMyAccordions: function() {
        this.accordionPanels = [{
            xtype: 'mediamanager.file-versions',
            itemId: 'versions',
            border: false,
            autoHeight: true,
            collapsed: true,
            listeners: {
                render: function (c) {
                    this.relayEvents(c, ['versionSelect', 'versionDownload']);
                },
//                versionChange: function() {
//
//                },
                scope: this
            }
        },{
            xtype: 'propertygrid',
            itemId: 'attributes',
            iconCls: Phlexible.Icon.get('property'),
            title: this.attributesText,
            emptyText: this.noAttributesText,
            source: {},
            border: false,
            autoHeight: true,
            collapsed: true
        },{
            xtype: 'mediamanager.folder-meta',
            itemId: 'folder-meta',
            border: false,
            autoHeight: true,
            collapsed: true,
            small: true
        },{
            xtype: 'mediamanager.file-meta',
            itemId: 'file-meta',
            border: false,
            //autoHeight: true,
            height: 200,
            collapsed: true,
            small: true
        },{
            xtype: 'grid',
            itemId: 'folder-usage',
            title: 'Folder used by',
            iconCls: Phlexible.Icon.get('folder-bookmark'),
            border: false,
            stripeRows: true,
            autoHeight: true,
            //autoExpandColumn: 'value',
            hidden: true,
            collapsed: true,
            store: Ext.create('Ext.data.Store', {
                fields: ['usageType', 'usageId', 'status', 'link']
            }),
            columns: [
                {
                    header: 'usage_type',
                    dataIndex: 'usageType'
                },
                {
                    header: 'usage_id',
                    dataIndex: 'usageId'
                },
                {
                    header: 'status',
                    dataIndex: 'status',
                    renderer: function (v) {
                        var out = '';
                        if (v & 8) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 4) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 2) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 1) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        return out;
                    }
                }
            ]
        },{
            xtype: 'grid',
            itemId: 'file-usage',
            title: 'File used by',
            iconCls: Phlexible.Icon.get('document-bookmark'),
            border: false,
            stripeRows: true,
            autoHeight: true,
            //autoExpandColumn: 'value',
            hidden: true,
            collapsed: true,
            store: Ext.create('Ext.data.Store', {
                fields: ['usageType', 'usageId', 'status', 'link']
            }),
            columns: [
                {
                    header: 'usage_type',
                    dataIndex: 'usageType'
                },
                {
                    header: 'usage_id',
                    dataIndex: 'usageId'
                },
                {
                    header: 'status',
                    dataIndex: 'status',
                    renderer: function (v) {
                        var out = '';
                        if (v & 8) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_green.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 4) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_yellow.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 2) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_gray.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        if (v & 1) {
                            out += '<img src="' + Phlexible.bundleAsset('/phlexiblemediamanager/images/bullet_black.gif') + '" width="8" height="12" style="vertical-align: middle;" />';
                        }
                        return out;
                    }
                }
            ]
        }];

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.accordionPanels.push({
                xtype: 'grid',
                itemId: 'file-debug',
                title: 'Debug File',
                iconCls: Phlexible.Icon.get('bug'),
                border: false,
                stripeRows: true,
                autoHeight: true,
                autoExpandColumn: 'value',
                collapsed: true,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'value']
                }),
                columns: [
                    {
                        header: 'key',
                        dataIndex: 'key',
                        width: 100
                    },
                    {
                        header: 'value',
                        dataIndex: 'value',
                        editor: 'textfield',
                        flex: 1
                    }
                ]
            });

            this.accordionPanels.push({
                xtype: 'grid',
                itemId: 'folder-debug',
                title: 'Debug Folder',
                iconCls: Phlexible.Icon.get('bug'),
                border: false,
                stripeRows: true,
                autoHeight: true,
                autoExpandColumn: 'value',
                collapsed: true,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'value']
                }),
                columns: [
                    {
                        header: 'key',
                        dataIndex: 'key',
                        width: 100
                    },
                    {
                        header: 'value',
                        dataIndex: 'value',
                        editor: 'textfield',
                        flex: 1
                    }
                ]
            });

            this.accordionPanels.push({
                xtype: 'grid',
                itemId: 'cache-debug',
                title: 'Debug Cache',
                iconCls: Phlexible.Icon.get('bug'),
                border: false,
                stripeRows: true,
                autoHeight: true,
                autoExpandColumn: 'link',
                collapsed: true,
                store: Ext.create('Ext.data.Store', {
                    fields: ['key', 'status', 'link']
                }),
                columns: [
                    {
                        header: 'key',
                        dataIndex: 'key'
                    },
                    {
                        header: 'status',
                        dataIndex: 'status',
                        width: 50
                    },
                    {
                        id: 'link',
                        header: 'link',
                        dataIndex: 'link'
                    }
                ],
                listeners: {
                    rowdblclick: function (grid, rowIndex) {
                        var r = grid.store.getAt(rowIndex);

                        window.open(r.data.link);
                    }
                }
            });
        }
    },

    getPreviewPanel: function () {
        return this.getComponent('preview');
    },

    getDetailsPanel: function () {
        return this.getComponent('details');
    },

    getAccordionPanel: function () {
        return this.getComponent('accordions');
    },

    getFileVersionsPanel: function () {
        return this.getAccordionPanel().getComponent('versions');
    },

    getFileAttributesPanel: function () {
        return this.getAccordionPanel().getComponent('attributes');
    },

    getFolderMetaPanel: function () {
        return this.getAccordionPanel().getComponent('folder-meta');
    },

    getFileMetaPanel: function () {
        return this.getAccordionPanel().getComponent('file-meta');
    },

    getFolderUsedPanel: function () {
        return this.getAccordionPanel().getComponent('folder-usage');
    },

    getFileUsedPanel: function () {
        return this.getAccordionPanel().getComponent('file-usage');
    },

    getFileDebugPanel: function () {
        return this.getAccordionPanel().getComponent('file-debug');
    },

    getFolderDebugPanel: function () {
        return this.getAccordionPanel().getComponent('folder-debug');
    },

    getCacheDebugPanel: function () {
        return this.getAccordionPanel().getComponent('cache-debug');
    },

    setFolderRights: function (folderRights) {
        this.folderRights = folderRights;

        this.getFileMetaPanel().setRights(folderRights);
        this.getFolderMetaPanel().setRights(folderRights);
    },

    loadFolderMeta: function (folderId) {
        this.getFolderMetaPanel().loadMeta({folderId: folderId});
    },

    emptyFolder: function() {
        // folder meta
        this.getFolderMetaPanel().empty();

        // folder usage
        this.getFolderUsedPanel().getStore().removeAll();

        // debug
        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.getFileDebugPanel().getStore().removeAll();
            this.getCacheDebugPanel().getStore().removeAll();
        }
    },

    loadFolder: function(folder) {
        this.setFolderRights(folder.data.rights);
        this.loadFolderMeta(folder.id);

        // folder usage
        if (folder.get('usedIn')) {
            this.getFolderUsedPanel().store.loadData(folder.get('usedIn'));
            this.getFolderUsedPanel().setTitle('Folder used by [' + this.getFolderUsedPanel().getStore().getCount() + ']');
        } else {
            this.getFolderUsedPanel().getStore().removeAll();
        }
        if (this.getFolderUsedPanel().getStore().getCount()) {
            this.getFolderUsedPanel().show();
        } else {
            this.getFolderUsedPanel().hide();
        }

        // debug
        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            var debugData = [];
            Ext.Object.each(folder.data, function(key, value) {
                debugData.push({key: key, value: value});
            });
            this.getFolderDebugPanel().getStore().loadData(debugData);
        }
    },

    emptyFile: function() {
        this.setTitle(this.noFileSelectedText);

        // preview
        this.getPreviewPanel().empty();

        // info
        this.getDetailsPanel().setData({});

        // file attributes
        this.getFileAttributesPanel().setSource({});

        // file meta
        this.getFileMetaPanel().empty();

        // file usage
        this.getFileUsedPanel().getStore().removeAll();

        // debug
        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            this.getFileDebugPanel().getStore().removeAll();
            this.getCacheDebugPanel().getStore().removeAll();
        }
    },

    loadFile: function (file) {
        this.setTitle(Ext.String.ellipsis(file.get('name'), 40));
        var documentTypeClass = Phlexible.documenttypes.DocumentTypes.getClass(file.get('mediaType')) || Phlexible.documenttypes.DocumentTypes.getClass('_unknown');
        this.setIconCls(documentTypeClass + '-small');

        this.getPreviewPanel().loadRecord(file);

        var properties = file.get('properties');
        this.fileId = file.get('id');
        this.fileVersion = file.get('version');

//        this.attributesPanel.setTitle(this.strings.attributes + ' [' + properties.attributesCnt + ']');
//        this.attributesPanel.setSource(properties.attributes);
        var details = {
            mediaType: file.get('mediaTypeTitle'),
            version: file.get('version'),
            size: file.get('size'),
            createTime: file.get('createTime'),
            createUser: file.get('createUser')
        };

        this.getDetailsPanel().setData(details);

        if (file.get('hasVersions')) {
            this.getFileVersionsPanel().loadFile(this.fileId);
        }
        else {
            this.getFileVersionsPanel().empty();
        }

        this.getFileAttributesPanel().setSource(file.get('attributes'));

        this.getFileMetaPanel().loadMeta({
            fileId: this.fileId,
            fileVersion: this.fileVersion
        });

        if (Phlexible.User.isGranted('ROLE_SUPER_ADMIN')) {
            var debugData = [];
            Ext.Object.each(file.data, function(key, value) {
                debugData.push({key: key, value: value});
            });
            this.getFileDebugPanel().store.loadData(debugData);

            var cacheData = [];
            if (file.get('cache')) {
                for (var i in file.get('cache')) {
                    cacheData.push([
                        i,
                        file.data.cache[i],
                        file.data.cache[i]
                    ]);
                }
                this.getCacheDebugPanel().getStore().loadData(cacheData);
            } else {
                this.getCacheDebugPanel().getStore().removeAll();
            }
        }

        // file usage
        if (file.get('usedIn')) {
            this.getFileUsedPanel().store.loadData(file.get('usedIn'));
            this.getFileUsedPanel().setTitle('File used by [' + this.getFileUsedPanel().getStore().getCount() + ']');
        } else {
            this.getFileUsedPanel().getStore().removeAll();
        }
        if (this.getFileUsedPanel().getStore().getCount()) {
            this.getFileUsedPanel().show();
        } else {
            this.getFileUsedPanel().hide();
        }
    },

    empty: function () {
        this.emptyFile();
        this.emptyFolder();
    }
});