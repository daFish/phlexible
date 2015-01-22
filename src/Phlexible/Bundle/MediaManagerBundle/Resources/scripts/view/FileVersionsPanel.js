Ext.define('Phlexible.mediamanager.view.FileVersionsPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediamanager-file-versions',

    title: '_FileVersionsPanel',
    iconCls: Phlexible.Icon.get('edit-number'),
    layout: 'fit',
    autoScroll: true,

    fileId: null,
    fileVersion: null,

    versionsText: '_versionsText',
    downloadFileVersionText: '_downloadFileVersionText',

    /**
     * @event versionChange
     */
    /**
     * @event versionSelect
     */

    /**
     *
     */
    initComponent: function () {
        this.initMyTemplates();
        this.initMyStore();
        this.initMyItems();
        this.initMyContextMenu();

        this.callParent(arguments);
    },

    onDestroy: function() {
        this.contextMenu.destroy();

        this.callParent(arguments);
    },

    initMyStore: function() {
        this.store = Ext.create('Ext.data.Store', {
            model: 'Phlexible.mediamanager.FileVersion',
            data: this.fileVersions || [],
            proxy: {
                type: 'ajax',
                url: '',
                simpleSortMode: true,
                reader: {
                    type: 'json',
                    rootProperty: 'detail',
                    idProperty: 'uid',
                    totalProperty: 'count'
                },
                extraParams: this.storeExtraParams
            },
            listeners: {
                load: function (store, records) {
                    if (!records.length) {
                        this.setTitle(this.versionsText);
                    }
                    else {
                        this.setTitle(this.versionsText + ' [' + records.length + ']');
                        if (this.fileVersion) {
                            var index = store.find('version', this.fileVersion);
                            this.getComponent(0).select(index);
                        } else {
                            this.getComponent(0).select(0);
                        }
                    }
                },
                scope: this
            }
        });
    },

    initMyTemplates: function() {
        this.fileVersionsTpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="version-wrap" id="version-{version}">',
            '<div class="thumb"><img src="{[Phlexible.Router.generate(\"mediamanager_media\", {fileId: values.id, templateKey: \"_mm_medium\", fileVersion: values.version})]}" width="48" height="48"></div>',
            '<div class="text">',
            '<span><b qtip="{name}">{[values.name.shorten(25)]}</b></span><br />',
            //'<span>[v{version}] {[Phlexible.documenttypes.DocumentTypes.getText(values.mediaType)]}, {[Phlexible.Format.size(values.size)]}</span><br />',
            //'<span>Create User: {create_user_id}</span><br />',
            '<span>{create_time}</span><br />',
            '</div>',
            '<div class="x-clear"></div>',
            '</div>',
            '</tpl>'
        );
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'dataview',
                cls: 'p-mediamanager-file-versions',
                store: this.store,
                tpl: this.fileVersionsTpl,
                autoHeight: true,
                singleSelect: true,
                overItemCls: 'x-view-over',
                itemSelector: 'div.version-wrap',
                emptyText: 'No versions to display',
                listeners: {
                    click: this.versionSelect,
                    dblclick: this.versionSelect,
                    contextmenu: this.onContextMenu,
                    scope: this.contextMenu
                }
            }
        ];
    },

    initMyContextMenu: function() {
        this.contextMenu = Ext.create('Ext.menu.Menu', {
            items: [
                {
                    text: this.downloadFileVersionText,
                    iconCls: Phlexible.Icon.get('drive-download'),
                    handler: function (btn) {
                        this.fireEvent('versionDownload', btn.parentMenu.fileId, btn.parentMenu.fileVersion);
                    },
                    scope: this
                }
            ]
        });
    },

    onDestroy: function() {
        this.contextMenu.destroy();

        this.callParent(arguments);
    },

    versionSelect: function (view, rowIndex, node, e) {
        e.stopEvent();

        var record = view.store.getAt(rowIndex);

        this.fireEvent('versionSelect', record);
    },

    loadVersions: function(versions) {
        this.getComponent(0).getStore().loadData(versions);
    },

    loadFile: function (fileId, fileVersion) {
        this.fileId = fileId;

        if (fileVersion) {
            this.fileVersion = fileVersion;
        } else {
            this.fileVersion = null;
        }

        this.getComponent(0).getStore().getProxy().setUrl(Phlexible.Router.generate('mediamanager_file_detail', {fileId: this.fileId}));
        this.getComponent(0).getStore().load();
    },

    empty: function () {
        this.getComponent(0).store.removeAll();
    },

    onContextMenu: function (view, rowIndex, node, event) {
        event.stopEvent();

        this.fileId = view.store.getAt(rowIndex).data.id;
        this.fileVersion = view.store.getAt(rowIndex).data.version;

        var coords = event.getXY();
        this.showAt([coords[0], coords[1]]);
    }
});
