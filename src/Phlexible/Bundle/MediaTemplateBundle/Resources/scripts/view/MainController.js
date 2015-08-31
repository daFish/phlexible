Ext.define('Phlexible.mediatemplate.view.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.mediatemplate.main',

    keyText: '_keyText',

    getTemplatePanelByType: function(type) {
        return this.getView().getCardPanel().getComponent(type);
    },

    onSelectionChange: function(grid, mediaTemplates) {
        if (mediaTemplates.length === 1) {
            var card = this.getTemplatePanelByType(mediaTemplates[0].get('type'));

            if (card) {
                this.getView().getCardPanel().getLayout().setActiveItem(card);
            } else {
                Ext.MessageBox.alert('Warning', 'Unknown template');
            }
        }
    },

    onPreviewTemplate: function(mediaTemplate, url, domHelper, resultFormatter, file) {
        this.getView().getPreviewPanel().requestPreview(mediaTemplate, url, domHelper, resultFormatter, file);
    },

    onSave: function() {
        this.getView().getViewModel().getStore('templates').sync();
    },

    onCreateTemplate: function (type) {
        if (!type || (type != 'image' && type != 'video' && type != 'audio')) {
            return;
        }

        Ext.MessageBox.prompt(this.keyText, this.keyText, function (btn, key) {
            if (btn !== 'ok') {
                return;
            }

            var mediaTemplate = Ext.create('Phlexible.mediatemplate.model.ImageTemplate', {
                type: type,
                cache: type === 'video' || type === 'audio' ? true : false,
                system: false,
                storage: 'default',
                revision: 1,
                createdAt: Ext.Date.format(new Date, "Y-m-d H:i:s"),
                modifiedAt: Ext.Date.format(new Date, "Y-m-d H:i:s")
            });
            this.getView().getViewModel().getStore('templates').add(mediaTemplate);
            mediaTemplate.set('key', key);
            this.getView().getListPanel().getSelectionModel().select(mediaTemplate);
        }, this);
    }
});
