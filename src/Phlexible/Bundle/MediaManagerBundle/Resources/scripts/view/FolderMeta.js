Ext.define('Phlexible.mediamanager.view.FolderMeta', {
    extend: 'Phlexible.mediamanager.view.AbstractMeta',
    requires: [
        'Phlexible.mediamanager.view.AbstractMeta',
        'Phlexible.mediamanager.model.FolderMetaSet',
        'Phlexible.mediamanager.model.FolderMeta'
    ],
    xtype: 'mediamanager.folder-meta',

    getMetaRoute: function() {
        return 'phlexible_api_mediamanager_get_folder_metas';
    },

    getMetasetRoute: function() {
        return 'phlexible_api_mediamanager_get_folder_metasets';
    },

    getMetasetModel: function() {
        return 'Phlexible.mediamanager.model.FolderMetaSet';
    },

    getCheckRight: function() {
        return Phlexible.mediamanager.Rights.FOLDER_MODIFY;
    },

    loadFolder: function(folder) {
        this.loadMeta({folderId: folder.getId()});
        this.sourceItem = folder;
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
