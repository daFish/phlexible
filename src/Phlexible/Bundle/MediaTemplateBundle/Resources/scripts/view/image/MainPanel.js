Ext.define('Phlexible.mediatemplates.image.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatemplates-image-main',

    title: Phlexible.mediatemplates.Strings.image_template,
    header: false,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

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
        this.getComponent(0).setTitle(Ext.String.format(this.strings.image_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});