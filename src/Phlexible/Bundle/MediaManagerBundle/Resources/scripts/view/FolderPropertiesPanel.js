Ext.define('Phlexible.mediamanager.FolderPropertiesPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediamanager-folder-properties',

    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.properties,
    iconCls: Phlexible.Icon.get('property'),
    cls: 'p-mediamanager-folder-properties',
    disabled: true,
    bodyStyle: 'padding: 10px',

    initComponent: function () {
        this.html = 'Loading...';

        this.initMyTemplates();

        this.callParent(arguments);

        if (this.folderId) {
            this.loadData(this.folderId);
        }
    },

    initMyTemplates: function() {
        this.folderPropertiesTpl = new Ext.XTemplate(
            '<div style="padding: 10px;">',
            '<table border="0" cellpadding="0" cellspacing="7">',
            '<tr>',
            '<td>{[this.strings.type]}:</td>',
            '<td>{[this.strings[values.type]]}</div>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.path]}:</td>',
            '<td>{path}</td>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.size]}:</td>',
            '<td>{[Phlexible.Format.size(values.size)]} ({size} Bytes)</td>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.contents]}:</td>',
            '<td>{folders} {[values.folders == 1 ? this.strings.folder : this.strings.folders]}, {files} {[values.files == 1 ? this.strings.file : this.strings.files]}</td>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.create_date]}:</td>',
            '<td>{[Phlexible.Format.date(values.create_time)]}</td>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.created_by]}:</td>',
            '<td>{values.create_user}</td>',
            '</tr>',
            '<tpl if="values.modify_date">',
            '<tr>',
            '<td>{[this.strings.modify_date]}:</td>',
            '<td>{[Phlexible.Format.date(values.modify_time)]}</td>',
            '</tr>',
            '</tpl>',
            '<tpl if="values.modify_user">',
            '<tr>',
            '<td>{[this.strings.modified_by]}:</td>',
            '<td>{values.modify_user}</td>',
            '</tr>',
            '</tpl>',
            '</table>',
            '</div>',
            {
                strings: Phlexible.mediamanager.Strings
            }
        );
    },

    loadData: function (folderId) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_properties'),
            loadMask: true,
            params: {
                folderId: folderId
            },
            success: function (response) {
                data = Ext.decode(response.responseText);

                this.applyData(data);

                this.enable();
            },
            scope: this
        });
    },

    applyData: function (data) {
        this.folderPropertiesTpl.overwrite(this.el, data);
    }
});
