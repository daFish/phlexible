Ext.define('Phlexible.mediamanager.FolderMeta', {
    extend: 'Phlexible.mediamanager.FileMeta',
    alias: 'widget.mediamanager-folder-meta',

    title: Phlexible.mediamanager.Strings.folder_meta,

    checkRight: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initMyUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_folder_meta'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_folder_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_sets_save'),
            available: Phlexible.Router.generate('metasets_sets_list')
        };
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager-folder-metas',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            data: fields
        };
    }
});
