Ext.define('Phlexible.mediamanager.view.FolderMeta', {
    extend: 'Phlexible.mediamanager.view.FileMeta',
    xtype: 'mediamanager.folder-meta',

    checkRight: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initMyUrls: function () {
        this.urls = {
            load: Phlexible.Router.generate('mediamanager_folder_meta'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_save')
        };

        this.metasetUrls = {
            list: Phlexible.Router.generate('mediamanager_folder_meta_sets_list'),
            save: Phlexible.Router.generate('mediamanager_folder_meta_sets_save'),
            available: Phlexible.Router.generate('phlexible_api_metaset_get_metasets')
        };
    },

    createMetaGridConfig: function(setId, title, fields, small) {
        return {
            xtype: 'mediamanager.folder-metas',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            data: fields
        };
    }
});
