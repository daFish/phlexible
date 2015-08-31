Ext.define('Phlexible.mediatemplate.view.audio.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.audio.Form'
    ],
    xtype: 'mediatemplate.audio.main',

    layout: 'fit',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.audio.form',
                itemId: 'form',
                border: false,
                listeners: {
                    save: function () {
                        this.fireEvent('save');
                    },
                    preview: function (file) {
                        this.fireEvent(
                            'preview',
                            this.mediaTemplate,
                            Phlexible.Router.generate('mediatemplates_preview_audio'),
                            function (data) {
                                return {
                                    tag: 'audio',
                                    autoplay: 'autoplay',
                                    controls: 'controls',
                                    children: [
                                        {
                                            tag: 'source',
                                            src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                                            type: data.mimetype
                                        }
                                    ]
                                }
                            },
                            function (data) {
                                var s = '';
                                if (data.template) {
                                    s += data.template;
                                }
                                if (data.format) {
                                    s += ', ' + data.format;
                                }
                                if (data.size) {
                                    s += ', ' + Phlexible.Format.size(data.size);
                                }
                                return s;
                            },
                            file
                        );
                    },
                    scope: this
                }
            }
        ];
    },

    setMediaTemplate: function(mediaTemplate) {
        if (mediaTemplate && mediaTemplate.get('type') === 'audio') {
            this.mediaTemplate = mediaTemplate;
            this.getComponent('form').enable();
            this.show();
        } else {
            this.mediaTemplate = null;
            this.getComponent('form').disable();
        }
    },

    getMediaTemplate: function() {
        return this.mediaTemplate;
    }
});
