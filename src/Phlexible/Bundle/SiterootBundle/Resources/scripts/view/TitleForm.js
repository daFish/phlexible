Ext.define('Phlexible.siteroot.view.TitleForm', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.siteroot-titles',

    title: '_TitleForm',
    border: false,

    customTitlesText: '_customTitlesText',
    nameText: '_nameText',
    patternText: '_patternText',
    exampleText: '_exampleText',
    legendText: '_legendText',

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
                padding: 5,
                xlabelAlign: 'top',
                items: []
            },
            {
                xtype: 'grid',
                itemId: 'patterns',
                title: this.customTitlesText,
                padding: 5,
                store: Ext.create('Ext.data.Store', {
                    fields: ['name', 'pattern', 'example'],
                    data: [{name: 'bla', pattern: 'bla', example:'bla'}]
                }),
                columns: [{
                    header: this.nameText,
                    dataIndex: 'name',
                    width: 50,
                    editor: {
                        xtype: 'textfield',
                        allowBlank: false
                    }
                },{
                    header: this.patternText,
                    dataIndex: 'pattern',
                    width: 300,
                    editor: {
                        xtype: 'textfield',
                        allowBlank: false
                    }
                },{
                    header: this.exampleText,
                    dataIndex: 'example',
                    width: 300
                }],
                listeners: {
                    afteredit: function(e) {
                        if (e.column === 1 && e.value !== e.originalValue) {
                            this.updatePreview(e.record);
                        }
                    },
                    scope: this
                }
            },
            {
                xtype: 'panel',
                title: this.legendText,
                padding: 5,
                bodyPadding: 5,
                items: [
                    {
                        xtype: 'dataview',
                        store: Ext.create('Ext.data.Store', {
                            fields: ['placeholder', 'title'],
                            proxy: {
                                type: 'ajax',
                                url: Phlexible.Router.generate('siteroot_pattern_placeholders'),
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

        this.getComponent('patterns').getStore().removeAll();
        this.getComponent('patterns').getStore().loadData(data.patterns);
    },

    isValid: function () {
        var valid = this.getComponent(0).getForm().isValid();

        return {
            titles: this.getComponent(0).getForm().getValues(),
            patterns: patterns
        };
    },


    updatePreview: function (record) {
        var pattern = record.get('pattern');
        if (!pattern) {
            record.set('example');
            return;
        }

        Ext.Ajax.request({
            url: Phlexible.Router.generate('siteroot_pattern_example'),
            params: {
                siteroot_id: this.siterootId,
                pattern: pattern
            },
            success: function (response) {
                var data = Ext.decode(response.responseText);

                if (data.success) {
                    record.set('example', data.msg);
                }
            },
            scope: this
        });
    }
});
