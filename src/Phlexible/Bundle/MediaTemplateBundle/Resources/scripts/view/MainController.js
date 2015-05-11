Ext.define('Phlexible.mediatemplate.view.MainController', {
    extend: 'Ext.app.ViewController',

    alias: 'controller.mediatemplate.main',

    getTemplatePanelByType: function(type) {
        return this.getView().getCardPanel().getComponent(type);
    },

    onLoadTemplate: function (mediaTemplate) {
        var activePanel = this.getTemplatePanelByType(mediaTemplate.get('type'));

        if (activePanel) {
            this.getView().getCardPanel().getLayout().setActiveItem(activePanel);
            activePanel.loadParameters(mediaTemplate.get('key'), mediaTemplate.get('parameters'));
        } else {
            Ext.MessageBox.alert('Warning', 'Unknown template');
        }
    },

    onSaveTemplate: function() {
        this.getView().getListPanel().getStore().reload();
    },

    onCreateTemplate: function (type) {
        if (!type || (type != 'image' && type != 'video' && type != 'audio')) {
            return;
        }

        Ext.MessageBox.prompt('_title', '_title', function (btn, key) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('phlexible_api_mediatemplate_post_mediatemplates'),
                params: {
                    type: type,
                    key: key
                },
                success: function (response) {
                    var data = Ext.decode(response.responseText);
                    if (data.success) {
                        Phlexible.success(data.msg);

                        // store reload
                        this.store.reload({
                            callback: function (template_id) {
                                var mediaTemplate = this.store.getById(template_id);
                                var index = this.store.indexOf(r);
                                this.selModel.selectRange(index);
                                this.fireEvent('create', r.get('key'), r.get('type'));
                            }.createDelegate(this, [data.id])
                        });
                    } else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope: this

            });
        }, this);

        alert("bla");

        var activePanel = this.getTemplatePanelByType(templateType);

        if (activePanel) {
            this.getView().getCardPanel().getLayout().setActiveItem(activePanel);
            activePanel.loadParameters(templateId, templateTitle);
        } else {
            Ext.MessageBox.alert('Warning', 'Unknown template');
        }
    }
});
