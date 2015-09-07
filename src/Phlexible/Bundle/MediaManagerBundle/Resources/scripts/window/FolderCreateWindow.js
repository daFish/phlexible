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

    folderName: '',

    initComponent: function() {
        this.items = [{
            xtype: 'displayfield',
            hideLabel: true,
            value: this.createDescriptionText
        },{
            xtype: 'textfield',
            flex: 1,
            fieldLabel: this.nameText,
            value: this.folderName,
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
        var folder = Ext.create('Phlexible.mediamanager.model.Folder', {
            text: this.getComponent(1).getValue(),
            name: this.getComponent(1).getValue(),
            leaf: true,
            createdAt: new Date(),
            createdBy: Phlexible.User.getUsername(),
            modifiedAt: new Date(),
            modifiedBy: Phlexible.User.getUsername(),
            rights: this.folder.get('rights')
        });

        this.folder.appendChild(folder);
        this.folder.expand();

        this.close();
    }
});
