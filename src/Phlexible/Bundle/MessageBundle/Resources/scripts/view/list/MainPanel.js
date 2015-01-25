Ext.define('Phlexible.message.view.list.MainPanel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.message-list-main',

    cls: 'p-messages-view-main',
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
                xtype: 'message-list-filter',
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
                xtype: 'message-list-list',
                itemId: 'list',
                region: 'center',
                padding: 5,
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
