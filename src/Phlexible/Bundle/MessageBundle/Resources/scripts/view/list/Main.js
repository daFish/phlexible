Ext.define('Phlexible.message.view.list.Main', {
    extend: 'Ext.panel.Panel',
    requires: [
        'Phlexible.message.view.list.Filter',
        'Phlexible.message.view.list.List'
    ],
    xtype: 'message.list.main',

    cls: 'p-message-list-main',
    iconCls: Phlexible.Icon.get('application-list'),
    layout: 'border',
    border: false,

    initComponent: function () {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'message.list.filter',
                itemId: 'filter',
                region: 'west',
                width: 250,
                padding: '5 0 5 5',
                collapsible: true,
                listeners: {
                    updateFilter: function (values) {
                        this.getComponent('list').setFilter(values);
                    },
                    scope: this
                }
            },
            {
                xtype: 'message.list.list',
                itemId: 'list',
                region: 'center',
                padding: 5,
                autoLoad: true,
                listeners: {
                    messages: function (p, data) {
                        this.getComponent('filter').updateFacets(data.facets);
                    },
                    scope: this
                }
            }
        ];
    }
});
