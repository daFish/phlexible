/**
 * User theme option panel
 */
Ext.define('Phlexible.user.options.Theme', {
    extend: 'Ext.panel.Panel',
    xtype: 'user.options-theme',

    iconCls: Phlexible.Icon.get('image-empty'),
    bodyPadding: '15',
    border: true,

    descriptionText: '_description',
    saveText: '_save',
    cancelText: '_cancel',
    emptyText: '_no_themes',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();
        this.initMyListeners();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = {
            xtype: 'dataview',
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.gui.model.KeyValueIconCls',
                data: Phlexible.Config.get('set.themes')
            }),
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="theme-wrap" id="{key}">',
                '<div class="theme"><img src="/bundles/phlexibleuser/themes/{iconCls}" title="{value}"></div>',
                '<span>{value}</span></div>',
                '</tpl>',
                '<div class="x-clear"></div>'
            ),
            autoHeight: true,
            singleSelect: true,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.theme-wrap',
            emptyText: this.emptyText
        };
    },

    initMyListeners: function() {
        this.addListener({
            show: function (panel) {
                var record = panel.getComponent(0).getStore().findRecord('key', Phlexible.Config.get('user.theme'));

                if (record) {
                    panel.getComponent(0).select(record, false, true);
                }
            }
        });
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.saveText,
                iconCls: Phlexible.Icon.get(Phlexible.Icon.SAVE),
                handler: function() {
                    var view = this.getComponent(0),
                        records = view.getSelectionModel().getSelection(),
                        record;

                    if(records.length)
                    {
                        record = records[0];

                        if (Phlexible.User.getOptions().theme != record.get('key')) {
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('phlexible_options'),
                                method: 'PATCH',
                                params: {
                                    theme: record.get('key')
                                },
                                success: function(response){
                                    var data = Ext.decode(response.responseText);
                                    if (data.success) {
                                        Phlexible.User.getOptions().theme = record.get('key');
                                    }
                                    else {
                                        Phlexible.Notify.failure(data.msg);
                                    }
                                },
                                scope: this
                            });
                        }
                    }
                },
                scope: this
            }]
        }];
    }
});
