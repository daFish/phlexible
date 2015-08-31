Ext.define('Phlexible.mediatemplate.view.video.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.video.Form'
    ],
    xtype: 'mediatemplate.video.main',

    layout: 'fit',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.video.form',
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
                            Phlexible.Router.generate('mediatemplates_preview_video'),
                            function (data) {
                                return {
                                    tag: 'video',
                                    autoplay: 'autoplay',
                                    controls: 'controls',
                                    width: data.width,
                                    height: data.height,
                                    children: [
                                        {
                                            tag: 'source',
                                            src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                                            type: data.mimetype
                                        }
                                    ]
                                };
                            },
                            function (data) {
                                var s = '';
                                if (data.template) {
                                    s += data.template;
                                }
                                if (data.width && data.height) {
                                    s += ', ' + data.width + ' x ' + data.height;
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
        if (mediaTemplate && mediaTemplate.get('type') === 'video') {
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
