Ext.define('Phlexible.mediamanager.MediamanagerPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediamanager-main',

    layout: 'border',
    closable: true,
    cls: 'p-mediamanager-main',
    iconCls: Phlexible.Icon.get('images'),
    strings: Phlexible.mediamanager.Strings,
    border: false,

    mode: '',
    params: {},
    hideFilter: false,

    dndFormInput: false,

    loadParams: function (params) {
        if (params.start_folder_path) {
            if (params.start_file_id) {
                this.getFilesGrid().start_file_id = params.start_file_id;
            }
            if (params.start_folder_path.substr(0, 5) !== '/root') {
                params.start_folder_path = '/root' + params.start_folder_path;
            }

            var folder = this.getFolderTree().getSelectionModel().getSelection()[0];
            if (!folder || folder.getPath() !== params.start_folder_path) {
                this.getFolderTree().selectPath(params.start_folder_path);
            } else if (params.start_file_id) {
                var i = this.getFilesGrid().getStore().find('id', params.start_file_id);
                if (i !== false) {
                    this.getFilesGrid().getSelectionModel().selectRow([i]);
                }
                this.getFilesGrid().start_file_id = params.start_file_id;
            }
        }
    },

    // private
    initComponent: function () {
        if (!this.noTitle) {
            this.title = this.strings.media;
        }

        if (this.params.start_folder_path) {
            if (this.params.start_folder_path.substr(0, 5) !== '/root') {
                this.params.start_folder_path = '/root' + this.params.start_folder_path;
            }
        }

        if (this.params.assetType || this.params.documenttypes) {
            this.hideFilter = true;
        }

        /*
         this.searchPanel = new Phlexible.mediamanager.FilesSearchPanel({
         height: 200,
         collapsible: true,
         collapsed: true,
         border: true,
         bodyStyle: 'padding: 3px;',
         listeners: {
         xsearch: {
         fn: this.onSearch,
         scope: this
         }
         }
         });
         */

        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'mediamanager-folders',
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
                nodedragover: function (e) {
                    if (e.target.id == 'root') {
                        // root node can not be dragged
                        return false;
                    }
                    if (e.data.node) {
                        // tree -> tree move (folder -> folder)
                        if (e.target.id == 'trash') {
                            console.warn('id is trash')
                            return false;
                        }
                        if (e.target.id == e.data.node.parentNode.id) {
                            console.log(e.target.id == e.data.node.parentNode.id, e.target.id, e.data.node.parentNode.id, e.target, e.data.node.parentNode);
                            return false;
                        }
                    }
                    else {
                        // list -> tree move (file -> folder)
                        var selections = e.data.selections;

                        for (var i = 0; i < selections.length; i++) {
                            if (selections[i].data.folder_id == e.target.id) {
                                return false;
                            }
                        }
                    }
                    return true;
                },
                beforenodedrop: this.onMove,
                scope: this
            }
        },{
            xtype: 'mediamanager-files',
            itemId: 'files',
            region: 'center',
            margin: '5 5 5 0',
            border: true,
            viewMode: this.params.file_view || false,
            assetType: this.params.assetType || false,
            documenttypes: this.params.documenttypes || false,
            start_file_id: this.params.start_file_id || false,
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
            xtype: 'mediamanager-attributes',
            itemId: 'attributes',
            region: 'east',
            margin: '5 5 5 0',
            width: 290,
            collapsible: true,
            collapsed: this.params.hide_properties || false,
            mode: this.mode,
            listeners: {
                versionSelect: function (file_id, file_version, file_name, folder_id, document_type_key, asset_type) {
                    if (this.mode == 'select') {
                        this.fireEvent('fileSelect', file_id, file_version, file_name, folder_id);
                    }
                    else {
                        var w = new Phlexible.mediamanager.FileDetailWindow({
                            iconCls: document_type_key,
                            file_id: file_id,
                            file_version: file_version,
                            file_name: file_name,
                            document_type_key: document_type_key,
                            asset_type: asset_type
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
                    text: this.strings.new_folder,
                    itemId: 'createFolderBtn',
                    iconCls: Phlexible.Icon.get('folder--plus'),
                    handler: this.onNewFolder,
                    scope: this
                },
                ' ',
                // 2
                {
                    text: this.strings.upload_files,
                    itemId: 'uploadBtn',
                    iconCls: Phlexible.Icon.get('drive-upload')
                },
                ' ',
                {
                    // 4
                    text: this.strings.download,
                    itemId: 'downloadBtn',
                    iconCls: Phlexible.Icon.get('drive-download'),
                    menu: [
                        {
                            text: this.strings.download_folder,
                            iconCls: Phlexible.Icon.get('folder'),
                            handler: this.onDownloadFolder,
                            scope: this
                        },
                        {
                            text: this.strings.download_files,
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
                    text: this.strings.views,
                    iconCls: Phlexible.Icon.get('application-tile'),
                    handler: this.nextViewMode,
                    scope: this,
                    menu: [
                        {
                            text: this.strings.view_extralarge,
                            iconCls: Phlexible.Icon.get('application-icon-large'),
                            itemId: 'extra',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.strings.view_large,
                            iconCls: Phlexible.Icon.get('application-icon-large'),
                            itemId: 'large',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.strings.view_medium,
                            iconCls: Phlexible.Icon.get('application-icon'),
                            itemId: 'medium',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.strings.view_small,
                            iconCls: Phlexible.Icon.get('application-icon'),
                            itemId: 'small',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.strings.view_tiles,
                            iconCls: Phlexible.Icon.get('application-tile'),
                            itemId: 'tile',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        {
                            text: this.strings.view_details,
                            iconCls: Phlexible.Icon.get('application-list'),
                            itemId: 'list',
                            handler: this.updateViewMode,
                            scope: this
                        },
                        '-',
                        {
                            xtype: 'checkbox',
                            text: this.strings.show_hidden_files,
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
                    text: this.strings.filters,
                    iconCls: Phlexible.Icon.get('funnel'),
                    hidden: this.hideFilter,
                    menu: [
                        {
                            text: this.strings.filter_no,
                            iconCls: Phlexible.Icon.get('funnel'),
                            handler: function () {
                                this.getFilesGrid().clearFilter();
                            },
                            scope: this
                        },
                        '-',
                        {
                            text: this.strings.filter_by_user,
                            iconCls: Phlexible.Icon.get('user'),
                            menu: [
                                {
                                    text: this.strings.filter_my_created,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('createUserId', Phlexible.App.getUser().getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_my_modified,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('modifyUserId', Phlexible.App.getUser().getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_other_created,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('notCreateUserId', Phlexible.App.getUser().getId());
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_other_modified,
                                    checked: false,
                                    group: 'user',
                                    handler: function () {
                                        this.getFilesGrid().setFilter('notModifyUserId', Phlexible.App.getUser().getId());
                                    },
                                    scope: this
                                }
                            ]
                        },
                        {
                            text: this.strings.filter_by_age_created,
                            iconCls: Phlexible.Icon.get('clock'),
                            menu: [
                                {
                                    text: this.strings.filter_age_one_day,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1day');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_two_days,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '2days');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_one_week,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1week');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_one_month,
                                    checked: false,
                                    group: 'ageCreated',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeCreated', '1month');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_six_months,
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
                            text: this.strings.filter_by_age_modified,
                            iconCls: Phlexible.Icon.get('clock'),
                            menu: [
                                {
                                    text: this.strings.filter_age_one_day,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1day');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_two_days,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '2days');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_one_week,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1week');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_one_month,
                                    checked: false,
                                    group: 'ageModified',
                                    handler: function () {
                                        this.getFilesGrid().setTimeFilter('timeModified', '1month');
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_age_six_months,
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
                            text: this.strings.filter_by_type,
                            iconCls: Phlexible.Icon.get('document'),
                            menu: [
                                {
                                    text: this.strings.filter_type_image,
                                    iconCls: Phlexible.Icon.get('image'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.IMAGE);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_video,
                                    iconCls: Phlexible.Icon.get('film'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.VIDEO);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_audio,
                                    iconCls: Phlexible.Icon.get('music'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.AUDIO);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_flash,
                                    iconCls: Phlexible.Icon.get('document-flash-movie'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.FLASH);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_document,
                                    iconCls: Phlexible.Icon.get('document'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.DOCUMENT);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_archive,
                                    iconCls: Phlexible.Icon.get('document-zipper'),
                                    checked: false,
                                    group: 'type',
                                    handler: function () {
                                        this.getFilesGrid().setAssetTypeFilter(Phlexible.mediamanager.ARCHIVE);
                                    },
                                    scope: this
                                },
                                {
                                    text: this.strings.filter_type_other,
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
            xtype: 'mediamanager-upload-statusbar',
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
            Phlexible.console.debug('uploader::Init', 'runtime:' + params.runtime, 'features:', up.features, 'caps:', up.caps);

            if (!up.features.dragdrop) {
                dropper.style.visibility = 'hidden';
            }

            if (params.runtime === 'flash') {
                up.params.multipart_params.sid = sessionID;
            }
        }, this);

        uploader.bind('FilesAdded', function (up, files) {
            up.refresh(); // Reposition Flash/Silverlight

            up.settings.multipart_params.folder_id = this.folder_id;
        }, this);

        uploader.bind('QueueChanged', function (up) {
            Phlexible.console.debug('uploader::QueueChanged');
            if (up.state == plupload.STOPPED) {
                up.start();
            }
        }, this);

        uploader.bind('Error', function (up, err) {
            up.refresh(); // Reposition Flash/Silverlight
            this.getFilesGrid().getStore().reload();
        }, this);

        uploader.bind('ChunkUploaded', function (up, file, info) {
            Phlexible.console.debug('uploader::ChunkUploaded', 'id:' + file.id, 'info:', info);
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

        if (folder.id == 'root') return;
        if (this.dndFormInput) document.getElementById('folder_id').value = folder.id;

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
            start_folder_path: folder.getPath()
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

    onFileDblClick: function (grid, rowIndex) {
        var r = grid.getStore().getAt(rowIndex);
        if (this.mode == 'select') {
            var file_id = r.data.id;
            var file_version = r.data.version;
            var file_name = r.data.name;
            var folder_id = r.data.folder_id;
            this.fireEvent('fileSelect', file_id, file_version, file_name, folder_id);
        } else {
            var w = new Phlexible.mediamanager.FileDetailWindow({
                file_id: r.data.id,
                file_version: r.data.version,
                file_name: r.data.name,
                document_type_key: r.data.documenttypeKey,
                asset_type: r.data.asset_type,
                cache: r.data.cache,
                rights: grid.folder_rights
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

            case 'asset_type':
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

    onMove: function (e) {
        if (e.data.selections) {
            this.onMoveFile(e);
        }
    },

    onMoveFile: function (e) {
        var fileIDs = [];
        for (var i = 0; i < e.data.selections.length; i++) {
            fileIDs.push(e.data.selections[i].data.id);
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_move'),
            method: 'post',
            params: {
                folderID: e.target.id,
                fileIDs: Ext.encode(fileIDs)
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
            this.uploadChecker = new Phlexible.mediamanager.UploadChecker({
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
        if (Phlexible.App.getConfig().get('mediamanager.upload.enable_upload_sort')) {
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
        var file_ids = [];
        for (var i = 0; i < selections.length; i++) {
            file_ids.push(selections[i].data.id);
        }
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_download_file_zip'),
            params: {
                data: Ext.encode(file_ids)
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

    onDownloadFile: function (file_id, file_version) {
        if (!file_id) {
            file_id = this.getFilesGrid().getSelectionModel().getSelected().data.id;
        }

        var href = Phlexible.Router.generate('mediamanager_download_file', {id: file_id});

        if (file_version) {
            href += '/' + file_version;
        }

        document.location.href = href;
    }
});
