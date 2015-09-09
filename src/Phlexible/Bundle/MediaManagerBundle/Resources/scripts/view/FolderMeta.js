Ext.define('Phlexible.mediamanager.view.FolderMeta', {
    extend: 'Phlexible.mediamanager.view.FileMeta',
    xtype: 'mediamanager.folder-meta',

    checkRight: Phlexible.mediamanager.Rights.FOLDER_MODIFY,

    initMyUrls: function () {
        this.routes = {
            load: 'phlexible_api_mediamanager_get_folder_metasets'
        };

        this.metasetUrls = {
            list: 'mediamanager_folder_meta_sets_list',
            save: 'mediamanager_folder_meta_sets_save',
            available: 'phlexible_api_metaset_get_metasets'
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
