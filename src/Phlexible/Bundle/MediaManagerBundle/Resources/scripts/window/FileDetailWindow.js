Ext.define('Phlexible.mediamanager.FileDetailWindow', {
    extend: 'Ext.window.Window',

    title: 'File Details',
    strings: Phlexible.mediamanager.Strings,
    iconCls: Phlexible.Icon.get('document'),
    width: 900,
    height: 600,
    layout: 'fit',
    cls: 'p-mediamanager-file-detail-window',
    bodyStyle: 'padding: 5px',
    modal: true,
    constrainHeader: true,
    maximizable: true,

    file_id: null,
    file_version: null,
    file_name: null,
    document_type_key: null,
    asset_type: null,
    cache: null,
    rights: [],

    initComponent: function () {
        this.initMyTemplates();
        this.initMyTabs();
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyTemplates: function() {
        this.fileDetailAttributesTemplate = new Ext.XTemplate(
            '<div style="padding: 4px 4px 8px 4px;">',
            '<div>',
            '<div><div style="float: left; width: 120px; color: grey;">{[Phlexible.mediamanager.Strings.name]}:</div> {[values.name.shorten(80)]}</div>',
            '<div><div style="float: left; width: 120px; color: grey;">{[Phlexible.mediamanager.Strings.type]}:</div> {document_type_key}</div>',
            '<div><div style="float: left; width: 120px; color: grey;">{[Phlexible.mediamanager.Strings.size]}:</div> {[Phlexible.Format.size(values.size)]}</div>',
            '<div><div style="float: left; width: 120px; color: grey;">{[Phlexible.mediamanager.Strings.created_by]}:</div> {create_user_id}</div>',
            '<div><div style="float: left; width: 120px; color: grey;">{[Phlexible.mediamanager.Strings.create_date]}:</div> {[Phlexible.Format.date(values.create_time)]}</div>',
            '</div>',
            '</div>'
        );
    },

    initMyItems: function() {
        this.items = [
            {
                region: 'west',
                border: false,
                width: 280,
                items: [
                    {
                        xtype: 'mediamanager-file-preview',
                        region: 'west',
                        height: 300,
                        border: false,
                        file_id: this.file_id,
                        file_version: this.file_version,
                        file_name: this.file_name,
                        document_type_key: this.document_type_key,
                        asset_type: this.asset_type,
                        cache: this.cache
                    },
                    {
                        header: false,
                        border: false,
                        autoHeight: true
                    }
                ]
            },
            {
                region: 'center',
                xtype: 'tabpanel',
                deferredRender: false,
                activeTab: 1,
                items: this.tabs
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [
                this.strings.folder,
                {
                    xtype: 'textfield',
                    value: '/test/bla/',
                    width: 250
                },
                ' ',
                ' ',
                this.strings.name,
                {
                    xtype: 'textfield',
                    value: 'blubb.png',
                    width: 310
                },
                ' ',
                ' ',
                this.strings.id,
                {
                    xtype: 'textfield',
                    value: '123123abc123123',
                    width: 220
                }
            ]
        },{
            xtype: 'toolbar',
            dock: 'bottom',
            items: [
                '->',
                {
                    text: 'Previous file',
                    iconCls: Phlexible.Icon.get('arrow-180'),
                    hidden: true,
                    handler: function () {
                        this.file_id = this.prev.fileId;
                        this.file_version = this.prev.fileVersion;
                        this.load();
                    },
                    scope: this
                }, ' ', {
                    text: 'Next file',
                    iconCls: Phlexible.Icon.get('arrow'),
                    hidden: true,
                    handler: function () {
                        this.file_id = this.next.fileId;
                        this.file_version = this.next.fileVersion;
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
                xtype: 'fileversionspanel',
                region: 'center',
                file_id: this.file_id,
                file_version: this.file_version,
                listeners: {
                    versionSelect: this.onVersionSelect,
                    versionDownload: function (file_id, file_version) {
                        var href = Phlexible.Router.generate('mediamanager_download_file', {id: file_id})

                        if (file_version) {
                            href += '/' + file_version;
                        }

                        document.location.href = href;
                    },
                    scope: this
                }
            },
            {
                xtype: 'propertygrid',
                title: this.strings.attributes,
                iconCls: Phlexible.Icon.get('property'),
                source: {},
                viewConfig: {
                    emptyText: this.strings.no_attribute_values,
                    forceFit: true
                },
                hidden: true
            },
            {
                xtype: 'mediamanager-filemeta',
                border: false,
                listeners: {
                    render: function (c) {
                        c.setRights(this.rights);
                    },
                    scope: this
                }
            }/*,{
             title: 'Rights',
             iconCls: 'p-mediamanager-file_rights-icon',
             hidden: true
             },{
             title: 'Preview',
             iconCls: 'p-mediamanager-file_preview-icon',
             hidden: true
             }*/
        ];
    },

    getToolbar: function() {
        return this.getComponent(0);
    },

    getLeft: function () {
        return this.getComponent(1);
    },

    getPreviewPanel: function () {
        return this.getLeft().getComponent(0);
    },

    getDetailsPanel: function () {
        return this.getLeft().getComponent(1);
    },

    getTabPanel: function () {
        return this.getComponent(2);
    },

    getVersionsPanel: function () {
        return this.getTabPanel().getComponent(0);
    },

    getAttributesPanel: function () {
        return this.getTabPanel().getComponent(1);
    },

    getMetaGrid: function () {
        return this.getTabPanel().getComponent(2);
    },

    onVersionSelect: function (file_id, file_version, file_name, folder_id, document_type_key, asset_type) {
        this.getPreviewPanel().load(file_id, file_version, file_name, document_type_key, asset_type);
        this.loadProperties(file_id, file_version);
    },

    loadProperties: function (file_id, file_version) {
        this.getMetaGrid().loadMeta({
            file_id: file_id,
            file_version: file_version
        });

        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_file_properties', {id: file_id, version: file_version}),
            success: function (response) {
                var data = Ext.decode(response.responseText);
                this.setTitle(data.name);
                this.setIconClass(Phlexible.documenttypes.DocumentTypes.getClass(data.documenttypeKey) + "-small");
                this.getPreviewPanel().load(data.id, data.version, data.name, data.documenttypeKey);
                this.fileDetailAttributesTemplate.overwrite(this.getDetailsPanel().body, data.detail);
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

    load: function () {
        // properties
        this.loadProperties(this.file_id, this.file_version);

        // versions
        this.getVersionsPanel().loadFile(this.file_id, this.file_version);

        if (this.rendered) this.getComponent(1).getComponent(1).setRights(this.rights);
    },

    show: function () {
        this.load();
        this.callParent(arguments);
    }
});
