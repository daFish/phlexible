Ext.define('Phlexible.mediamanager.window.FolderRenameWindow', {
    extend: 'Ext.window.Window',

    title: '_FolderRenameWindow',
    iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
    layout: 'form',
    width: 400,
    height: 150,
    modal: true,
    resizable: false,

    renameDescriptionText: '_renameDescriptionText',
    nameText: '_nameText',
    cancelText: '_cancelText',
    renameText: '_renameText',

    initComponent: function() {
        this.items = [{
            xtype: 'displayfield',
            hideLabel: true,
            value: this.renameDescriptionText
        },{
            xtype: 'textfield',
            flex: 1,
            fieldLabel: this.nameText,
            value: this.folderName
        }];

        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                '->',
                {
                    xtype: 'button',
                    text: this.cancelText,
                    handler: this.close,
                    scope: this
                },{
                    xtype: 'button',
                    text: this.renameText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                    handler: this.submit,
                    scope: this
                }
            ]
        }];

        this.callParent(arguments);
    },

    submit: function() {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('mediamanager_folder_patch', {folderId: this.folderId}),
            method: 'PATCH',
            params: {
                name: this.getComponent(1).getValue()
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.Notify.success(data.msg);
                    this.fireEvent('success', data.data)
                    this.close();
                } else {
                    Phlexible.Notify.failure('Failure', data.msg);
                }
            },
            scope: this
        })
    }
});
