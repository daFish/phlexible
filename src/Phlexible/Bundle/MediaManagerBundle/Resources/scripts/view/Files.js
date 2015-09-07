Ext.define('Phlexible.mediamanager.view.Files', {
    extend: 'Ext.grid.GridPanel',
    requires: [
        'Phlexible.mediamanager.model.File'
    ],
    xtype: 'mediamanager.files',

    iconCls: Phlexible.Icon.get('folder-open-document'),
    cls: 'p-mediamanager-files-grid',
    emptyText: '_emptyText',
    multiSelect: true,

    folderRights: {},

    nameText: '_nameText',
    folderText: '_folderText',
    typeText: '_typeText',
    categoryText: '_categoryText',
    versionText: '_versionText',
    sizeText: '_sizeText',
    createdByText: '_createdByText',
    createdAtText: '_createdAtText',
    modifiedByText: '_modifiedByText',
    modifiedAtText: '_modifiedAtText',
    renameFileText: '_renameFileText',
    deleteFilesText: '_deleteFilesText',
    deleteFileText: '_deleteFileText',
    hideFilesText: '_hideFilesText',
    hideFileText: '_hideFileText',
    showFilesText: '_showFilesText',
    showFileText: '_showFileText',
    downloadFilesText: '_downloadFilesText',
    downloadFileText: '_downloadFileText',
    propertiesText: '_propertiesText',
    searchResultsText: '_searchResultsText',
    deleteFilesWarningText: '_deleteFilesWarningText',
    deleteFileWarningText: '_deleteFileWarningText',
    confirmText: '_confirmText',
    hideFilesWarningText: '_hideFilesWarningText',
    hideFileWarningText: '_hideFileWarningText',
    xFilesText: '_xFilesText',

    /**
     * Fires when a File is selected
     *
     * @event fileChange
     * @param {Phlexible.mediamanager.view.Files} grid
     * @param {Phlexible.mediamanager.model.File[]} selection
     */

    /**
     * Fires when a Filter is changed / cleard
     *
     * @event filterChange
     * @param {Phlexible.mediamanager.view.Files} grid
     * @param {Object} filter
     */

    /**
     * Fires when one needs to be downloaded
     *
     * @event downloadFile
     * @param {Phlexible.mediamanager.view.Files} grid
     * @param {Phlexible.mediamanager.model.File} files
     */

    /**
     * Fires when at least two files need to be downloaded
     *
     * @event downloadFiles
     * @param {Phlexible.mediamanager.view.Files} grid
     * @param {Array} files
     */

    /**
     * @private
     */
    initComponent: function () {
        this.activeFilter = {};

        if (this.mediaCategory) {
            this.activeFilter.mediaCategory = this.mediaCategory;
        }

        if (this.mediaTypes) {
            this.activeFilter.mediaTypes = this.mediaTypes;
        }

        this.initMyView();
        this.initMyStore();
        this.initMyColumns();
        this.initMyFeatures();
        this.initMyContextMenuItems();
        this.initMyContextMenu();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyView: function() {
        this.viewConfig = {
            plugins: {
                ptype: 'gridviewdragdrop',
                containerScroll: false,
                dragGroup: 'p-mediamanager-folders'
            }
        };
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.mediamanager.model.File',
            remoteSort: true,
            sorters: [{property: "name", direction: "ASC"}],
            listeners: {
                load: function (store) {
                    if (this.startFileId) {
                        var index = store.find('id', this.startFileId);
                        this.startFileId = false;

                        if (index != -1) {
                            var r = store.getAt(index);
                            this.selectedRecordDummy = r;
                            this.selModel.selectRecords([r]);
                            this.fireEvent('fileChange', this, [r]);
                        }
                    }
                },
                scope: this
            }
        });
    },

    initMyColumns: function() {
        this.columns = [
            {
                header: this.nameText,
                dataIndex: 'name',
                sortable: true,
                renderer: this.nameRenderer,
                width: 150,
                flex: 1
            },
            {
                header: this.folderText,
                dataIndex: 'folder',
                sortable: false,
                hidden: true,
                width: 100
            },
            {
                header: this.typeText,
                dataIndex: 'mediaTypeTitle',
                sortable: true,
                width: 120
            },{
                header: this.categoryText,
                dataIndex: 'mediaCategory',
                sortable: true,
                hidden: true,
                width: 120
            },{
                header: this.versionText,
                dataIndex: 'version',
                sortable: true,
                width: 50
            },
            {
                header: this.sizeText,
                dataIndex: 'size',
                sortable: true,
                renderer: Phlexible.Format.size,
                width: 50
            },
            {
                header: this.createdByText,
                dataIndex: 'createUser',
                sortable: true,
                width: 100
            },
            {
                xtype: 'datecolumn',
                header: this.createdAtText,
                dataIndex: 'createTime',
                format: 'Y-m-d H:i:s',
                sortable: true,
                //renderer: Phlexible.Format.date,
                width: 120
            },
            {
                header: this.modifiedByText,
                dataIndex: 'modifyUserId',
                sortable: true,
                hidden: true,
                width: 100
            },
            {
                xtype: 'datecolumn',
                header: this.modifiedAtText,
                dataIndex: 'modifyTime',
                format: 'Y-m-d H:i:s',
                sortable: true,
                //renderer: Phlexible.Format.date,
                hidden: true,
                width: 120
            }
        ];
    },

    initMyFeatures: function() {
        var tileView = Ext.create('Ext.ux.grid.feature.Tileview', {
            viewMode: 'tiles',
            rowTpls: {
                extra: [
                    '<tpl if="view.tileViewFeature.viewMode==\'extra\'">',
                        '{%',
                        'Phlexible.console.log("view: extra");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view extra-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td x-grid-cell-{[values.columns[0].getId()]}" data-columnid="{[values.columns[0].getId()]}">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="extra-image">',
                                                //'<img width="256" height="256" src="<tpl if="values.record.data.cache._mm_extra">{[values.record.data.cache._mm_extra]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.record.data.id, templateKey: \"_mm_extra\", fileVersion: values.record.data.version, _dc: Ext.Date.format(new Date(), \"U\")})]}</tpl>" />',
                                                '<img width="256" height="256" src="/bundles/phlexiblemediamanager/svg/{[values.record.data.mediaCategory]}.svg" />',
                                            '</div>',
                                            '<div class="extra-text">',
                                                '<div>{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}{[columnValues.name]}</div>',
                                            '</div>',
                                        '</div>',
                                    '</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',
                    '<tpl else>',
                        '{%this.nextTpl.applyOut(values, out, parent);%}',
                    '</tpl>',
                    {
                        priority: 10000
                    }
                ],
                large: [
                    '<tpl if="view.tileViewFeature.viewMode==\'large\'">',
                        '{%',
                        'Phlexible.console.log("view: large");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view large-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td x-grid-cell-{[values.columns[0].getId()]}" data-columnid="{[values.columns[0].getId()]}">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="large-image">',
                                                '<img width="96" height="96" src="<tpl if="values.record.data.cache._mm_large">{[values.record.data.cache._mm_large]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.record.data.id, templateKey: \"_mm_large\", fileVersion: values.record.data.version, _dc: Ext.Date.format(new Date(), \"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="large-text">',
                                                '<div>{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}{[columnValues.name]}</div>',
                                            '</div>',
                                        '</div>',
                                    '</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',
                    '<tpl else>',
                        '{%this.nextTpl.applyOut(values, out, parent);%}',
                    '</tpl>',
                    {
                        priority: 10000
                    }
                ],
                medium: [
                    '<tpl if="view.tileViewFeature.viewMode==\'medium\'">',
                        '{%',
                        'Phlexible.console.log("view: medium");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view medium-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td x-grid-cell-{[values.columns[0].getId()]}" data-columnid="{[values.columns[0].getId()]}">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="medium-image">',
                                                '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.record.data.id, templateKey: \"_mm_medium\", fileVersion: values.record.data.version, _dc: Ext.Date.format(new Date(), \"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="medium-text">',
                                                '<div>{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}{[columnValues.name]}</div>',
                                            '</div>',
                                        '</div>',
                                    '</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',
                    '<tpl else>',
                        '{%this.nextTpl.applyOut(values, out, parent);%}',
                    '</tpl>',
                    {
                        priority: 10000
                    }
                ],
                small: [
                    '<tpl if="view.tileViewFeature.viewMode==\'small\'">',
                        '{%',
                        'Phlexible.console.log("view: small");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view small-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td x-grid-cell-{[values.columns[0].getId()]}" data-columnid="{[values.columns[0].getId()]}">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<span class="small-image">',
                                                '<img width="16" height="16" src="<tpl if="values.record.data.cache._mm_small">{[values.record.data.cache._mm_small]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.record.data.id, templateKey: \"_mm_small\", fileVersion: values.record.data.version, _dc: Ext.Date.format(new Date(), \"U\")})]}</tpl>" /> ',
                                            '</span>',
                                            '<span class="small-text">',
                                                '{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}{[columnValues.name]}',
                                            '</span>',
                                        '</div>',
                                    '</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',
                    '<tpl else>',
                        '{%this.nextTpl.applyOut(values, out, parent);%}',
                    '</tpl>',
                    {
                        priority: 10000
                    }
                ],
                tile: [
                    '<tpl if="view.tileViewFeature.viewMode==\'tile\'">',
                        '{%',
                        'Phlexible.console.log("view: tile");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        'console.log(columnValues);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view tile-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td x-grid-cell-{[values.columns[0].getId()]}" data-columnid="{[values.columns[0].getId()]}">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="tile-image">',
                                                '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.record.data.id, templateKey: \"_mm_medium\", fileVersion: values.record.data.version, _dc: Ext.Date.format(new Date(), \"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="tile-text">',
                                                '<div class="tile-text-name">{[Phlexible.mediamanager.Bullets.getWithTrailingSpace(values.record.data)]}{[columnValues.name]}</div>',
                                                '<div class="tile-text-type">{[columnValues.mediaTypeTitle]}</div>',
                                                '<div class="tile-text-size">{[Phlexible.Format.size(columnValues.size)]}</div>',
                                            '</div>',
                                            '<div class="x-clear"></div>',
                                        '</div>',
                                    '</td>',
                                '</tr>',
                            '</tbody>',
                        '</table>',
                    '<tpl else>',
                        '{%this.nextTpl.applyOut(values, out, parent);%}',
                    '</tpl>',
                    {
                        priority: 10000
                    }
                ]
            }
        });

        this.features = [tileView];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [{
                xtype: 'pagingtoolbar',
                border: false,
                pageSize: this.store.proxy.extraParams.limit,
                displayInfo: true,
                store: this.store,
                items: [
                    '-',
                    {
                        xtype: 'slider',
                        width: 104,
                        value: this.store.proxy.extraParams.limit,
                        increment: 5,
                        minValue: 5,
                        maxValue: 250,
                        listeners: {
                            drag: function (slider) {
                                this.getBottomToolbar().items.items[13].setText(slider.getValue());
                            },
                            changecomplete: function (slider, value) {
                                var pager = this.getBottomToolbar();
                                pager.pageSize = value;
                                pager.items.items[13].setText(value);
                                this.store.proxy.extraParams.limit = value;

                                if (this.store.totalLength < value) {
                                    this.store.reload({
                                        params: {
                                            start: 0
                                        }
                                    });
                                    return;
                                } else if (pager.cursor % value !== 0) {
                                    this.store.reload({
                                        params: {
                                            start: Math.floor(pager.cursor / value) * value
                                        }
                                    });
                                    return;
                                }
                                this.store.reload();
                            },
                            scope: this
                        }
                    }, {
                        text: this.store.proxy.extraParams.limit
                    }
                ]
            }]
        }];
    },

    initMyContextMenu: function() {
        this.contextMenu = Ext.create('Ext.menu.Menu', {
            items: this.contextMenuItems
        });
    },

    initMyListeners: function() {
        this.on({
            selectionchange: this.onSelectionChange,
            rowcontextmenu: this.onContextMenu,
            scope: this
        });
    },

    initMyContextMenuItems: function () {
        this.contextMenuItems = [
            {
                itemId: 'nameBtn',
                iconCls: Phlexible.Icon.get('document'),
                text: '.',
                focusable: false
            },
            '-',
            {
                text: this.renameFileText,
                itemId: 'renameBtn',
                iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
                handler: this.showRenameFileWindow,
                scope: this
            },
            {
                text: this.deleteFileText,
                itemId: 'deleteBtn',
                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                handler: this.showDeleteFileWindow,
                scope: this
            },
            {
                text: this.hideFileText,
                itemId: 'hideBtn',
                iconCls: Phlexible.Icon.get('eye-close'),
                handler: this.showHideFileWindow,
                scope: this,
                hidden: true
            },
            {
                text: this.showFileText,
                itemId: 'showBtn',
                iconCls: Phlexible.Icon.get('eye'),
                handler: this.showFiles,
                scope: this,
                hidden: true
            },
            '-',
            {
                text: this.downloadFileText,
                itemId: 'downloadBtn',
                iconCls: Phlexible.Icon.get('drive-download'),
                handler: this.download,
                scope: this
            },
            '-',
            {
                text: this.propertiesText,
                itemId: 'propertiesBtn',
                iconCls: Phlexible.Icon.get('property'),
                handler: this.showDetailWindow,
                scope: this
            }
        ];
    },

    checkRights: function (right) {
        return this.folderRights.indexOf(right) !== -1;
    },

    getDragDropText: function () {
        var sel = this.getSelectionModel().getSelections();
        if (sel.length != 1) {
            return Phlexible.mediamanager.templates.DragMulti.apply(sel);
        } else {
            return Phlexible.mediamanager.templates.DragSingle.apply(sel);
        }
    },

    loadFolder: function (volumeId, folderId, folderName, folderRights) {
        this.volumeId = volumeId;
        this.folderId = folderId;
        this.folderRights = folderRights;

        this.store.getProxy().extraParams.volumeId = this.volumeId;
        this.store.getProxy().extraParams.folderId = this.folderId;

        this.store.load();

        this.setTitle(folderName);
    },

    loadSearch: function (search_values) {
        var params;
        if (search_values) {
            if (search_values.below) {
                search_values.below = this.folderId;
            }
            params = {
                searchValues: Ext.encode(search_values)
            };

//            this.store.groupBy('folder');
        } else {
            params = {
                folderID: this.folderId
            };

//            this.store.clearGrouping();
        }

        params.start = 0;

        this.store.load({
            params: params
        });

        this.setTitle(this.searchResultsText);
    },

    setAssetTypeFilter: function(mediaCategory) {
        this.setFilter('mediaCategory', mediaCategory);
    },

    setUserFilter: function(key, value) {

    },

    setTimeFilter: function (key, value) {
        var time = new Date();

        switch (value) {
            case "1day":
                time.add(Date.DAY, -1);

                break;

            case "2days":
                time.add(Date.DAY, -2);
                break;

            case "1week":
                time.add(Date.DAY, -7);
                break;

            case "1month":
                time.add(Date.MONTH, -1);
                break;

            case "6months":
                time.add(Date.MONTH, -6);
                break;

            default:
                return;
        }

        if (time && key) {
            this.setFilter(key, time.format('U'));
        }
    },

    setFilter: function (key, value) {
        this.activeFilter[key] = value;

        this.store.getProxy().extraParams.filter = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    clearFilter: function () {
        this.activeFilter = {};

        this.store.getProxy().extraParams.filter = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    nameRenderer: function (name, e, r) {
        var documentTypeClass = Phlexible.documenttypes.DocumentTypes.getClass(r.get('mediaType')) || Phlexible.documenttypes.DocumentTypes.getClass('_unknown');
        documentTypeClass += "-small";

        var prefix = ' ';
        var style = '';

        prefix += Phlexible.mediamanager.Bullets.getWithTrailingSpace(r.data);

        if (r.data.hidden) {
            style += 'text-decoration: line-through;';
        }
        return Phlexible.Icon.inlineDirect(documentTypeClass) + prefix + name;
    },

    showRenameFileWindow: function () {
        var nodes = this.getSelectionModel().getSelection(),
            file;
        if (!nodes.length) {
            return;
        }
        file = nodes[0];

        var w = Ext.create('Phlexible.mediamanager.window.FileRenameWindow', {
            fileId: file.id,
            fileName: file.data.name,
            listeners: {
                success: function (data) {
                    file.set('name', data.name);
                },
                scope: this
            }
        });

        w.show();
    },

    showDetailWindow: function () {
        var selections = this.selModel.getSelection(),
            file;

        if (!selections.length) {
            return;
        }

        file = selections[0];

        var w = Ext.create('Phlexible.mediamanager.window.FileDetailWindow', {
            file: file,
            fileId: file.id,
            fileVersion: file.get('version'),
            fileName: file.get('name'),
            mediaType: file.get('mediaType'),
            mediaCategory: file.get('mediaCategory'),
            cache: file.get('cache'),
            folderRights: this.folderRights
        });
        w.show();
    },

    showDeleteFileWindow: function () {
        var files = this.getSelectionModel().getSelections(),
            text;

        if (fileArr.length > 1) {
            text = this.deleteFilesWarningText;
        } else {
            text = this.deleteFileWarningText;
        }

        Ext.MessageBox.confirm(this.confirmText, text, function (btn) {
            if (btn == 'yes') {
                this.deleteFiles(files);
            }
        }, this);
    },

    deleteFiles: function (file) {
        var fileId = '';
        for (var i = 0; i < file.length; i++) {
            fileId += (fileId ? ',' : '') + file[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_delete'),
            params: {
                volumeId: this.volumeId,
                fileId: fileId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Delete failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    showHideFileWindow: function () {
        var fileArr = this.getSelectionModel().getSelections(),
            txt;

        if (fileArr.length > 1) {
            txt = this.hideFilesWarningText;
        } else {
            txt = this.hideFileWarningText;
        }

        Ext.MessageBox.confirm(this.confirmText, txt, function (btn, e, x) {
            if (btn === 'yes') {
                this.hideFiles(fileArr);
            }
        }, this);
    },

    hideFiles: function (file) {
        var fileId = '';
        for (var i = 0; i < file.length; i++) {
            fileId += (fileId ? ',' : '') + file[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_hide'),
            params: {
                volumeId: this.volumeId,
                fileId: fileId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Hide failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    showFiles: function (file) {
        var fileArr = this.getSelectionModel().getSelections();
        var fileId = '';
        for (var i = 0; i < fileArr.length; i++) {
            fileId += (fileId ? ',' : '') + fileArr[i].get('id');
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_show'),
            params: {
                volumeId: this.volumeId,
                fileId: fileId
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (!data.success) {
                    Ext.MessageBox.alert('Status', 'Show failed: ' + data.msg);
                }

                this.store.reload();
            },
            scope: this
        });
    },

    onSelectionChange: function (grid, selected) {
        this.fireEvent('fileChange', grid, selected);
    },

    onContextMenu: function (grid, record, tr, rowIndex, event) {
        event.stopEvent();

        var contextmenu = this.contextMenu;
        var selections = [record];

        if (selections.length > 1) {
            contextmenu.getComponent('nameBtn').setText('[' + String.format(this.xFilesText, selections.length) + ']');
            contextmenu.getComponent('nameBtn').setIconCls('documents');

            contextmenu.getComponent('renameBtn').disable();
            //if(this.folderRights.file_modify == '1') {
            //    contextmenu.getComponent('renameBtn').enable();
            //} else {
            //    contextmenu.getComponent('renameBtn').disable();
            //}

            var usageStatus = 0;
            var hasHidden = false;
            var allHidden = true;
            var hasPresent = false;
            var allPresent = true;

            for (var i = 0; i < selections.length; i++) {
                if (selections[i].data.usageStatus) {
                    usageStatus |= selections[i].data.usageStatus;
                }
                if (selections[i].data.hidden) {
                    hasHidden = true;
                } else {
                    allHidden = false;
                }
                if (selections[i].data.present) {
                    hasPresent = true;
                } else {
                    allPresent = false;
                }
            }
            var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');

            contextmenu.getComponent('deleteBtn').setText(this.deleteFilesText);
            contextmenu.getComponent('hideBtn').setText(this.hideFilesText);
            contextmenu.getComponent('showBtn').setText(this.showFilesText);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) &&
                (!usageStatus ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (usageStatus == 1 || usageStatus == 2 || usageStatus == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.getComponent('deleteBtn').enable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (usageStatus == 1 || usageStatus == 2 || usageStatus == 3))) {
                contextmenu.getComponent('deleteBtn').disable();
                contextmenu.getComponent('deleteBtn').hide();
                if (!allHidden) {
                    contextmenu.getComponent('hideBtn').enable();
                    contextmenu.getComponent('hideBtn').show();
                } else {
                    contextmenu.getComponent('hideBtn').disable();
                    contextmenu.getComponent('hideBtn').hide();
                }
            }
            else {
                contextmenu.getComponent('deleteBtn').disable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }

            if (hasHidden) {
                contextmenu.getComponent('showBtn').enable();
                contextmenu.getComponent('showBtn').show();
            } else {
                contextmenu.getComponent('showBtn').disable();
                contextmenu.getComponent('showBtn').hide();
            }

            contextmenu.getComponent('downloadBtn').setText(this.downloadFilesText);
            contextmenu.getComponent('downloadBtn').setIconCls('p-mediamanager-download_files-icon');
            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD) == '1' && hasPresent) {
                contextmenu.getComponent('downloadBtn').enable();
            } else {
                contextmenu.getComponent('downloadBtn').disable();
            }
        }
        else {
            contextmenu.getComponent('nameBtn').setText(record.get('name'));
            var documentTypeClass = Phlexible.documenttypes.DocumentTypes.getClass(record.get('mediaType')) || Phlexible.documenttypes.DocumentTypes.getClass('_unknown');
            contextmenu.getComponent('nameBtn').setIconCls(documentTypeClass + '-small');

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_MODIFY) == '1') {
                contextmenu.getComponent('renameBtn').enable();
            } else {
                contextmenu.getComponent('renameBtn').disable();
            }

            var usageStatus = record.get('usageStatus');
            var deletePolicy = Phlexible.Config.get('mediamanager.delete_policy');

            contextmenu.getComponent('deleteBtn').setText(this.deleteFileText);
            contextmenu.getComponent('hideBtn').setText(this.hideFileText);
            contextmenu.getComponent('showBtn').setText(this.showFileText);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (!usageStatus ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (usageStatus == 1 || usageStatus == 2 || usageStatus == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.getComponent('deleteBtn').enable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (usageStatus == 1 || usageStatus == 2 || usageStatus == 3))) {
                contextmenu.getComponent('deleteBtn').disable();
                contextmenu.getComponent('deleteBtn').hide();
                if (!record.get('hidden')) {
                    contextmenu.getComponent('hideBtn').enable();
                    contextmenu.getComponent('hideBtn').show();
                }
                else {
                    contextmenu.getComponent('hideBtn').disable();
                    contextmenu.getComponent('hideBtn').hide();
                }
            }
            else {
                contextmenu.getComponent('deleteBtn').disable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }

            if (record.get('hidden')) {
                contextmenu.getComponent('showBtn').enable();
                contextmenu.getComponent('showBtn').show();
            } else {
                contextmenu.getComponent('showBtn').disable();
                contextmenu.getComponent('showBtn').hide();
            }

            contextmenu.getComponent('downloadBtn').setText(this.downloadFileText);
            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD) == '1' && selections[0].data.present) {
                contextmenu.getComponent('showBtn').enable();
            } else {
                contextmenu.getComponent('showBtn').disable();
            }
        }

        var coords = event.getXY();
        contextmenu.showAt([coords[0], coords[1]]);
    },

    onRename: function () {
        this.store.reload();
    },

    download: function () {
        var sel = this.getSelectionModel().getSelections();

        if (sel.length > 1) {
            this.fireEvent('downloadFiles');
        } else if (sel.length === 1) {
            this.fireEvent('downloadFile');
        }
    }

});
