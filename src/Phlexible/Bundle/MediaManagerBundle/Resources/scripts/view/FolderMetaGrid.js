Ext.define('Phlexible.mediamanager.FolderMetaGrid', {
    extend: 'Phlexible.mediamanager.FileMetaGrid',
    alias: 'widget.mediamanager-folder-metas',

    title: Phlexible.metasets.Strings.folder_meta,

    right: Phlexible.mediamanager.Rights.FOLDER_MODIFY
});
