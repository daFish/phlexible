Ext.define('Phlexible.mediamanager.FolderPropertiesPanel', {
    extend: 'Ext.Component',
    alias: 'widget.mediamanager-folder-properties',

    strings: Phlexible.mediamanager.Strings,
    title: Phlexible.mediamanager.Strings.properties,
    iconCls: Phlexible.Icon.get('property'),
    cls: 'p-mediamanager-folder-properties',
    padding: 10,

    initComponent: function () {
        this.initMyTemplates();
        this.data = this.folder.data;

        this.callParent(arguments);
    },

    initMyTemplates: function() {
        this.tpl = new Ext.XTemplate(
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
            '<td>{[Phlexible.Format.date(values.createTime)]}</td>',
            '</tr>',
            '<tr>',
            '<td>{[this.strings.created_by]}:</td>',
            '<td>{createUser}</td>',
            '</tr>',
            '<tpl if="values.modify_date">',
            '<tr>',
            '<td>{[this.strings.modify_date]}:</td>',
            '<td>{[Phlexible.Format.date(values.modifyTime)]}</td>',
            '</tr>',
            '</tpl>',
            '<tpl if="values.modify_user">',
            '<tr>',
            '<td>{[this.strings.modified_by]}:</td>',
            '<td>{modifyUser}</td>',
            '</tr>',
            '</tpl>',
            '</table>',
            {
                strings: Phlexible.mediamanager.Strings
            }
        );
    }
});
