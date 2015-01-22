Ext.define('Phlexible.mediatemplate.view.audio.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatemplates-audio-main',

    title: '_MainPanel',
    layout: 'border',

    audioTemplateTitleText: '_audioTemplateTitleText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplates-audio-form',
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
                xtype: 'mediatemplates-audio-preview',
                region: 'center',
                header: false
            }
        ];
    },

    loadParameters: function (template_key) {
        this.setTitle(Ext.String.format(this.audioTemplateTitleText, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});