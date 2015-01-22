Ext.define('Phlexible.mediatemplate.view.image.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatemplates-image-main',

    title: '_MainPanel',
    header: false,
    layout: 'border',

    imageTemplateTitleText: '_imageTemplateTitleText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function () {
        this.items = [
            {
                xtype: 'mediatemplates-image-form',
                region: 'west',
                width: 320,
                margin: 5,
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
                xtype: 'mediatemplates-image-preview',
                region: 'center',
                margin: '5 5 5 0',
                header: false
            }
        ];
    },

    loadParameters: function (template_key) {
        this.getComponent(0).setTitle(Ext.String.format(this.imageTemplateTitleText, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});