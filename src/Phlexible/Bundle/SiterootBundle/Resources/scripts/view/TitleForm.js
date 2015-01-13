Ext.define('Phlexible.siteroots.TitleForm', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.siteroots-titles',

    title: Phlexible.siteroots.Strings.titles,
    strings: Phlexible.siteroots.Strings,
    border: false,

    initComponent: function () {
        this.initMyTasks();
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.task1 = new Ext.util.DelayedTask(this.updateDefaultPreview, this);
        this.task2 = new Ext.util.DelayedTask(this.updateHomePreview, this);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'form',
                itemId: 'titles',
                border: false,
                bodyStyle: 'padding: 5px;',
                xlabelAlign: 'top',
                items: []
            },
            {
                xtype: 'form',
                itemId: 'custom_titles',
                border: false,
                bodyStyle: 'padding: 5px;',
                labelAlign: 'top',
                items: [
                    {
                        xtype: 'panel',
                        itemId: 'head_title',
                        title: this.strings.default_custom_title,
                        layout: 'form',
                        autoScroll: true,
                        bodyStyle: 'padding: 5px;',
                        style: 'padding-bottom: 5px;',
                        items: [
                            {
                                fieldLabel: this.strings.title,
                                itemId: 'head_title_text',
                                name: 'head_title',
                                xtype: 'textfield',
                                anchor: '-50',
                                emptyText: this.strings.no_customized_title,
                                enableKeyEvents: true,
                                listeners: {
                                    keyup: function (field, event) {
                                        if (event.getKey() == event.ENTER) {
                                            this.task1.cancel();
                                            this.updateDefaultPreview();
                                            return;
                                        }

                                        this.task1.delay(500);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'fieldcontainer',
                                itemId: 'head_title_example_wrap',
                                fieldLabel: this.strings.example,
                                layout: 'hbox',
                                items: [{
                                    itemId: 'head_title_example',
                                    name: 'example',
                                    xtype: 'textfield',
                                    flex: 1,
                                    readOnly: true
                                },{
                                    xtype: 'button',
                                    itemId: 'head_title_example_button',
                                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                                    width: 30,
                                    handler: this.updateDefaultPreview,
                                    scope: this
                                }]
                            }
                        ]
                    },
                    {
                        xtype: 'panel',
                        itemId: 'start_head_title',
                        title: this.strings.start_custom_title,
                        layout: 'form',
                        bodyStyle: 'padding: 5px;',
                        style: 'padding-bottom: 5px;',
                        items: [
                            {
                                xtype: 'textfield',
                                itemId: 'start_head_title_text',
                                name: 'start_head_title',
                                fieldLabel: this.strings.title,
                                anchor: '-50',
                                emptyText: this.strings.no_customized_start_title,
                                enableKeyEvents: true,
                                listeners: {
                                    keyup: function (field, event) {
                                        if (event.getKey() == event.ENTER) {
                                            this.task2.cancel();
                                            this.updateHomePreview();
                                            return;
                                        }

                                        this.task2.delay(500);
                                    },
                                    scope: this
                                }
                            },
                            {
                                xtype: 'fieldcontainer',
                                itemId: 'start_head_title_example_wrap',
                                fieldLabel: this.strings.example,
                                layout: 'hbox',
                                items: [{
                                    xtype: 'textfield',
                                    itemId: 'start_head_title_example',
                                    name: 'start_example',
                                    flex: 1,
                                    readOnly: true
                                },{
                                    xtype: 'button',
                                    itemId: 'start_head_title_example_button',
                                    iconCls: Phlexible.Icon.get(Phlexible.Icon.RELOAD),
                                    handler: this.updateHomePreview,
                                    scope: this
                                }]
                            }
                        ]
                    },
                    {
                        xtype: 'panel',
                        title: this.strings.legend,
                        bodyStyle: 'padding: 5px;',
                        items: [
                            {
                                xtype: 'dataview',
                                store: Ext.create('Ext.data.Store', {
                                    fields: ['placeholder', 'title'],
                                    proxy: {
                                        type: 'ajax',
                                        url: Phlexible.Router.generate('siteroots_customtitle_placeholders'),
                                        simpleSortMode: true,
                                        reader: {
                                            type: 'json',
                                            rootProperty: 'placeholders'
                                        }
                                    },
                                    autoLoad: true
                                }),
                                tpl: new Ext.XTemplate(
                                    '<tpl for=".">',
                                    '<span style="padding-right: 15px;"><b>{placeholder}</b> {title}</span>',
                                    '</tpl>'
                                ),
                                autoHeight: true,
                                singleSelect: true,
                                overItemClass: 'xxx',
                                itemSelector: 'div'
                            }
                        ]
                    }
                ]
            }
        ];

        for (var i = 0; i < Phlexible.App.getConfig().get('set.language.frontend').length; i++) {
            this.items[0].items.push({
                fieldLabel: Phlexible.Icon.inline(Phlexible.App.getConfig().get('set.language.frontend')[i][2]) + ' ' + Phlexible.App.getConfig().get('set.language.frontend')[i][1],
                name: Phlexible.App.getConfig().get('set.language.frontend')[i][0],
                xtype: 'textfield',
                flex: 1,
                allowBlank: false
            });
        }
    },

    getTitlePanels: function() {
        return this.getComponent('titles');
    },

    getCustomTitlePanels: function() {
        return this.getComponent('custom_titles');
    },

    getHeadTitlePanel: function() {
        return this.getCustomTitlePanels().getComponent('head_title');
    },

    getHeadTitleField: function() {
        return this.getHeadTitlePanel().getComponent('head_title_text');
    },

    getHeadTitleExampleWrap: function() {
        return this.getHeadTitlePanel().getComponent('head_title_example_wrap');
    },

    getHeadTitleExampleField: function() {
        return this.getHeadTitleExampleWrap().getComponent('head_title_example');
    },

    getHeadTitleExampleButton: function() {
        return this.getHeadTitleExampleWrap().getComponent('head_title_example_button');
    },

    getStartHeadTitlePanel: function() {
        return this.getCustomTitlePanels().getComponent('start_head_title');
    },

    getStartHeadTitleField: function() {
        return this.getStartHeadTitlePanel().getComponent('start_head_title_text');
    },

    getStartHeadTitleExampleWrap: function() {
        return this.getStartHeadTitlePanel().getComponent('start_head_title_example_wrap');
    },

    getStartHeadTitleExampleField: function() {
        return this.getStartHeadTitleExampleWrap().getComponent('start_head_title_example');
    },

    getStartHeadTitleExampleButton: function() {
        return this.getStartHeadTitleExampleWrap().getComponent('start_head_title_example_button');
    },

    /**
     * After the siteroot selection changes load the siteroot data.
     *
     * @param {Number} id
     * @param {String} title
     * @param {Object} data
     */
    loadData: function (id, title, data) {
        // remember current siteroot id
        this.siterootId = id;

        this.getComponent('titles').getForm().reset();
        this.getComponent('titles').getForm().setValues(data.titles);

        this.getComponent('custom_titles').getForm().reset();
        this.getComponent('custom_titles').getForm().setValues(data.customtitles);

        this.updateDefaultPreview();
        this.updateHomePreview();
    },

    isValid: function () {
        var valid = this.getComponent('titles').getForm().isValid() && this.getComponent(1).getForm().isValid();

        if (valid) {
            this.header.child('span').removeClass('error');
        } else {
            this.header.child('span').addClass('error');
        }

        return valid;
    },

    /**
     * Get the data to be saved.
     */
    getSaveData: function () {
        return {
            'titles': this.getTitlePanels().getForm().getValues(),
            'customtitles': {
                head_title: this.getHeadTitleField().getValue(),
                start_head_title: this.getStartHeadTitleField.getValue()
            }
        };
    },


    updateDefaultPreview: function () {
        var title = this.getHeadTitleField().getValue();
        if (!title) {
            this.getHeadTitleExampleField().reset();
            return;
        }

        this.getHeadTitleExampleButton().setIconCls('p-siteroot-loading-icon');

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_customtitle_example'),
            params: {
                siteroot_id: this.siterootId,
                head_title: title
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getHeadTitleExampleField().setValue(data.data.example);
                }

                this.getHeadTitleExampleButton().setIconCls(Phlexible.Icon.get(Phlexible.Icon.RELOAD));
            },
            scope: this
        });
    },

    updateHomePreview: function () {
        var title = this.getStartHeadTitleField().getValue();
        if (!title) {
            this.getStartHeadTitleExampleField().reset();
            return;
        }

        this.getStartHeadTitleExampleButton().setIconCls('p-siteroot-loading-icon');

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroots_customtitle_example'),
            params: {
                siteroot_id: this.siterootId,
                head_title: title
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    this.getStartHeadTitleExampleField().setValue(data.data.example);
                }

                this.getStartHeadTitleExampleButton().setIconCls(Phlexible.Icon.get(Phlexible.Icon.RELOAD));
            },
            scope: this
        });
    }
});
