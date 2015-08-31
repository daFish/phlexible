Ext.define('Phlexible.mediatemplate.view.image.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.mediatemplate.view.image.Form'
    ],
    xtype: 'mediatemplate.image.main',

    layout: 'fit',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'mediatemplate.image.form',
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
                            Phlexible.Router.generate('mediatemplates_preview_image'),
                            function (data) {
                                return {
                                    tag: 'img',
                                    alt: 'Loading image preview',
                                    src: Phlexible.Router.generate('mediatemplates_preview_get', {file: data.file, dc: new Date().getTime()}),
                                    style: {
                                        border: '1px dotted gray'
                                    }
                                };
                            },
                            function (data) {
                                var s = '';
                                if (data.template) {
                                    s += data.template + ', ';
                                }
                                s += data.width + ' x ' + data.height;
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
        if (mediaTemplate && mediaTemplate.get('type') === 'image') {
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
