Ext.define('Phlexible.message.view.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.message.view.MainController',
        'Phlexible.message.view.list.Main',
        'Phlexible.message.view.filter.Main',
        'Phlexible.message.view.subscription.Main'
    ],

    xtype: 'message.main',

    controller: 'message.main',

    componentCls: 'p-message-main',
    iconCls: Phlexible.Icon.get('resource-monitor'),
    layout: 'fit',
    border: false,

    initComponent: function () {
        this.initMyTabs();
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyTabs: function() {
        this.tabs = [{
            xtype: 'message.list.main',
            itemId: 'view'
        }];

        if (Phlexible.User.isGranted('ROLE_MESSAGE_FILTERS')) {
            this.tabs.push({
                xtype: 'message.filter.main',
                itemId: 'filter',
                listeners: {
                    filterDeleted: 'onDeleteFilter'
                }
            });
        }
        if (Phlexible.User.isGranted('ROLE_MESSAGE_SUBSCRIPTIONS')) {
            this.tabs.push({
                xtype: 'message.subscription.main',
                itemId: 'subscription'
            });
        }
    },

    initMyItems: function() {
        this.items = {
            xtype: 'tabpanel',
            deferredRender: false,
            activeItem: 0,
            border: false,
            items: this.tabs
        };

        delete this.tabs;
    }
});
