/**
 * Columns window
 */
Ext.define('Phlexible.dashboard.window.ColumnsWindow', {
    extend: 'Ext.Window',
    requires: ['Phlexible.gui.model.KeyValueIconCls'],

    cls: 'p-dashboard-columns-window',
    width: 500,
    height: 150,
    modal: true,
    closable: false,
    layout: 'fit',
    bodyPadding: 15,

    col1Text: '_1_columnText',
    col2Text: '_2_columnsText',
    col3Text: '_3_columnsText',
    col4Text: '_4_columnsText',
    cancelText: '_cancelText',
    saveText: '_saveText',

    initComponent: function() {
        this.initMyItems();
        this.initDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = {
            xtype: 'dataview',
            store: Ext.create('Ext.data.Store', {
                model: 'Phlexible.gui.model.KeyValueIconCls',
                data: [
                    {key: '1', value: this.col1Text, iconCls: 'col-1.png'},
                    {key: '2', value: this.col2Text, iconCls: 'col-2.png'},
                    {key: 'bigleft', value: this.col2Text, iconCls: 'col-bigleft.png'},
                    {key: 'bigright', value: this.col2Text, iconCls: 'col-bigright.png'},
                    {key: '3', value: this.col3Text, iconCls: 'col-3.png'},
                ]
            }),
            tpl: new Ext.XTemplate(
                '<tpl for=".">',
                '<div class="col-wrap" id="{key}">',
                '<div class="col"><img src="/bundles/phlexibledashboard/cols/{iconCls}" title="{value}"></div>',
                //'<span>{value}</span>',
                '</div>',
                '</tpl>',
                '<div class="x-clear"></div>'
            ),
            autoHeight: true,
            singleSelect: true,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector:'div.col-wrap',
            listeners: {
                selectionchange: function(sm) {
                    var records = sm.getSelection(),
                        record;

                    if (records.length) {
                        record = records[0];
                        this.fireEvent('columnsChange', record.get('key'));
                    }
                },
                scope: this
            }
        };
    },

    initDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [{
                text: this.cancelText,
                handler: function() {
                    this.fireEvent('columnsChange', this.columns);
                    this.close()
                },
                scope: this
            },{
                text: this.saveText,
                handler: this.submit,
                scope: this
            }]
        }];
    },

    initMyListeners: function() {
        this.addListener({
            show: function(panel) {
                var record = panel.getComponent(0).getStore().findRecord('key', this.columns);

                if (!record) {
                    record = panel.getComponent(0).getStore().findRecord('key', '3');
                }

                if (record) {
                    panel.getComponent(0).select(record, false, true);
                }
            }
        });
    },

    submit: function() {
        var view = this.getComponent(0),
            records = view.getSelectionModel().getSelection(),
            user = Phlexible.App.getUser(),
            record;

        if (records.length) {
            record = records[0];

            user.setProperty('dashboard.columns', record.get('key'));
            user.commit();

            this.fireEvent('columnsChange', record.get('key'));
            this.close();
        }
    }
});
