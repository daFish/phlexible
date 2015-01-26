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
                store: Ext.data.StoreManager.lookup('dashboard-available'),
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                        '<tpl if="hidden">',
                            '<div class="list-item list-wrap" style="display:none;" id="{title}">',
                        '<tpl else>',
                            '<div class="list-item list-wrap" id="{title}">',
                        '</tpl>',
                        '<div class="image"><img src="{imageUrl}" width="120" height="50" /></div>',
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
                        var item = Ext.clone(record.data);
                        record.set('hidden', true);
                        this.fireEvent('portletOpen', item);
                    },
                    scope: this
                }
            }]
        }];
    }
});