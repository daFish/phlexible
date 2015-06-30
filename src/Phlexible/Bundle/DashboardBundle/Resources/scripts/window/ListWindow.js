/**
 * List window
 */
Ext.define('Phlexible.dashboard.window.ListWindow', {
    extend: 'Ext.Window',

    width: 600,
    height: 400,
    layout: 'fit',
    modal: true,
    constrainHeader: true,
    cls: 'p-dashboard-list-window',

    noAvailablePortletsText: '_noAvailablePortletsText',

    /**
     * @event portletOpen
     */

    /**
     *
     */
    initComponent: function() {
        this.initMyItems();

        this.callParent(arguments);
    },

    initMyItems: function() {
        this.items = [{
            xtype: 'panel',
            itemId: 'portletPanel',
            border: false,
            autoScroll: true,
            layout: 'fit',
            items: [{
                xtype: 'dataview',
                padding: '5 25 5 5;',
                store: Phlexible.dashboard.Portlets,
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                        '<div class="list-item list-wrap" id="{title}">',
                        '<div class="image"><img src="{image}" width="120" height="50" /></div>',
                        '<div class="text">',
                            '<div class="title"><tpl if="iconCls">{[Phlexible.Icon.inline(values.iconCls)]} </tpl>{title}</div>',
                            '<div class="description">{description}</div>',
                        '</div>',
                        '<div class="x-clear x-clear-left"></div>',
                        '</div>',
                    '</tpl>'
                ),
                emptyText: this.noAvailablePortletsText,
                deferEmptyText: false,
                itemSelector: 'div.list-wrap',
                overItemCls: 'list-item-over',
                trackOver: true,
                singleSelect: true,
                listeners: {
                    itemclick: function(view, record){
                        var item = Ext.clone(record.data),
                            store = this.getComponent(0).getComponent(0).getStore();
                        record.set('hidden', true);
                        store.clearFilter();
                        store.filterBy(function(record) {
                            if (!record.get('hidden')) {
                                return true;
                            }
                        });
                        this.fireEvent('portletOpen', item);
                    },
                    scope: this
                }
            }]
        }];
    }
});
