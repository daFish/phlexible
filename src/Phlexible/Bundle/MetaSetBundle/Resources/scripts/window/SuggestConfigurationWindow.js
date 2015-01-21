Ext.define('Phlexible.metaset.window.SuggestConfigurationWindow', {
    extend: 'Ext.window.Window',

    title: '_SuggestConfigurationWindow',
    width: 300,
    height: 400,
    layout: 'fit',
    modal: true,

    datasourceText: '_datasourceText',
    createDatasourceText: '_createDatasourceText',
    createDatasourceDescriptionText: '_createDatasourceDescriptionText',
    cancelText: '_cancelText',
    selectText: '_selectText',

    initComponent: function() {
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'grid',
            border: false,
            store: Ext.create('Ext.data.Store', {
                fields: ['id', 'title'],
                proxy: {
                    type: 'ajax',
                    url: Phlexible.Router.generate('datasources_list'),
                    simpleSortMode: true,
                    reader: {
                        type: 'json',
                        rootProperty: 'datasources',
                        idProperty: 'id',
                        totalProperty: 'count'
                    },
                    extraParams: this.storeExtraParams
                },
                autoLoad: true,
                listeners: {
                    load: function (store, records) {
                        if (!this.options) {
                            return;
                        }

                        var row = store.find('id', this.options);

                        if (row === -1) {
                            return;
                        }

                        this.getComponent(0).getSelectionModel().selectRow(row);
                    },
                    scope: this
                }
            }),
            columns: [{
                header: this.datasourceText,
                dataIndex: 'title',
                flex: 1
            }],
            dockedItems: [{
                xtype: 'toolbar',
                dock: 'top',
                items: [{
                    text: this.createDatasourceText,
                    iconCls: Phlexible.Icon.get(Phlexible.Icon.ADD),
                    handler: function () {
                        Ext.MessageBox.prompt(this.createDatasourceText, this.createDatasourceDescriptionText, function (btn, title) {
                            if (btn !== 'ok') {
                                return;
                            }
                            Ext.Ajax.request({
                                url: Phlexible.Router.generate('datasources_create'),
                                params: {
                                    title: title
                                },
                                success: function (response) {
                                    var result = Ext.decode(response.responseText);

                                    if (result.success) {
                                        this.getComponent(0).getStore().reload();
                                    } else {
                                        Phlexible.failure(result.msg);
                                    }
                                },
                                scope: this
                            })

                        }, this);
                    },
                    scope: this
                }]
            }]
        }];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'bottom',
            ui: 'footer',
            items: [
                '->',
            {
                text: this.cancelText,
                handler: this.close,
                scope: this
            },{
                text: this.selectText,
                handler: function() {
                    var options = this.getComponent(0).getSelectionModel().getSelected().get('id');
                    this.fireEvent('select', options);
                    this.close();
                },
                scope: this
            }]
        }];
    }
});