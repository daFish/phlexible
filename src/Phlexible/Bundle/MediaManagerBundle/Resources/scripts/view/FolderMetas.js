Ext.define('Phlexible.mediamanager.view.FolderMetas', {
    extend: 'Phlexible.mediamanager.view.FileMetas',
    requires: [
        'Phlexible.mediamanager.view.FileMetas'
    ],
    xtype: 'mediamanager.folder-metas',

    title: '_FolderMetaGrid',

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY
});
