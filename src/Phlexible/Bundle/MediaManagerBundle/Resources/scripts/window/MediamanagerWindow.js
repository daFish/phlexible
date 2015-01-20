Ext.define('Phlexible.mediamanager.MediamanagerWindow', {
    extend: 'Ext.window.Window',

    title: 'Mediamanager',
    iconCls: 'p-mediamanager-component-icon',
    width: 800,
    height: 600,
    layout: 'fit',
    modal: true,
    border: false,
    constrainHeader: true,

    mode: '',
    params: {},

    /**
     * @event fileSelectWindow
     */
    /**
     *
     */
    initComponent: function () {
        if (!this.params.start_folder_path && Phlexible.mediamanager.lastParams && Phlexible.mediamanager.lastParams.start_folder_path) {
            this.params.start_folder_path = Phlexible.mediamanager.lastParams.start_folder_path;
        }

        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediamanager-main',
                noTitle: true,
                mode: this.mode,
                params: this.params,
                listeners: {
                    fileSelect: function (fileId, fileVersion, fileName, folderId) {
                        this.fireEvent('fileSelectWindow', this, fileId, fileVersion, fileName, folderId);
                    },
                    scope: this
                }
            }
        ];
    }
});
