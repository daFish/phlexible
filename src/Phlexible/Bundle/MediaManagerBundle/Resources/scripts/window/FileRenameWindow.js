Ext.define('Phlexible.mediamanager.FileRenameWindow', {
    extend: 'Ext.window.Window',

    title: Phlexible.mediamanager.Strings.rename_file,
    strings: Phlexible.mediamanager.Strings,
    iconCls: Phlexible.Icon.get(Phlexible.Icon.EDIT),
    layout: 'form',
    width: 400,
    height: 150,
    modal: true,
    resizable: false,

    initComponent: function() {
        this.items = [{
            xtype: 'displayfield',
            hideLabel: true,
            value: this.strings.rename_file_desc
        },{
            xtype: 'textfield',
            flex: 1,
            fieldLabel: this.strings.name,
            value: this.fileName
        }];

        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                '->',
                {
                    xtype: 'button',
                    text: this.strings.cancel,
                    handler: this.close,
                    scope: this
                },{
                    xtype: 'button',
                    text: this.strings.rename,
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
            url: Phlexible.Router.generate('mediamanager_file_patch', {fileId: this.fileId}),
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
