Ext.define('Phlexible.mediatype.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatype-main',

    title: Phlexible.mediatype.Strings.media_types,
    iconCls: Phlexible.Icon.get('image-share'),
    layout: 'border',
    border: false,

    initComponent: function () {
        this.items = [
            {
                xtype: 'mediatype-list',
                region: 'center',
                header: false,
                padding: 5,
                listeners: {
                    mediaTypeChange: function (r) {
                        var mimetypes = null;
                        if (r) {
                            mimetypes = r.get('mimetypes');
                        }
                        this.getComponent('mimetypes').loadMimetypes(mimetypes);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatype-mimetypes',
                itemId: 'mimetypes',
                region: 'east',
                padding: '5 5 5 0',
                width: 400
            }
        ];

        this.callParent(arguments);
    },

    loadParams: function () {

    }
});
