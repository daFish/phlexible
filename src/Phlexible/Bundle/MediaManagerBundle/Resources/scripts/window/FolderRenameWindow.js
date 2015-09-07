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
            value: this.folder.get('name'),
            allowBlank: false
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
        this.folder.set('text', this.getComponent(1).getValue());
        this.folder.set('name', this.getComponent(1).getValue());

        this.close();
    }
});
