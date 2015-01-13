Ext.define('Phlexible.mediatemplates.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.mediatemplates-main',

    title: Phlexible.mediatemplates.Strings.mediatemplates,
    strings: Phlexible.mediatemplates.Strings,
    iconCls: 'p-mediatemplate-component-icon',
    layout: 'border',
    border: false,

    loadParams: function () {
    },

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'mediatemplates-list',
            region: 'west',
            itemId: 'list',
            width: '200',
            listeners: {
                templatechange: function (r) {
                    switch (r.get('type')) {
                        case 'image':
                            this.getCardPanel().getLayout().setActiveItem(0);
                            this.getImagePanel().loadParameters(r.get('key'));
                            break;

                        case 'video':
                            this.getCardPanel().getLayout().setActiveItem(1);
                            this.getVideoPanel().loadParameters(r.get('key'));
                            break;

                        case 'audio':
                            this.getCardPanel().getLayout().setActiveItem(2);
                            this.getAudioPanel().loadParameters(r.get('key'));
                            break;

                        case 'pdf':
                            this.getCardPanel().getLayout().setActiveItem(3);
                            this.getPdf2swfPanel().loadParameters(r.get('key'));
                            break;

                        default:
                            Ext.MessageBox.alert('Warning', 'Unknown template');
                    }

                },
                create: function (template_id, template_title, template_type) {
                    switch (template_type) {
                        case 'image':
                            this.imagePanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(0);
                            break;

                        case 'video':
                            this.videoFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(1);
                            break;

                        case 'audio':
                            this.audioFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(2);
                            break;

                        case 'pdf':
                            this.pdfFormPanel.loadParameters(template_id, template_title);
                            this.cardPanel.getLayout().setActiveItem(3);
                            break;

                        default:
                            Ext.MessageBox.alert('Warning', 'Unknown template');
                    }
                },
                scope: this,
            }
        }, {
            region: 'center',
            layout: 'card',
            itemId: 'cards',
            activeItem: 0,
            border: false,
            items: [{
                xtype: 'mediatemplates-image-main',
                itemId: 'image',
                listeners: {
                    paramsload: function () {},
                    paramssave: this.reloadStore,
                    scope: this
                }
            }, {
                xtype: 'mediatemplates-video-main',
                itemId: 'video',
                listeners: {
                    paramsload: function () {},
                    paramssave: this.reloadStore,
                    scope: this
                }
            }, {
                xtype: 'mediatemplates-audio-main',
                itemId: 'audio',
                listeners: {
                    paramsload: function () {},
                    paramssave: this.reloadStore,
                    scope: this
                }
            }, {
                xtype: 'mediatemplates-pdf2swf-main',
                itemId: 'pdf2swf',
                listeners: {
                    paramsload: function () {},
                    paramssave: this.reloadStore,
                    scope: this
                }
            }]
        }];
    },

    reloadStore: function() {
        this.getListPanel().getStore().reload();
    },

    getListPanel: function() {
        return this.getComponent('list');
    },

    getCardPanel: function() {
        return this.getComponent('cards');
    },

    getImagePanel: function() {
        return this.getCardPanel().getComponent('image');
    },

    getVideoPanel: function() {
        return this.getCardPanel().getComponent('video');
    },

    getAudioPanel: function() {
        return this.getCardPanel().getComponent('audio');
    },

    getPdf2swfPanel: function() {
        return this.getCardPanel().getComponent('pdf2swf');
    }
});
