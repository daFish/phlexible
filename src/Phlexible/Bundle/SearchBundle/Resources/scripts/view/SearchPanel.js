Ext.define('Phlexible.search.view.SearchPanel', {
    extend: 'Ext.panel.Panel',
    requires: ['Phlexible.search.model.Result'],

    xtype: 'searchpanel',

    title: '_SearchPanel',
    cls: 'p-searchpanel',
    iconCls: 'p-search-search-icon',
    layout: 'fit',

    initComponent: function () {
        this.initMyTasks();
        this.initMyItems();
        this.initMyDockedItems();

        this.callParent(arguments);
    },

    initMyTasks: function() {
        this.task = new Ext.util.DelayedTask(this.doSearch, this);
    },

    initMyItems: function() {
        this.items = [
            {
                xtype: 'dataview',
                tpl: new Ext.XTemplate(
                    '<tpl for=".">',
                    '<div class="search-item">',
                    '<div class="search-result-image">',
                    '<img src="{image}" alt="{title}" />',
                    '</div>',
                    '<div class="search-result-text">',
                    '<h3><span>{date:date("Y-m-d H:i:s"}<br />by {author}</span>{title}</h3>',
                    '{component}<br />&nbsp;',
                    '</div>',
                    '<div class="x-clear"">',
                    '</div>',
                    '</div>',
                    '</tpl>'
                ),
                store: new Ext.data.JsonStore({
                    url: Phlexible.Router.generate('search_search'),
                    root: 'results',
                    totalProperty: 'count',
                    //                id: 'id'
                    model: 'Phlexible.search.model.Result'
                }),
                autoHeight: true,
                multiSelect: false,
                overClass: 'x-view-over',
                itemSelector: 'div.search-item',

                pageSize: 8,
                minChars: 2,
                listeners: {
                    click: function (view, index) {
                        var record = view.store.getAt(index);
                        var menu = record.get('menu');

                        if (menu && menu.handler) {
                            var handler = menu.handler;
                            if (typeof handler == 'string') {
                                handler = Phlexible.evalClassString(handler);
                            }
                            handler(menu);
                        }

                        return false;
                    }
                }
            }
        ];
    },

    initMyDockedItems: function() {
        this.dockedItems = [{
            xtype: 'toolbar',
            dock: 'top',
            items: [{
                xtype: 'trigger',
                triggerClass: 'x-form-clear-trigger',
                enableKeyEvents: true,
                onTriggerClick: function () {
                    this.getTopToolbar().items.items[0].setValue('');
                    this.getComponent(0).store.baseParams.query = '';
                    this.getComponent(0).store.removeAll();
                }.createDelegate(this),
                listeners: {
                    keyup: function (field, event) {
                        if (event.getKey() == event.ENTER) {
                            this.task.cancel();
                            this.doSearch();
                            return;
                        }

                        this.task.delay(500);
                    },
                    scope: this
                }
            }]
        }];
    },

    doSearch: function () {
        var query = this.getTopToolbar().items.items[0].getValue();

        this.getComponent(0).store.baseParams.query = query;
        this.getComponent(0).store.load();
    }
});
