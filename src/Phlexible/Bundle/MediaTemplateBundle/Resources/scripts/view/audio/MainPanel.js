Ext.define('Phlexible.mediatemplates.audio.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.mediatypes-audio-main',

    title: Phlexible.mediatemplates.Strings.audio_template,
    strings: Phlexible.mediatemplates.Strings,
    layout: 'border',

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
        this.setTitle(Ext.String.format(this.strings.audio_template_title, template_key));

        this.getComponent(0).loadParameters(template_key);
    }
});