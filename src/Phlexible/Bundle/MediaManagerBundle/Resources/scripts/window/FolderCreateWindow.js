Ext.define('Phlexible.mediamanager.window.FolderCreateWindow', {
    extend: 'Ext.window.Window',

    title: '_FolderCreateWindow',
    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
    layout: 'form',
    width: 400,
    height: 150,
    modal: true,
    resizable: false,

    createDescriptionText: '_createDescriptionText',
    nameText: '_nameText',
    cancelText: '_cancelText',
    createText: '_createText',

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
                    text: this.createText,
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
            url: Phlexible.Router.generate('mediamanager_folder_create'),
            method: 'POST',
            params: {
                name: this.getComponent(1).getValue()
            },
            success: function(response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    Phlexible.Notify.success(data.msg);
                    this.fireEvent('success', data.data);
                    this.close();
                } else {
                    Phlexible.Notify.failure('Failure', data.msg);
                }
            },
            scope: this
        });
    }
});
