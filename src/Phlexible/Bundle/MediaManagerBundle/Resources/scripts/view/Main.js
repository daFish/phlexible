Ext.define('Phlexible.mediamanager.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediamanager.toolbar.UploadStatus',
        'Phlexible.mediamanager.view.Files',
        'Phlexible.mediamanager.view.Folders',
        'Phlexible.mediamanager.view.Attributes'
    ],
    xtype: 'mediamanager.main',

    iconCls: Phlexible.Icon.get('images'),
    layout: 'border',
    closable: true,
    cls: 'p-mediamanager-main',
    border: false,

    mode: '',
    params: {},
    hideFilter: false,

    createFolderText: '_createFolderText',
    uploadText: '_uploadText',
    downloadText: '_downloadText',
    downloadFolderText: '_downloadFolderText',
    downloadFilesText: '_downloadFilesText',
    viewsText: '_viewsText',
    viewExtralargeText: '_viewExtralargeText',
    viewLargeText: '_viewLargeText',
    viewMediumText: '_viewMediumText',
    viewSmallText: '_viewSmallText',
    viewTilesText: '_viewTilesText',
    viewDetailsText: '_viewDetailsText',
    showHiddenFiles: '_showHiddenFiles',
    filterText: '_filterText',
    filterNoneText: '_filterNoneText',
    filterByUserText: '_filterByUser',
    filterCreatedByMeText: '_filterCreatedByMeText',
    filterModifiedByMeText: '_filterModifiedByMeText',
    filterCreatedByOtherText: '_filterCreatedByOtherText',
    filterModifiedByOther: '_filterModifiedByOther',
    filterByAgeCreateText: '_filterByAgeCreateText',
    filterAgeOneDayText: '_filterAgeOneDayText',
    filterAgeTwoDaysText: '_filterAgeTwoDaysText',
    filterAgeOneWeekText: '_filterAgeOneWeekText',
    filterAgeOneMonthText: '_filterAgeOneMonthText',
    filterAgeSixMonthText: '_filterAgeSixMonthText',
    filterByAgeModifiedText: '_filterByAgeModifiedText',
    filterByTypeText: '_filterByTypeText',
    filterTypeImageText: '_filterTypeImageText',
    filterTypeVideoText: '_filterTypeVideoText',
    filterTypeAudioText: '_filterTypeAudioText',
    filterTypeFlashText: '_filterTypeFlash',
    filterTypeDocumentText: '_filterTypeDocumentText',
    filterTypeArchiveText: '_filterTypeArchiveText',
    filterTypeOtherText: '_filterTypeOtherText',

    loadParams: function (params) {
        if (params.startFolderPath) {
            if (params.startFileId) {
                this.getFilesGrid().startFileId = params.startFileId;
            }
            if (params.startFolderPath.substr(0, 5) !== '/root') {
                params.startFolderPath = '/root' + params.startFolderPath;
            }

            var folder = this.getFolderTree().getSelectionModel().getSelection()[0];
            if (!folder || folder.getPath() !== params.startFolderPath) {
                this.getFolderTree().selectPath(params.startFolderPath);
            } else if (params.startFolderId) {
                var i = this.getFilesGrid().getStore().find('id', params.startFileId);
                if (i !== false) {
                    this.getFilesGrid().getSelectionModel().selectRow([i]);
                }
                this.getFilesGrid().startFileId = params.startFileId;
            }
        }
    },

    // private
    initComponent: function () {
        if (!this.noTitle) {
            this.header = false;
            //title = this.mediaText;
        }

        if (this.params.startFolderPath) {
            if (this.params.startFolderPath.substr(0, 5) !== '/root') {
                this.params.startFolderPath = '/root' + this.params.startFolderPath;
            }
        }

        if (this.params.mediaCategory || this.params.mediaTypes) {
            this.hideFilter = true;
        }

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'mediamanager.folders',
            itemId: 'folders',
            region: 'west',
            margin: '5 0 5 5',
            width: 200,
            start_folder_path: this.params.start_folder_path || false,
            split: true,
            minWidth: 100,
            maxWidth: 400,
            listeners: {
                render: function (c) {
                    //this.getLocationBar().setStore(c.getStore());
                    //this.getLocationBar().updateSelection(c.getStore().getRoot().childNodes[0]);
                },
                reload: this.onReload,
                folderChange: this.onFolderChange,
                fileMove: this.onMoveFile,
                scope: this
            }
        },{
            xtype: 'mediamanager.files',
            itemId: 'files',
            region: 'center',
            margin: '5 5 5 0',
            border: true,
            viewMode: this.params.file_view || false,
            mediaCategory: this.params.mediaCategory || false,
            mediaTypes: this.params.mediaTypes || false,
            startFileId: this.params.startFileId || false,
            listeners: {
                fileChange: this.onFileChange,
                rowdblclick: this.onFileDblClick,
                filterChange: this.onFilterChange,
                downloadFiles: this.onDownloadFiles,
                downloadFile: this.onDownloadFile,
                render: this.initUploader,
                scope: this
            }
        },{
            xtype: 'mediamanager.attributes',
            itemId: 'attributes',
            region: 'east',
            margin: '5 5 5 0',
            width: 290,
            collapsible: true,
            collapsed: this.params.hide_properties || false,
            mode: this.mode,
            listeners: {
                versionSelect: function (file, fileVersion) {
                    if (this.mode == 'select') {
                        this.fireEvent('fileSelect', file, fileVersion);
                    }
                    else {
                        var w = Ext.create('Phlexible.mediamanager.window.FileDetailWindow', {
                            file: file,
                            folderRights: this.folderRights
                        });
                        w.show();
                    }
                },
                versionDownload: this.onDownloadFile,
                scope: this
            }
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            itemId: 'location',
            dock: 'top',
            items: [{
                xtype: 'breadcrumb',
                itemId: 'breadcrumb',
                dock: 'top',
                //height: 28,
                showIcons: true,
                showMenuIcons: true,
                useSplitButtons: false,
                //border: false,
                //stopNodeId: 'root',
                //noHome: true
                //handler: function(node) {
                //    node.select();
                //    node.getOwnerTree().onClick(node);
                //}
                listeners: {
                    selectionchange: function(c, node) {
                        if (node) {
                            this.onFolderChange(node);
                        }
                    },
                    scope: this
                }
            }]
        },{
            xtype: 'toolbar',
            itemId: 'tbar',
            dock: 'top',
            items: [
                {
                    // 0
                    text: this.createFolderText,
                    itemId: 'createFolderBtn',
                    iconCls: Phlexible.Icon.get('folder--plus'),
                    handler: this.onNewFolder,
                    scope: this
                },
                ' ',
                // 2
                {
                    text: this.uploadText,
                    itemId: 'uploadBtn',
                    iconCls: Phlexible.Icon.get('drive-upload')
                },
                ' ',
                {
                    // 4
                    text: this.downloadText,
                    itemId: 'downloadBtn',
                    iconCls: Phlexible.Icon.get('drive-download'),
                    menu: [
                        {
                            text: this.downloadFolderText,
                            iconCls: Phlexible.Icon.get('folder'),
                            handler: this.onDownloadFolder,
                            scope: this
                        },
                        {
                            text: this.downloadFilesText,
                            iconCls: Phlexible.Icon.get('documents'),
                            handler: this.onDownloadFiles,
                            scope: this
                        }
                    ]
                },
                '->',
                {
                    // 6
                    xtype: 'splitbutton',
                    itemId: 'viewBtn',
                    text: this.viewsText,
                    iconCls: Phlexible.Icon.get('application-tile'),
                    handler: this.nextViewMode,
                    scope: this,
                    menu: [
                        {
                            text: this.viewExtralargeText,
                            iconCls: Phlexible.Icon.get('application-icon-large'),
                            itemId: 'extra',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.viewLargeText,
                            iconCls: Phlexible.Icon.get('application-icon-large'),
                            itemId: 'large',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.viewMediumText,
                            iconCls: Phlexible.Icon.get('application-icon'),
                            itemId: 'medium',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.viewSmallText,
                            iconCls: Phlexible.Icon.get('application-icon'),
                            itemId: 'small',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.viewTilesText,
                            iconCls: Phlexible.Icon.get('application-tile'),
                            itemId: 'tile',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.viewDetailsText,
                            iconCls: Phlexible.Icon.get('application-list'),
                            itemId: 'list',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        '-',
                        {
                            xtype: 'checkbox',
                            text: this.showHiddenFiles,
                            checked: false,
                            handler: function () {
                                this.getFilesGrid().getStore().baseParams.show_hidden = !this.getFilesGrid().getStore().baseParams.show_hidden ? 1 : 0;
                                this.getFilesGrid().getStore().reload();
                            },
                            scope: this
                        }
                    ]
                },
                ' ',
                {
                    // 8
                    xtype: 'button',
                    itemId: 'filterBtn',
                    text: this.filterText,
                    iconCls: Phlexible.Icon.get('funnel'),
                    hidden: this.hideFilter,
                    menu: [
                        {
                            text: this.filterNoneText,
                            iconCls: Phlexible.Icon.get('funnel'),
                            handler: function () {
                                this.getFilesGrid().clearFilter();
                            },
                            scope: this
                        },
                        '-',
                        {
                            text: this.filterByUserText,
                            iconCls: Phlexible.Icon.get('user'),
                            menu: [
                                {
                                    text: this.filterCreatedByMeText,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('createUserId', Phlexible.User.getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterModifiedByMeText,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('modifyUserId', Phlexible.User.getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterCreatedByOtherText,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('notCreateUserId', Phlexible.User.getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterModifiedByOther,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('notModifyUserId', Phlexible.User.getId());
                                    },
                                    scope: this
                                }
                            ]
                        },
                        {
                            text: this.filterByAgeCreateText,
                            iconCls: Phlexible.Icon.get('clock'),
                            menu: [
                                {
                                    text: this.filterAgeOneDayText,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1day');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeTwoDaysText,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '2days');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeOneWeekText,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1week');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeOneMonthText,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1month');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeSixMonthText,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '6months');
                                    },
                                    scope: this
                                }
                            ]
                        },
                        {
                            text: this.filterByAgeModifiedText,
                            iconCls: Phlexible.Icon.get('clock'),
                            menu: [
                                {
                                    text: this.filterAgeOneDayText,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1day');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeTwoDaysText,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '2days');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeOneWeekText,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1week');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeOneMonthText,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1month');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterAgeSixMonthText,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '6months');
                                    },
                                    scope: this
                                }
                            ]
                        },
                        {
                            text: this.filterByTypeText,
                            iconCls: Phlexible.Icon.get('document'),
                            menu: [
                                {
                                    text: this.filterTypeImageText,
                                    iconCls: Phlexible.Icon.get('image'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.IMAGE);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeVideoText,
                                    iconCls: Phlexible.Icon.get('film'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.VIDEO);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeAudioText,
                                    iconCls: Phlexible.Icon.get('music'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.AUDIO);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeFlashText,
                                    iconCls: Phlexible.Icon.get('document-flash-movie'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.FLASH);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeDocumentText,
                                    iconCls: Phlexible.Icon.get('document'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.DOCUMENT);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeArchiveText,
                                    iconCls: Phlexible.Icon.get('document-zipper'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.ARCHIVE);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.filterTypeOtherText,
                                    iconCls: Phlexible.Icon.get('document'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.OTHER);
                                    },
                                    scope: this
                                }
                            ]
                        }
                    ]
                }
            ]
        },{
            xtype: 'mediamanager.upload-status',
            itemId: 'updatestatus',
            dock: 'bottom'
        }];
    },

    getLocationBar: function () {
        return this.getDockedComponent('location').getComponent('breadcrumb');
    },

    getFolderTree: function () {
        return this.getComponent('folders');
    },

    getAttributesPanel: function () {
        return this.getComponent('attributes');
    },

    getFilesGrid: function () {
        return this.getComponent('files');
    },

    getStatusBar: function () {
        return this.getDockedComponent('updatestatus');
    },

    updateViewMode: function(btn) {
        this.getDockedComponent('tbar').getComponent('viewBtn').setIconCls(btn.iconCls);
        this.getDockedComponent('tbar').getComponent('viewBtn').setText(btn.text);
        this.getFilesGrid().features[0].setViewMode(btn.itemId);
    },

    nextViewMode: function(btn) {
        var currentViewMode = this.getFilesGrid().features[0].getViewMode();

        if (!currentViewMode || currentViewMode === 'list') {
            this.updateViewMode(btn.getMenu().getComponent('extra'));
        } else if (currentViewMode === 'extra') {
            this.updateViewMode(btn.getMenu().getComponent('large'));
        } else if (currentViewMode === 'large') {
            this.updateViewMode(btn.getMenu().getComponent('medium'));
        } else if (currentViewMode === 'medium') {
            this.updateViewMode(btn.getMenu().getComponent('small'));
        } else if (currentViewMode === 'small') {
            this.updateViewMode(btn.getMenu().getComponent('tile'));
        } else if (currentViewMode === 'tile') {
            this.updateViewMode(btn.getMenu().getComponent('list'));
        } else {
            this.updateViewMode(btn.getMenu().getComponent('list'));
        }
    },

    createDropper: function (c) {
        var div = document.createElement('div');
        div.style.position = 'absolute';
        div.style.left = '10px';
        div.style.right = '10px';
        div.style.bottom = '10px';
        div.style.height = '30px';
        div.style.border = '2px dashed lightgrey';
        div.style.textAlign = 'center';
        div.style.verticalAlign = 'center';
        div.style.lineHeight = '30px';
        div.style.backgroundColor = '#f3f3f3';
        div.style.color = 'gray';
        div.style.opacity = 0.8;
        div.style.padding = '10px';
        div.id = 'dropper';
        var text = document.createTextNode('Drop files here for quick upload');
        div.appendChild(text);
        this.dropper = c.body.dom.appendChild(div);

        plupload.addEvent(div, 'dragenter', function (e) {
            div.style.borderColor = 'lightblue';
        });
        plupload.addEvent(div, 'dragleave', function (e) {
            div.style.borderColor = 'lightgrey';
        });
        plupload.addEvent(div, 'drop', function (e) {
            div.style.borderColor = 'lightgrey';
        });
        plupload.addEvent(c.body.dom, 'drop', function (e) {
            e.preventDefault();
        });

        return div;
    },

    initUploader: function () {
        //if (Phlexible.config.mediamanager.upload.disable_flash) {
        //    return;
        //}
        var sessionID = 'abc';//Phlexible.Cookie.get('phlexible'); // TODO
        if (!sessionID) {
            Phlexible.console.warn("No session ID, upload via flash _will_ fail!");
        }

        var addBtn = this.getDockedComponent('tbar').getComponent('uploadBtn');
        /*
        var btn = addBtn.el.child('button');
        var suoID = Ext.id();
        var p = btn.parent();
        var em = p.createChild({
            tag: 'em',
            style: {
                position: 'relative',
                display: 'block'
            }
        });
        addBtn.el.child('button').appendTo(em);
        em.createChild({
            tag: 'div',
            id: suoID,
            style: 'display: block; position: absolute; top: 0pt; left: 0pt;'
        });
        */

        var dropper = this.createDropper(this.getFilesGrid());

        var uploader = new plupload.Uploader({
            runtimes: 'html5,flash,silverlight,html4',
            file_data_name: 'Filedata',
            browse_button: addBtn.id,
            //container: 'container',
            filters: {
                max_file_size: '2000mb'
            },
            url: Phlexible.Router.generate('mediamanager_upload'),
            flash_swf_url: Phlexible.bundleAsset('/phlexiblemediamanager/plupload/Moxie.swf'),
            silverlight_xap_url: Phlexible.bundleAsset('/phlexiblemediamanager/plupload/Moxie.xap'),
            drop_element: dropper,
            multipart: true,
            multipart_params: {
            }
        });

        uploader.bind('Init', function (up, params) {
            Phlexible.console.info('uploader::Init', 'runtime:' + params.runtime, 'features:', up.features, 'caps:', up.caps);

            if (!up.features.dragdrop) {
                dropper.style.visibility = 'hidden';
            }

            if (params.runtime === 'flash') {
                up.params.multipart_params.sid = sessionID;
            }
        }, this);

        uploader.bind('FilesAdded', function (up, files) {
            up.refresh(); // Reposition Flash/Silverlight

            up.settings.multipart_params.folderId = this.folderId;
        }, this);

        uploader.bind('QueueChanged', function (up) {
            Phlexible.console.log('uploader::QueueChanged');
            if (up.state == plupload.STOPPED) {
                up.start();
            }
        }, this);

        uploader.bind('Error', function (up, err) {
            up.refresh(); // Reposition Flash/Silverlight
            this.getFilesGrid().getStore().reload();
        }, this);

        uploader.bind('ChunkUploaded', function (up, file, info) {
            Phlexible.console.log('uploader::ChunkUploaded', 'id:' + file.id, 'info:', info);
        }, this);

        uploader.bind('FileUploaded', function (up, file, info) {
            this.onUploadComplete();
        }, this);

        this.getStatusBar().bindUploader(uploader);

        uploader.init();

        window.up = uploader;
    },

    onReload: function () {
        this.getFolderTree().getRoot().reload();
        this.getFilesGrid().getStore().reload();
    },

    onFolderChange: function (folder) {
        this.folderId = folder.id;
        this.volumeId = folder.data.volumeId;

        if (folder.id == 'root') {
            return;
        }

        var tbar = this.getDockedComponent('tbar');
        //this.locationBar.setNode(folder);
        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FOLDER_CREATE)) {
            tbar.getComponent('createFolderBtn').enable();
        } else {
            tbar.getComponent('createFolderBtn').disable();
        }
        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_CREATE)) {
            tbar.getComponent('uploadBtn').enable();
        } else {
            tbar.getComponent('uploadBtn').disable();
        }

        if (folder.data.versions) {
            this.getAttributesPanel().getComponent(2).getComponent(0).show();
        } else {
            this.getAttributesPanel().getComponent(2).getComponent(0).hide();
        }

        if (this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD)) {
            tbar.getComponent('downloadBtn').enable();
            tbar.getComponent('downloadBtn').menu.items.get(1).disable();
        } else {
            tbar.getComponent('downloadBtn').disable();
        }

