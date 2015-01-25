Ext.define('Phlexible.message.view.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.message-main',

    iconCls: Phlexible.Icon.get('resource-monitor'),
    layout: 'fit',

    initComponent: function () {
        this.initMyTabs();
        this.initMyItems();

        this.callParent(arguments);
    },
    initMyTabs: function() {
        this.tabs = [{
            xtype: 'message-list-main',
            itemId: 'view'
        }];

        if (Phlexible.App.isGranted('ROLE_MESSAGE_FILTERS')) {
            this.tabs.push({
                xtype: 'message-filter-main',
                itemId: 'filter',
                listeners: {
                    filterDeleted: function () {
                        if (Phlexible.App.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
                            this.getComponent(0).getComponent('subscriptions').reloadSubscriptions();
                        }
                    },
                    scope: this
                }
            });
        }
        if (Phlexible.App.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
            this.tabs.push({
                xtype: 'message-subscription-main',
                itemId: 'subscription'
            });
        }
    },

    initMyItems: function() {
        this.items = {
            xtype: 'tabpanel',
            deferredRender: true,
            activeItem: 0,
            border: false,
            items: this.tabs
        };

        delete this.tabs;
    }
});
