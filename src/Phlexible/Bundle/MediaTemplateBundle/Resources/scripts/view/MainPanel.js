Ext.define('Phlexible.mediatemplates.MainPanel', {
    extend: 'Ext.Panel',
    alias: 'widget.mediatemplates-main',

    title: Phlexible.mediatemplates.Strings.mediatemplates,
    strings: Phlexible.mediatemplates.Strings,
    iconCls: Phlexible.Icon.get('document-template'),
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
            width: 200,
            margin: '5 0 5 5',
            header: false,
            listeners: {
                templatechange: this.onTemplateChange,
                create: this.onTemplateCreate,
                scope: this
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
                border: false,
                listeners: {
                    paramsload: function () {},
                    paramssave: this.reloadStore,
                    scope: this
                }
            }/*, {
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
            }*/]
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
    },

    getTemplatePanelByType: function(type) {
        switch (type) {
            case 'image':
                return this.getImagePanel();

            case 'video':
                return this.getVideoPanel();

            case 'audio':
                return this.getAudioPanel();

            case 'pdf2swf':
                return this.getPdf2swfPanel();
        }

        return null;
    },

    onTemplateChange: function (r) {
        var activePanel = this.getTemplatePanelByType(r.get('type'));

        if (activePanel) {
            this.getCardPanel().getLayout().setActiveItem(activePanel);
            activePanel.loadParameters(r.get('key'));
        } else {
            Ext.MessageBox.alert('Warning', 'Unknown template');
        }
    },

    onTemplateCreate: function (templateId, templateTitle, templateType) {
        var activePanel = this.getTemplatePanelByType(templateType);

        if (activePanel) {
            this.getCardPanel().getLayout().setActiveItem(activePanel);
            activePanel.loadParameters(templateId, templateTitle);
        } else {
            Ext.MessageBox.alert('Warning', 'Unknown template');
        }
    }
});
