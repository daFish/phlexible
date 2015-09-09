Ext.define('Phlexible.mediamanager.view.FolderProperties', {
    extend: 'Ext.panel.Panel',
    xtype: 'mediamanager.folder-properties',

    iconCls: Phlexible.Icon.get('information'),
    cls: 'p-mediamanager-folder-properties',
    padding: 10,

    typeText: '_typeText',
    pathText: '_pathText',
    sizeText: '_sizeText',
    contentsText: '_contentsText',
    foldersText: '_folders',
    folderText: '_folder',
    filesText: '_files',
    fileText: '_file',
    createdAtText: '_createdAtText',
    createdByText: '_createdByText',
    modifiedAtText: '_modifiedAtText',
    modifiedByText: '_modifiedByText',

    initComponent: function () {
        this.tpl = this.createTpl();
        if (this.folder) {
            this.data = this.folder.data;
        } else {
            this.data = {
                name: '',
                path: '',
                size: 0,
                folders: 0,
                createUser: '',
                createTime: 0,
                modifyUser: '',
                modifyTime: 0
            }
        }

        this.callParent(arguments);
    },

    createTpl: function() {
        return new Ext.XTemplate(
            '<table border="0" cellpadding="0" cellspacing="7">',
            '<tr>',
            '<td>' + this.typeText + ':</td>',
            '<td>{type}</div>',
            '</tr>',
            '<tr>',
            '<td>' + this.pathText + ':</td>',
            '<td>{path}</td>',
            '</tr>',
            '<tr>',
            '<td>' + this.sizeText + ':</td>',
            '<td>{[Phlexible.Format.size(values.size)]} ({size} Bytes)</td>',
            '</tr>',
            '<tr>',
            '<td>' + this.contentsText + ':</td>',
            '<td>{folders} <tpl if="values.folders==1">' + this.folderText + '<tpl else>' + this.foldersText + '</tpl>, {files} <tpl if="values.folders==1">'+this.fileText+'<tpl else>' + this.filesText + '</tpl></td>',
            '</tr>',
            '<tr>',
            '<td>' + this.createdAtText + ':</td>',
            '<td>{[Phlexible.Format.date(values.createTime)]}</td>',
            '</tr>',
            '<tr>',
            '<td>' + this.createdByText + ':</td>',
            '<td>{createUser}</td>',
            '</tr>',
            '<tpl if="values.modifyTime">',
            '<tr>',
            '<td>' + this.modifiedAtText + ':</td>',
            '<td>{[Phlexible.Format.date(values.modifyTime)]}</td>',
            '</tr>',
            '</tpl>',
            '<tpl if="values.modifyUser">',
            '<tr>',
            '<td>' + this.modifiedByText + ':</td>',
            '<td>{modifyUser}</td>',
            '</tr>',
            '</tpl>',
            '</table>'
        );
    }
});