//        if((!this.getFolderTree().checkRights('folder_modify') || !this.getFolderTree().checkRights('file_modify')) && (!this.getFolderTree().checkRights('folder_create') || !this.getFolderTree().checkRights('file_create'))) {
//            this.getComponent(1).getTopToolbar().items.get(8).disable();
//        } else {
//            this.getComponent(1).getTopToolbar().items.get(8).enable();
//
//            if(this.getFolderTree().checkRights('folder_modify') && this.getFolderTree().checkRights('file_modify')) {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(0).enable();
//            } else {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(0).disable();
//            }
//
//            if(this.getFolderTree().checkRights('folder_create') && this.getFolderTree().checkRights('file_create')) {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(1).enable();
//            } else {
//                this.getComponent(1).getTopToolbar().items.get(8).menu.items.get(1).disable();
//            }
//        }

        this.getFilesGrid().loadFolder(folder.data.volumeId, folder.id, folder.data.text, folder.data.rights);

        this.getAttributesPanel().empty();
        this.getAttributesPanel().loadFolder(folder);

        Phlexible.mediamanager.lastParams = {
            startFolderPath: folder.getPath()
        };
    },

    onFileChange: function (grid, selection) {
        if (selection.length >= 1 && this.getFolderTree().checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD)) {
            this.getAttributesPanel().loadFile(selection[0]);
            this.getDockedComponent('tbar').getComponent('downloadBtn').menu.items.get(1).enable();
        } else {
            this.getDockedComponent('tbar').getComponent('downloadBtn').menu.items.get(1).disable();
        }
    },

    onFileDblClick: function (grid, file) {
        if (this.mode == 'select') {
            this.fireEvent('fileSelect', file);
        } else {
            var w = Ext.create('Phlexible.mediamanager.window.FileDetailWindow', {
                file: file,
                folderRights: grid.folderRights
            });

            w.show();
        }
    },

    onFilterChange: function (grid, filters) {
        return;

        var s;
        switch (key) {
            case 'create_user_id':
                s = 'mine';
                break;

            case 'modify_user_id':
                s = 'mine_modified';
                break;

            case 'filterTimeCreated':
                s = 'age';
                break;

            case 'filterTimeModified':
                s = 'age_modified';
                break;

            case 'mediaCategory':
                s = value.toLowerCase();
                break;

            default:
                s = 'no';
        }
        this.getDockedComponent('tbar').getComponent('filterBtn').setIconCls('p-mediamanager-filter_' + s + '-icon');
    },

    onNewFolder: function () {
        this.getFolderTree().showCreateFolderWindow();
    },

    onMoveFile: function (folder, files) {
        var fileIds = [];
        Ext.each(files, function(file) {
            fileIds.push(file.get('id'));
        });

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_move'),
            method: 'POST',
            params: {
                folderId: folder.get('id'),
                fileId: fileIds.join(',')
            },
            success: this.onMoveFileSuccess,
            scope: this
        });
    },

    onMoveFileSuccess: function (response) {
        var data = Ext.decode(response.responseText);

        if (data.success) {
            this.getFilesGrid().getStore().reload();
            if (data.data.length) {
                var msg = "The following file(s) have not been moved, since an identical file already exists in the target folder:<br /><br />";
                for (var i = 0; i < data.data.length; i++) {
                    msg += '- ' + data.data[i] + "<br />";
                }
                Ext.Msg.alert('Warning', msg);
            }
        } else {
            Ext.Msg.alert('Failure', data.msg);
        }
    },

    onUploadComplete: function () {
        this.reloadFilesSortedLatest();
        if (!this.uploadChecker) {
            this.uploadChecker = Ext.create('Phlexible.mediamanager.util.UploadChecker', {
                listeners: {
                    reload: function() {
                        this.reloadFilesSortedLatest();
                    },
                    scope: this
                }
            });
        }
        this.uploadChecker.check();
    },

    reloadFilesSortedLatest: function() {
        var store = this.getFilesGrid().getStore();
        if (Phlexible.Config.get('mediamanager.upload.enable_upload_sort')) {
            if (!store.lastOptions) store.lastOptions = {};
            if (!store.lastOptions.params) store.lastOptions.params = {};
            store.lastOptions.params.start = 0;
            var sort = store.getSortState();
            if (sort.field != 'create_time' || sort.direction != 'DESC') {
                store.sort('create_time', 'DESC');
            }
            else {
                store.reload();
            }
        }
        else {
            store.reload();
        }

    },

    onSearch: function (search_values) {
        this.getFilesGrid().loadSearch(search_values);
    },

    onDownloadFolder: function () {
        var folder = this.getFolderTree().getSelectionModel().getSelection()[0];
        var folderId = folder.id;
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_download_folder_zip'),
            params: {
                folderId: folderId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success && data.data.filename) {
                    document.location.href = Phlexible.Router.generate('mediamanager_download_zip', {filename: data.data.filename});
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    onDownloadFiles: function () {
        var selections = this.getFilesGrid().getSelectionModel().getSelections();
        var fileIds = [];
        for (var i = 0; i < selections.length; i++) {
            fileIds.push(selections[i].data.id);
        }
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_download_file_zip'),
            params: {
                data: Ext.encode(fileIds)
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success && data.data.filename) {
                    document.location.href = Phlexible.Router.generate('mediamanager_download_zip', {filename: data.data.filename});
                } else {
                    Ext.MessageBox.alert('Failure', data.msg);
                }
            },
            scope: this
        });
    },

    onDownloadFile: function (fileId, fileVersion) {
        if (!fileId) {
            fileId = this.getFilesGrid().getSelectionModel().getSelected().data.id;
        }

        var href = Phlexible.Router.generate('mediamanager_download_file', {id: fileId});

        if (fileVersion) {
            href += '/' + fileVersion;
        }

        document.location.href = href;
    }
});
