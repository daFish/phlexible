Ext.define('Phlexible.message.view.subscription.Main', {
    extend: 'Ext.panel.Panel',
    xtype: 'message.subscription.main',

    cls: 'p-message-subscription-main',
    iconCls: Phlexible.Icon.get('tick'),
    layout: 'border',
    border: false,

    emptyText: '_emptyText',
    actionsText: '_actionsText',
    idText: '_idText',
    filterText: '_filterText',
    handlerText: '_handlerText',
    deleteText: '_deleteText',
    subscribeText: '_subscribeText',

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'grid',
                region: 'center',
                loadMask: true,
                padding: 5,
                border: true,
                emptyText: this.emptyText,
                viewConfig: {
                    deferEmptyText: false
                },
                store: Ext.create('Ext.data.Store', {
                    model: 'Phlexible.message.model.Subscription',
                    proxy: {
                        type: 'ajax',
                        url: Phlexible.Router.generate('phlexible_message_get_subscriptions'),
                        simpleSortMode: true,
                        reader: {
                            type: 'json',
                            idProperty: 'id'
                        }
                    },
                    autoLoad: true
                }),
                columns: [
                    {
                        header: this.idText,
                        dataIndex: 'id',
                        width: 200,
                        hidden: true
                    },
                    {
                        header: this.filterText,
                        width: 200,
                        dataIndex: 'filter'
                    },
                    {
                        header: this.handlerText,
                        width: 200,
                        dataIndex: 'handler'
                    },
                    {
                        xtype: 'actioncolumn',
                        width: 40,
                        items: [
                            {
                                iconCls: Phlexible.Icon.get(Phlexible.Icon.DELETE),
                                tooltip: this.deleteText,
                                handler: this.deleteSubscription,
                                scope: this
                            }
                        ]
                    }
                ],
                dockedItems: [{
                    xtype: 'toolbar',
                    itemId: 'tbar',
                    dock: 'top',
                    items: [
                        {
                            xtype: 'combo',
                            itemId: 'filter',
                            emptyText: this.filterText,
                            store: Ext.create('Ext.data.Store', {
                                model: 'Phlexible.message.model.Filter',
                                proxy: {
                                    type: 'ajax',
                                    url: Phlexible.Router.generate('phlexible_message_get_filters'),
                                    reader: {
                                        type: 'json',
                                        idProperty: 'id'
                                    }
                                }
                            }),
                            displayField: 'title',
                            valueField: 'id',
                            mode: 'remote',
                            editable: false,
                            triggerAction: 'all'
                        },
                        ' ',
                        {
                            xtype: 'iconcombo',
                            itemId: 'handler',
                            emptyText: this.handlerText,
                            store: Ext.create('Ext.data.Store', {
                                fields: ['id', 'name', 'iconCls'],
                                data: Phlexible.message.Handlers
                            }),
                            displayField: 'name',
                            valueField: 'id',
                            iconClsField: 'iconCls',
                            mode: 'local',
                            editable: false,
                            triggerAction: 'all'
                        },
                        ' ',
                        {
                            xtype: 'button',
                            text: this.subscribeText,
                            iconCls: Phlexible.Icon.get('tick'),
                            handler: function () {
                                var filter = this.getDockedComponent('tbar').getComponent('filter').getValue(),
                                    handler = this.getDockedComponent('tbar').getComponent('handler').getValue();

                                Ext.Ajax.request({
                                    url: Phlexible.Router.generate('phlexible_message_post_subscriptions'),
                                    params: {
                                        filter: filter,
                                        handler: handler
                                    },
                                    success: function (response) {
                                        var result = Ext.decode(response.responseText);

                                        if (result.success) {
                                            this.getComponent(0).getStore().reload();
                                            Phlexible.Notify.success(result.msg);
                                        } else {
                                            Phlexible.Notify.failure(result.msg);
                                        }
                                    },
                                    scope: this
                                });
                            },
                            scope: this
                        }
                    ]
                }]
            }
        ];
    },

    reloadSubscriptions: function () {
        this.getComponent(0).getStore().reload();
    },

    deleteSubscription: function (grid, record) {
        Ext.Ajax.request({
            url: Phlexible.Router.generate('phlexible_message_delete_subscription', {subscriptionId: record.data.id}),
            success: function (response) {
                var result = Ext.decode(response.responseText);

                if (result.success) {
                    this.getComponent(0).getStore().reload();
                    Phlexible.Notify.success(result.msg);
                } else {
                    Phlexible.Notify.failure(result.msg);
                }
            },
            scope: this
        })
    }
});
