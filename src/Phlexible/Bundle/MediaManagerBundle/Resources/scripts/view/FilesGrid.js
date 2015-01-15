/*
Phlexible.mediamanager.ToolbarHidePlugin = Ext.extend(Object, {
    init: function (panel) {
        this.panel = panel;
        panel.on('render', this.onPanelRender, this);
    },

    onPanelRender: function (panel) {
        var b = panel.getTopToolbar();
        if (b) {
            b.on({
                beforehide: this.onToolbarBeforeChange,
                beforeshow: this.onToolbarBeforeChange,
                hide: this.onToolbarChange,
                show: this.onToolbarChange,
                scope: this
            });
        }
        b = panel.getBottomToolbar();
        if (b) {
            b.on({
                beforehide: this.onToolbarBeforeChange,
                beforeshow: this.onToolbarBeforeChange,
                hide: this.onToolbarChange,
                show: this.onToolbarChange,
                scope: this
            });
        }
    },

    onToolbarBeforeChange: function (b) {
        var height = this.panel.body.getSize().height;
        this.height = height + b.getSize().height;
    },

    onToolbarChange: function (b) {
        this.panel.body.setHeight(this.height);
        this.panel.doLayout();
    }
});
*/

Ext.define('Phlexible.mediamanager.FilesGrid', {
    extend: 'Ext.grid.GridPanel',
    alias: 'widget.mediamanager-files',

    title: Phlexible.mediamanager.Strings.no_folder_selected,
    iconCls: Phlexible.Icon.get('folder'),
    cls: 'p-mediamanager-files-grid',
    strings: Phlexible.mediamanager.Strings,
    enableDragDrop: true,
    ddGroup: 'mediamanager',

    folderRights: {},

    /**
     * @event fileChange
     * Fires when a File is selected
     * @param {Phlexible.mediamanager.FilesGrid} grid      This grid.
     * @param {Array}                            selection The selected file records.
     */
    /**
     * @event filterChange
     * Fires when a Filter is changed / cleard
     * @param {string} filterKey The key of the filter
     * @param {string} filterValue The value of the filter
     */
    /**
     * @event downloadFiles
     * Fires when one or more Files need to be downloaded
     */

    // private
    initComponent: function () {
        this.activeFilter = {};

        if (this.assetType) {
            this.activeFilter['assetType'] = this.assetType;
        }

        if (this.documenttypes) {
            this.activeFilter['documenttypes'] = this.documenttypes;
        }

        this.initMyStore();
        this.initMyColumns();
        this.initMyFeatures();
        this.initMyContextMenuItems();
        this.initMyContextMenu();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.mediamanager.model.File',
            proxy: {
                type: 'ajax',
                url: Phlexible.Router.generate('mediamanager_file_list'),
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'files',
                    idProperty: 'id',
                    totalProperty: 'total'
                },
                extraParams: {
                    limit: Phlexible.App.getConfig().get('mediamanager.files.num_files'),
                    filter: Ext.encode(this.activeFilter)
                }
            },
            sorters: [{property: "name", direction: "ASC"}],
            remoteSort: true,
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
                header: this.strings.name,
                dataIndex: 'name',
                sortable: true,
                renderer: this.nameRenderer,
                width: 150,
                flex: 1
            },
            {
                header: this.strings.folder,
                dataIndex: 'folder',
                sortable: false,
                hidden: true,
                width: 100
            },
            {
                header: this.strings.type,
                dataIndex: 'documentType',
                sortable: true,
                width: 120
            },{
                header: this.strings.asset,
                dataIndex: 'assetType',
                sortable: true,
                hidden: true,
                width: 120
            },{
                header: this.strings.version,
                dataIndex: 'version',
                sortable: true,
                width: 50
            },
            {
                header: this.strings.size,
                dataIndex: 'size',
                sortable: true,
                renderer: Phlexible.Format.size,
                width: 50
            },
            {
                header: this.strings.created_by,
                dataIndex: 'createUserId',
                sortable: true,
                width: 100
            },
            {
                xtype: 'datecolumn',
                header: this.strings.create_date,
                dataIndex: 'createTime',
                format: 'Y-m-d H:i:s',
                sortable: true,
                //renderer: Phlexible.Format.date,
                width: 120
            },
            {
                header: this.strings.modified_by,
                dataIndex: 'modifyUserId',
                sortable: true,
                hidden: true,
                width: 100
            },
            {
                xtype: 'datecolumn',
                header: this.strings.modify_date,
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
                        'console.log("extra");',
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
                                    '<td class="x-grid-cell x-grid-td">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="extra-image">',
                                                '<img width="256" height="256" src="<tpl if="values.record.data.cache._mm_extra">{[values.record.data.cache._mm_extra]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_extra\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="extra-text">',
                                                '<div>{[columnValues.name]}</div>',
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
                        'console.log("large");',
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
                                    '<td class="x-grid-cell x-grid-td">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="large-image">',
                                                '<img width="96" height="96" src="<tpl if="values.record.data.cache._mm_large">{[values.record.data.cache._mm_large]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_large\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="large-text">',
                                                '<div>{[columnValues.name]}</div>',
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
                        'console.log("medium");',
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
                                    '<td class="x-grid-cell x-grid-td">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="medium-image">',
                                                '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_medium\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="medium-text">',
                                                '<div>{[columnValues.name]}</div>',
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
                        'console.log("small");',
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
                                    '<td class="x-grid-cell x-grid-td">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<img width="16" height="16" src="<tpl if="values.record.data.cache._mm_small">{[values.record.data.cache._mm_small]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_small\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" /> ',
                                            '{[columnValues.name]}',
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
                        'console.log("tile");',
                        'var dataRowCls = values.recordIndex === -1 ? "" : " ' + Ext.baseCSSPrefix + 'grid-row";',
                        'var columnValues = values.view.tileViewFeature.getColumnValues(values.columns, values.record);',
                        '%}',
                        '<table id="{rowId}" ',
                            'data-boundView="{view.id}" ',
                            'data-recordId="{record.internalId}" ',
                            'data-recordIndex="{recordIndex}" ',
                            'class="tpl-view tile-view {[values.itemClasses.join(" ")]}" cellPadding="0" cellSpacing="0" {ariaTableAttr} style="{itemStyle};width:0">',
                            '<tbody>',
                                '<tr class="{[values.rowClasses.join(" ")]} {[dataRowCls]}" {rowAttr:attributes} {ariaRowAttr}>',
                                    '<td class="x-grid-cell x-grid-td">',
                                        '<div {unselectableAttr} class="x-grid-cell-inner">',
                                            '<div class="tile-image">',
                                                '<img width="48" height="48" src="<tpl if="values.record.data.cache._mm_medium">{[values.record.data.cache._mm_medium]}<tpl else>{[Phlexible.Router.generate(\"mediamanager_media\", {file_id: values.record.data.id, template_key: \"_mm_medium\", file_version: values.record.data.version, _dc: new Date().format(\"U\")})]}</tpl>" />',
                                            '</div>',
                                            '<div class="tile-text">',
                                                '<div class="tile-text-name">{[columnValues.name]}</div>',
                                                '<div class="tile-text-type">{[columnValues.documentType]}</div>',
                                                '<div class="tile-text-size">{[columnValues.size]}</div>',
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
        this.bbar = new Ext.PagingToolbar({
            store: this.store,
            border: false,
            pageSize: this.store.proxy.extraParams.limit,
            displayInfo: true,
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
            }]
        });
    },

    initMyContextMenu: function() {
        this.contextMenu = new Ext.menu.Menu({
            items: this.contextMenuItems
        });
    },

    initMyListeners: function() {
        this.on({
            selectionchange: this.onSelectionChange,
            xrender: function (c) {
                // TODO
                var firstGridDropTargetEl = c.getView().scroller.dom;
                var firstGridDropTarget = new Ext.dd.DropTarget(firstGridDropTargetEl, {
                    ddGroup: 'versions',
                    notifyDrop: function (ddSource, e, data) {
                        Phlexible.console.log(arguments);
                        return true;
//                                var records =  ddSource.dragData.selections;
//                                Ext.each(records, ddSource.grid.store.remove, ddSource.grid.store);
//                                firstGrid.store.add(records);
//                                firstGrid.store.sort('name', 'ASC');
//                                return true
                    }
                });
            },
            rowcontextmenu: this.onRowContextMenu,
            scope: this
        });
    },

    initMyContextMenuItems: function () {
        this.contextMenuItemIndex = {
            name: 0,
            rename: 2,
            'delete': 4,
            hide: 5,
            show: 6,
            download: 8,
            properties: 10,
            _last: 10
        };

        this.contextMenuItems = [
            {
                // 0
                cls: 'x-btn-text-icon-bold',
                itemId: 'nameBtn',
                iconCls: 'p-mediamanager-file-icon',
                text: '.'
            },
            '-',
            {
                // 2
                text: this.strings.rename_file,
                itemId: 'renameBtn',
                iconCls: 'p-mediamanager-file_edit-icon',
                handler: this.showRenameFileWindow,
                scope: this
            },
            '-',
            {
                // 4
                text: this.strings.delete_file,
                itemId: 'deleteBtn',
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showDeleteFileWindow,
                scope: this
            },
            {
                // 5
                text: this.strings.hide_file,
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showHideFileWindow,
                scope: this,
                hidden: true
            },
            {
                // 6
                text: this.strings.show_file,
                itemId: 'showBtn',
                iconCls: 'p-mediamanager-file_delete-icon',
                handler: this.showFiles,
                scope: this,
                hidden: true
            },
            '-',
            {
                // 8
                text: this.strings.download_file,
                itemId: 'downloadBtn',
                iconCls: 'p-mediamanager-download-icon',
                handler: this.download,
                scope: this
            },
            '-',
            {
                // 10
                text: this.strings.properties,
                itemId: 'propertiesBtn',
                iconCls: 'p-mediamanager-file_properties-icon',
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

        this.setTitle(this.strings.search_results);
    },

    setAssetTypeFilter: function(assetType) {
        this.setFilter('assetType', assetType);
    },

    setUserFilter: function(key, value) {

    },

    setTimeFilter: function (key, value) {
        var time = new Date();

        switch (value) {
            case "1day":
                time.add(Date.DAY, -1)

                break;

            case "2days":
                time.add(Date.DAY, -2)
                break;

            case "1week":
                time.add(Date.DAY, -7)
                break;

            case "1month":
                time.add(Date.MONTH, -1)
                break;

            case "6months":
                time.add(Date.MONTH, -6)
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

        this.store.getProxy().extraParams['filter'] = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    clearFilter: function () {
        this.activeFilter = {};

        this.store.getProxy().extraParams['filter'] = Ext.encode(this.activeFilter);
        this.store.reload();

        this.fireEvent('filterChange', this, this.activeFilter);
    },

    nameRenderer: function (name, e, r) {
        var documentTypeClass = Phlexible.documenttypes.DocumentTypes.getClass(r.data.documentTypeKey) || Phlexible.documenttypes.DocumentTypes.getClass('_unknown');
        documentTypeClass += "-small";

        var prefix = '';
        var style = '';

        prefix += Phlexible.mediamanager.Bullets.getWithTrailingSpace(r.data);

        if (r.data.hidden) {
            style += 'text-decoration: line-through;';
        }
        return '<span class="m-mimetype ' + documentTypeClass + '" style="' + style + '"><div>' + prefix + name + '<\/div><\/span>';
    },

    showRenameFileWindow: function () {
        var selFile = this.selModel.getSelected();

        var w = new Phlexible.mediamanager.RenameFileWindow({
            values: {
                file_name: selFile.data.name
            },
            submitParams: {
                fileId: selFile.data.id
            },
            listeners: {
                success: this.onRename,
                scope: this
            }
        });

        w.show();
    },

    showDetailWindow: function () {
        var selFile = this.selModel.getSelected();

        var w = new Phlexible.mediamanager.FileDetailWindow({
            fileId: selFile.data.id,
            fileVersion: selFile.data.version,
            fileName: selFile.data.name,
            documentTypeKey: selFile.data.documentTypeKey,
            assetType: selFile.data.assetType,
            cache: selFile.data.cache,
            rights: this.folderRights
        });
        w.show();
    },

    showDeleteFileWindow: function () {
        var fileArr = this.getSelectionModel().getSelections();

        if (fileArr.length > 1) {
            var txt = this.strings.delete_files_warning;
        } else {
            var txt = this.strings.delete_file_warning;
        }

        Ext.MessageBox.confirm(this.strings.confirm, txt, function (btn, e, x, fileArr) {
            if (btn == 'yes') {
                this.deleteFiles(fileArr);
            }
        }.createDelegate(this, [fileArr], true));
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
        var fileArr = this.getSelectionModel().getSelections();

        if (fileArr.length > 1) {
            var txt = this.strings.hide_files_warning;
        } else {
            var txt = this.strings.hide_file_warning;
        }

        Ext.MessageBox.confirm(this.strings.confirm, txt, function (btn, e, x, fileArr) {
            if (btn == 'yes') {
                this.hideFiles(fileArr);
            }
        }.createDelegate(this, [fileArr], true));
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

    onRowContextMenu: function (grid, rowIndex, event) {
        event.stopEvent();

        var contextmenu = this.contextMenu;

        var r = grid.getStore().getAt(rowIndex);
        var sm = grid.getSelectionModel();
//        Phlexible.console.log(this.folderRights);
        if (!sm.isSelected(r)) {
            sm.selectRow(rowIndex);
        }
        var selections = sm.getSelections();
        if (selections.length < 1) {
            return;
        } else if (selections.length > 1) {
            contextmenu.getComponent('nameBtn').setText('[' + String.format(this.strings.x_files, selections.length) + ']');

            contextmenu.getComponent('renameBtn').disable();
            //if(this.folderRights.file_modify == '1') {
            //    contextmenu.items.items[this.contextMenuItemIndex.rename].enable();
            //} else {
            //    contextmenu.items.items[this.contextMenuItemIndex.rename].disable();
            //}

            var used = 0;
            var hasHidden = false;
            var allHidden = true;
            var hasPresent = false;
            var allPresent = true;

            for (var i = 0; i < selections.length; i++) {
                if (selections[i].data.used) {
                    used |= selections[i].data.used;
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
            var deletePolicy = Phlexible.App.getConfig().get('mediamanager.delete_policy');

            contextmenu.getComponent('deleteBtn').setText(this.strings.delete_files);
            contextmenu.getComponent('hideBtn').setText(this.strings.hide_files);
            contextmenu.getComponent('showBtn').setText(this.strings.show_files);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) &&
                (!used ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (used == 1 || used == 2 || used == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.getComponent('deleteBtn').enable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (used == 1 || used == 2 || used == 3))) {
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

            contextmenu.getComponent('downloadBtn').setText(this.strings.download_files);
            contextmenu.getComponent('downloadBtn').setIconCls('p-mediamanager-download_files-icon');
            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DOWNLOAD) == '1' && hasPresent) {
                contextmenu.getComponent('downloadBtn').enable();
            } else {
                contextmenu.getComponent('downloadBtn').disable();
            }
        }
        else {
            contextmenu.getComponent('nameBtn').setText(r.get('name'));

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_MODIFY) == '1') {
                contextmenu.getComponent('renameBtn').enable();
            } else {
                contextmenu.getComponent('renameBtn').disable();
            }

            var used = r.data.used;
            var deletePolicy = Phlexible.App.getConfig().get('mediamanager.delete_policy');

            contextmenu.getComponent('deleteBtn').setText(this.strings.delete_file);
            contextmenu.getComponent('hideBtn').setText(this.strings.hide_file);
            contextmenu.getComponent('showBtn').setText(this.strings.show_file);

            if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (!used ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_OLD && (used == 1 || used == 2 || used == 3)) ||
                    (deletePolicy === Phlexible.mediamanager.DeletePolicy.DELETE_ALL))) {
                contextmenu.getComponent('deleteBtn').enable();
                contextmenu.getComponent('deleteBtn').show();
                contextmenu.getComponent('hideBtn').disable();
                contextmenu.getComponent('hideBtn').hide();
            }
            else if (this.checkRights(Phlexible.mediamanager.Rights.FILE_DELETE) == '1' &&
                (deletePolicy === Phlexible.mediamanager.DeletePolicy.HIDE_OLD && (used == 1 || used == 2 || used == 3))) {
                contextmenu.getComponent('deleteBtn').disable();
                contextmenu.getComponent('deleteBtn').hide();
                if (!r.data.hidden) {
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

            if (r.data.hidden) {
                contextmenu.getComponent('showBtn').enable();
                contextmenu.getComponent('showBtn').show();
            } else {
                contextmenu.getComponent('showBtn').disable();
                contextmenu.getComponent('showBtn').hide();
            }

            contextmenu.items.items[this.contextMenuItemIndex.download].setText(this.strings.download_file);
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
