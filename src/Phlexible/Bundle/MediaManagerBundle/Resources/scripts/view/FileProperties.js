Ext.define('Phlexible.mediamanager.view.FileProperties', {
    extend: 'Ext.panel.Panel',
    xtype: 'mediamanager.file-properties',

    iconCls: Phlexible.Icon.get('information'),
    cls: 'p-mediamanager-file-properties',

    nameText: '_nameText',
    typeText: '_typeText',
    sizeText: '_sizeText',
    createdAtText: '_createdAtText',
    createdByText: '_createdByText',
    modifiedAtText: '_modifiedAtText',
    modifiedByText: '_modifiedByText',

    initComponent: function () {
        this.tpl = this.createTpl();
        if (this.file) {
            this.data = this.file.data;
        } else {
            this.data = {
                name: '',
                mediaType: '',
                createUser: '',
                createTime: 0
            }
        }

        this.callParent(arguments);
    },

    createTpl: function() {
        return new Ext.XTemplate(
            '<div>',
            '<div style="color: grey;">' + this.nameText + ':</div>',
            //'<div>{[Ext.String.ellipsis(values.name, 40)]}</div>',
            '<div>{name}</div>',
            '<div style="color: grey; padding-top: 5px;">' + this.typeText + ':</div>',
            '<div>{mediaType}</div>',
            '<div style="color: grey; padding-top: 5px;">' + this.sizeText + ':</div>',
            '<div>{[Phlexible.Format.size(values.size)]}</div>',
            '<div style="color: grey; padding-top: 5px;">' + this.createdByText + ':</div>',
            '<div>{createUser}</div>',
            '<div style="color: grey; padding-top: 5px;">' + this.createdAtText + ':</div>',
            '<div>{[Phlexible.Format.date(values.createTime)]}</div>',
            '</div>'
        );
    }
});
