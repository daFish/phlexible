Ext.define('Phlexible.mediamanager.view.FileMeta', {
    extend: 'Phlexible.mediamanager.view.AbstractMeta',
    requires: [
        'Phlexible.mediamanager.view.AbstractMeta',
        'Phlexible.mediamanager.model.FileMetaSet',
        'Phlexible.mediamanager.model.FileMeta'
    ],
    xtype: 'mediamanager.file-meta',

    getMetaRoute: function() {
        return 'phlexible_api_mediamanager_get_file_metas';
    },

    getMetasetRoute: function() {
        return 'phlexible_api_mediamanager_get_file_metasets';
    },

    getMetasetModel: function() {
        return 'Phlexible.mediamanager.model.FileMetaSet';
    },

    getCheckRight: function() {
        return Phlexible.mediamanager.Rights.FILE_MODIFY;
    },

    loadFile: function(file) {
        this.loadMeta({fileId: file.getId()});
        this.sourceItem = file;
    },

    createMetaGridConfig: function(setId, title, fieldData, small) {
        return {
            xtype: 'mediamanager.file-metas',
            setId: setId,
            title: title,
            height: 180,
            border: false,
            small: small,
            fieldData: fieldData
        };
    }
});
