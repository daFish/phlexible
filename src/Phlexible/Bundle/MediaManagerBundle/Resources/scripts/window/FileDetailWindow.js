Ext.define('Phlexible.mediamanager.window.FileDetailWindow', {
    extend: 'Ext.window.Window',
    requires: [
        'Phlexible.mediamanager.view.FilePreview',
        'Phlexible.mediamanager.view.FileProperties'
    ],

    iconCls: Phlexible.Icon.get('document'),
    width: 900,
    minWidth: 900,
    height: 600,
    minHeight: 600,
    layout: 'border',
    cls: 'p-mediamanager-file-detail-window',
    modal: true,
    constrainHeader: true,
    maximizable: true,
    resizable: true,

    file: null,
    folderRights: [],

    pathText: '_pathText',
    idText: '_idText',
    nameText: '_nameText',
    attributesText: '_attributesText',
    noAttributesText: '_noAttributesText',

    initComponent: function () {
        this.title = this.file.get('name');
        this.iconCls = Phlexible.mediatype.MediaTypes.getClass(this.file.get('mediaType')) + "-small";

        this.initMyTabs();
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.plain = true;

        this.items = [
            {
                region: 'west',
                itemId: 'previewWrap',
                border: true,
                width: 274,
                layout: 'border',
                padding: '5 0 5 5',
                bodyStyle: 'background-color: white;',
                items: [
                    {
                        xtype: 'mediamanager.file-preview',
                        itemId: 'preview',
                        region: 'north',
                        border: false,
                        header: false,
                        fileId: this.file.get('id'),
                        fileVersion: this.file.get('version'),
                        fileName: this.file.get('name'),
                        mediaType: this.file.get('mediaType'),
                        cache: this.file.get('cache')
                    },
                    {
                        xtype: 'mediamanager.file-properties',
                        itemId: 'details',
                        region: 'center',
                        header: false,
                        border: false,
                        autoHeight: true,
                        padding: 5,
                        data: this.file.data
                    }
                ]
            },
            {
                xtype: 'tabpanel',
                region: 'center',
                itemId: 'tabs',
                deferredRender: false,
                padding: 5,
                activeTab: 1,
                items: this.tabs
            }
        ];

        delete this.tabs;
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
                    value: this.file.get('folderPath'),
                    width: 200
                },
                ' ',
                this.nameText,
                {
                    xtype: 'textfield',
                    itemId: 'nameField',
                    value: this.file.get('name'),
                    flex: 1
                },
                ' ',
                this.idText,
                {
                    xtype: 'textfield',
                    itemId: 'idField',
                    value: this.file.get('id'),
                    width: 240
                }
            ]
        },{
            xtype: 'toolbar',
            itemId: 'bbar',
            dock: 'bottom',
            hidden: !this.file.get('prevId') && !this.file.get('nextId'),
            items: [
                {
                    text: 'Previous file',
                    itemId: 'prevBtn',
                    iconCls: Phlexible.Icon.get('arrow-180'),
                    hidden: !this.file.get('prevId'),
                    handler: function () {
                        this.fileId = this.file.get('prevId');
                        this.fileVersion = this.file.get('prevVersion');
                        this.load();
                    },
                    scope: this
                }, '->', {
                    text: 'Next file',
                    itemId: 'nextBtn',
                    iconCls: Phlexible.Icon.get('arrow'),
                    hidden: !this.file.get('nextId'),
                    handler: function () {
                        this.fileId = this.file.get('nextId');
                        this.fileVersion = this.file.get('nextVersion');
                        this.load();
                    },
                    scope: this
                }
            ]
        }];
    },

    initMyTabs: function () {
        this.tabs = [
            {
                xtype: 'mediamanager.file-versions',
                itemId: 'versions',
                region: 'center',
                fileId: this.file.get('id'),
                fileVersion: this.file.get('version'),
                fileVersions: this.file.get('versions'),
                hidden: !this.file.get('hasVersions'),
                listeners: {
                    versionSelect: this.onVersionSelect,
                    versionDownload: function (fileId, fileVersion) {
                        var href = Phlexible.Router.generate('mediamanager_download_file', {id: fileId});

                        if (fileVersion) {
                            href += '/' + fileVersion;
                        }

                        document.location.href = href;
                    },
                    scope: this
                }
            },
            {
                xtype: 'propertygrid',
                itemId: 'attributes',
                title: this.attributesText,
                iconCls: Phlexible.Icon.get('property'),
                source: this.file.get('attributes'),
                emptyText: this.noAttributeValuesText,
                hidden: false
            },
            {
                xtype: 'mediamanager.file-meta',
                itemId: 'meta',
                border: false,
                params: {
                    fileId: this.file.get('id'),
                    fileVersion: this.file.get('version'),
                },
                rights: this.folderRights
            }/*,{
                title: 'Rights',
                iconCls: 'p-mediamanager-file_rights-icon',
                hidden: true
             }*/
        ];
    },

    getPreviewWrap: function () {
        return this.getComponent('previewWrap');
    },

    getPreviewPanel: function () {
        return this.getPreviewWrap().getComponent('preview');
    },

    getDetailsPanel: function () {
        return this.getPreviewWrap().getComponent('details');
    },

    getTabPanel: function () {
        return this.getComponent('tabs');
    },

    getVersionsPanel: function () {
        return this.getTabPanel().getComponent('versions');
    },

    getAttributesPanel: function () {
        return this.getTabPanel().getComponent('attributes');
    },

    getMetaGrid: function () {
        return this.getTabPanel().getComponent('meta');
    },

    onVersionSelect: function (fileId, fileVersion, fileName, folderId, mediaType) {
        //this.getPreviewPanel().load(fileId, fileVersion, fileName, mediaType);
        this.load(fileId, fileVersion);
    },

    load: function (fileId, fileVersion) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_detail', {fileId: fileId, fileVersion: fileVersion}),
            success: function(response) {
                var data = Ext.decode(response.responseText);
                return;
                this.setTitle(data.name);
                this.setIconClass(Phlexible.mediatype.MediaTypes.getClass(data.media_type) + "-small");
                this.getPreviewPanel().load(data.id, data.version, data.name, data.media_type);
                Phlexible.mediamanager.FileDetailAttributesTemplate.overwrite(this.getDetailsPanel().body, data.detail);
                this.getAttributesPanel().setSource(data.attributes);
                //var html = Phlexible.mediamanager.FileDetailAttributesTemplate.applyTemplate(data);
                //this.getAccordionPanel().body.update(html);
                this.getToolbar().items.items[1].setValue(data.path);
                this.getToolbar().items.items[5].setValue(data.name);
                this.getToolbar().items.items[9].setValue(data.id);

                var bbar = this.getBottomToolbar();
                if (data.prev) {
                    this.prev = data.prev;
                    bbar.items.items[1].show();
                    bbar.items.items[2].show();
                } else {
                    this.prev = null;
                    bbar.items.items[1].hide();
                    bbar.items.items[2].hide();
                }
                if (data.next) {
                    this.next = data.next;
                    bbar.items.items[3].show();
                    bbar.items.items[2].show();
                } else {
                    this.next = null;
                    bbar.items.items[3].hide();
                    bbar.items.items[2].hide();
                }
            },
            scope: this
        });
    },

    setFile: function(file) {
        this.setTitle(file.get('name'));
        this.setIconCls(Phlexible.mediatype.MediaTypes.getClass(file.get('mediaType')) + "-small");

        this.getPreviewPanel().load(
            file.get('fileId'),
            file.get('version'),
            file.get('name'),
            file.get('mediaType'),
            file.get('mediaCategory'),
            file.get('cache')
        );

        //this.fileDetailAttributesTemplate.overwrite(this.getDetailsPanel().body, this.file.data);

        this.getAttributesPanel().setSource(file.get('attributes'));

        var tbar = this.getDockedComponent('tbar'),
            bbar = this.getDockedComponent('bbar');

        tbar.getComponent('pathField').setValue(file.get('folderPath'));
        tbar.getComponent('nameField').setValue(file.get('name'));
        tbar.getComponent('idField').setValue(file.get('id'));

        if (file.get('prevId')) {
            this.prev = {
                fileId: file.get('prevId'),
                fileVersion: file.get('prevVersion')
            };
            bbar.getComponent('prevBtn').show();
        } else {
            this.prev = null;
            bbar.getComponent('prevBtn').hide();
        }

        if (file.get('nextId')) {
            this.next = {
                fileId: file.get('nextId'),
                fileVersion: file.get('nextVersion')
            };
            bbar.getComponent('nextBtn').show();
        } else {
            this.next = null;
            bbar.getComponent('nextBtn').hide();
        }

        // versions
        if (file.hasVersions()) {
            this.getVersionsPanel().loadVersions(file.get('versions'));
            this.getVersionsPanel().show();
        } else {
            this.getVersionsPanel().hide();
        }

        this.getMetaGrid().loadMeta({
            fileId: file.get('id'),
            fileVersion: file.get('version')
        });
    }
});
