Ext.define('Phlexible.mediatemplate.view.video.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatemplates-video-main',

    title: '_MainPanel',
    layout: 'border',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplates-video-form',
                region: 'west',
                width: 320,
                header: false,
                listeners: {
                    paramsload: function () {
                    },
                    paramssave: function () {
                        this.fireEvent('paramssave');
                    },
                    preview: function (params, debug) {
                        this.getComponent(1).createPreview(params, debug);
                    },
                    scope: this
                }
            },
            {
                xtype: 'mediatemplates-video-preview',
                region: 'center',
                header: false
            }
        ];
    },

    loadParameters: function (template_key) {
        this.setTitle(Ext.String.format(this.videoTemplateTitleText, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});